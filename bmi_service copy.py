#!/usr/bin/env python3
import asyncio
import json
import time
import traceback
from typing import Optional, Dict, Any

import serial
import websockets

# =========================
# CONFIG
# =========================
PRINTER_DEVICE = "/dev/usb/lp0"

SERIAL_PORT = "/dev/ttyAMA0"
SERIAL_BAUD = 115200

WS_HOST = "0.0.0.0"
WS_PORT = 8765


# Printer helper (ESC/POS)
def escpos_receipt_bytes(payload: Dict[str, Any]) -> bytes:
    # Basic ESC/POS formatting (works on most 58/80mm thermal printers)
    def line(s=""):
        return (s + "\n").encode("utf-8", errors="replace")

    height = payload.get("height_cm", "")
    weight = payload.get("weight_kg", "")
    bmi = payload.get("bmi", "")
    cls = payload.get("classification", "")
    user_id = payload.get("user_id", "")
    ts = payload.get("ts", None)

    if ts:
        try:
            dt = time.strftime("%Y-%m-%d %H:%M:%S", time.localtime(ts / 1000))
        except Exception:
            dt = ""
    else:
        dt = time.strftime("%Y-%m-%d %H:%M:%S")

    b = bytearray()

    # init
    b += b"\x1b@"          # ESC @
    b += b"\x1b\x61\x01"   # center
    b += b"\x1d\x21\x11"   # double size
    b += line("BMI RESULT")
    b += b"\x1d\x21\x00"   # normal
    b += line("BMI Monitoring System")
    b += line("--------------------------------")

    b += b"\x1b\x61\x00"   # left
    if user_id:
        b += line(f"User ID: {user_id}")
    b += line(f"Date: {dt}")
    b += line("")
    b += line(f"Height (cm): {height}")
    b += line(f"Weight (kg): {weight}")
    b += line(f"BMI:         {bmi}")
    b += line(f"Class:       {cls}")
    b += line("")
    b += line("--------------------------------")
    b += b"\x1b\x61\x01"   # center
    b += line("Thank you!")
    b += line("")
    b += line("")

    # cut (some printers ignore; safe)
    b += b"\x1d\x56\x00"   # GS V 0 (full cut)
    return bytes(b)

def print_to_usb_printer(payload: Dict[str, Any]) -> None:
    data = escpos_receipt_bytes(payload)
    with open(PRINTER_DEVICE, "wb") as f:
        f.write(data)
        f.flush()


# =========================
# BMI helpers
# =========================
def compute_bmi(height_cm: float, weight_kg: float) -> float:
    h_m = height_cm / 100.0
    if h_m <= 0:
        return 0.0
    return weight_kg / (h_m * h_m)

# =========================
# Serial utils
# =========================
def open_serial() -> serial.Serial:
    return serial.Serial(
        port=SERIAL_PORT,
        baudrate=SERIAL_BAUD,
        bytesize=serial.EIGHTBITS,
        parity=serial.PARITY_NONE,
        stopbits=serial.STOPBITS_ONE,
        timeout=0.2,          # short timeout so we can poll in asyncio loop
        write_timeout=1.0,
    )

def send_serial_json(ser: serial.Serial, obj: Dict[str, Any]) -> None:
    line = (json.dumps(obj, separators=(",", ":")) + "\n").encode("utf-8")
    ser.write(line)
    ser.flush()

def try_parse_json_line(line: str) -> Optional[Dict[str, Any]]:
    line = line.strip()
    if not line:
        return None
    if not (line.startswith("{") and line.endswith("}")):
        return None
    try:
        return json.loads(line)
    except json.JSONDecodeError:
        return None

# =========================
# Global state
# =========================
P_clients = set()                 # connected website clients
P_active_user_id: Optional[str] = None
P_ser: Optional[serial.Serial] = None

async def broadcast(obj: Dict[str, Any]) -> None:
    if not P_clients:
        return
    msg = json.dumps(obj)
    dead = []
    for ws in list(P_clients):
        try:
            await ws.send(msg)
        except Exception:
            dead.append(ws)
    for ws in dead:
        P_clients.discard(ws)

# =========================
# Serial reader task
# =========================
async def serial_reader_task():
    global P_ser, P_active_user_id

    while True:
        try:
            if P_ser is None:
                P_ser = open_serial()
                print(f"[OK] Serial connected {SERIAL_PORT} @ {SERIAL_BAUD}")

            raw = P_ser.readline()
            if not raw:
                await asyncio.sleep(0.01)
                continue

            line = raw.decode("utf-8", errors="replace").strip()
            msg = try_parse_json_line(line)

            if msg is None:
                # ESP32 debug text
                print("[ESP32]", line)
                continue

            # Expected from ESP32:
            # {"type":"scan_result","user_id":"123","height_cm":170.2,"weight_kg":65.8}
            if msg.get("type") != "scan_result":
                print("[SERIAL JSON]", msg)
                continue

            user_id = str(msg.get("user_id", "")).strip()
            try:
                height_cm = float(msg.get("height_cm", 0))
                weight_kg = float(msg.get("weight_kg", 0))
            except Exception:
                await broadcast({"ok": False, "cmd": "error", "error": "bad_numbers", "raw": msg})
                continue

            # sanity range check
            if not (50.0 <= height_cm <= 250.0) or not (5.0 <= weight_kg <= 400.0):
                await broadcast({
                    "ok": False, "cmd": "error", "error": "out_of_range",
                    "user_id": user_id, "height_cm": height_cm, "weight_kg": weight_kg
                })
                continue

            # Only accept result if it matches active user_id (kiosk logic)
            if P_active_user_id is None or user_id != P_active_user_id:
                await broadcast({
                    "ok": False, "cmd": "error", "error": "job_mismatch",
                    "active_user_id": P_active_user_id, "user_id": user_id
                })
                continue

            bmi_val = compute_bmi(height_cm, weight_kg)

            result = {
                "ok": True,
                "cmd": "result",
                "user_id": user_id,
                "height_cm": round(height_cm, 1),
                "weight_kg": round(weight_kg, 1),
                "bmi": round(bmi_val, 1),
            }

            print("[RESULT]", result)
            await broadcast(result)

            # clear active job after result
            P_active_user_id = None

        except Exception as e:
            print("[ERR] Serial reader:", e)
            print(traceback.format_exc())
            try:
                if P_ser:
                    P_ser.close()
            except Exception:
                pass
            P_ser = None
            await asyncio.sleep(1.0)

# =========================
# WebSocket handler
# =========================
async def ws_handler(ws):
    global P_active_user_id, P_ser

    P_clients.add(ws)
    await ws.send(json.dumps({"ok": True, "status": "connected"}))

    try:
        async for msg in ws:
            try:
                req = json.loads(msg)
            except Exception:
                await ws.send(json.dumps({"ok": False, "error": "bad_json"}))
                continue

            cmd = req.get("cmd")
            if cmd == "start":
                user_id = str(req.get("user_id", "")).strip()
                if user_id == "":
                    await ws.send(json.dumps({"ok": False, "error": "missing_user_id"}))
                    continue

                if P_active_user_id is not None:
                    await ws.send(json.dumps({
                        "ok": False,
                        "error": "busy",
                        "active_user_id": P_active_user_id
                    }))
                    continue

                P_active_user_id = user_id
                await broadcast({"ok": True, "cmd": "started", "user_id": user_id})

                # Ensure serial open, then tell ESP32 to start scanning
                try:
                    if P_ser is None:
                        P_ser = open_serial()
                        print(f"[OK] Serial connected {SERIAL_PORT} @ {SERIAL_BAUD}")

                    send_serial_json(P_ser, {"cmd": "start_scan", "user_id": user_id})
                except Exception as e:
                    P_active_user_id = None
                    await broadcast({"ok": False, "cmd": "error", "error": f"serial_send_failed: {e}"})

            elif cmd == "cancel":
                if P_active_user_id is None:
                    await ws.send(json.dumps({"ok": False, "error": "no_active_job"}))
                    continue

                canceled = P_active_user_id
                P_active_user_id = None

                # optional tell esp32
                try:
                    if P_ser is None:
                        P_ser = open_serial()
                    send_serial_json(P_ser, {"cmd": "cancel_scan", "user_id": canceled})
                except Exception:
                    pass

                await broadcast({"ok": True, "cmd": "canceled", "user_id": canceled})

            elif cmd == "print_bmi":
                # Print receipt via USB printer
                try:
                    # you can validate required fields here if you want
                    payload = {
                        "user_id": str(req.get("user_id", "")).strip(),
                        "height_cm": req.get("height_cm", ""),
                        "weight_kg": req.get("weight_kg", ""),
                        "bmi": req.get("bmi", ""),
                        "classification": str(req.get("classification", "")).strip(),
                        "ts": req.get("ts", int(time.time() * 1000)),
                    }

                    # run blocking printer write in a thread so asyncio doesn't freeze
                    await asyncio.to_thread(print_to_usb_printer, payload)

                    await ws.send(json.dumps({"ok": True, "cmd": "print_ok"}))
                except Exception as e:
                    await ws.send(json.dumps({"ok": False, "cmd": "print_error", "error": str(e)}))

            elif cmd == "ping":
                await ws.send(json.dumps({"ok": True, "cmd": "pong"}))

            else:
                await ws.send(json.dumps({"ok": False, "error": "unknown_cmd"}))

    finally:
        P_clients.discard(ws)

async def main():
    # start serial reader
    asyncio.create_task(serial_reader_task())

    # start ws server
    async with websockets.serve(ws_handler, WS_HOST, WS_PORT):
        print(f"[OK] WS server listening on ws://{WS_HOST}:{WS_PORT}")
        await asyncio.Future()

if __name__ == "__main__":
    asyncio.run(main())
