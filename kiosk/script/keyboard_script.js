// keyboard_script.js
const Keyboard = {
  elements: {
    main: null,
    keysContainer: null,
    keys: [],
    capsKey: null,
  },

  properties: {
    value: "",
    capsLock: false,
    keyboardInputs: null,
    currentInput: null,
    keyLayout: [
      "1","2","3","4","5","6","7","8","9","0","backspace",
      "q","w","e","r","t","y","u","i","o","p",
      "caps","a","s","d","f","g","h","j","k","l","enter",
      "done","z","x","c","v","b","n","m",",",".","?","space",
    ],
  },

  init() {
    // Create main container
    this.elements.main = document.createElement("div");
    this.elements.main.classList.add("keyboard", "keyboard--hidden");
    document.body.appendChild(this.elements.main);

    // Create keys container
    this.elements.keysContainer = document.createElement("div");
    this.elements.keysContainer.classList.add("keyboard__keys");
    this.elements.main.appendChild(this.elements.keysContainer);

    // Generate keys
    this.elements.keysContainer.appendChild(this._createKeys());
    this.elements.keys =
      this.elements.keysContainer.querySelectorAll(".keyboard__key");

    // Attach to inputs
    this.properties.keyboardInputs =
      document.querySelectorAll(".use-keyboard-input");

    this.properties.keyboardInputs.forEach((element) => {
      element.addEventListener("focus", () => {
        this.properties.currentInput = element;
        this.open(element.value);
      });
    });

    // 👇 Hide keyboard when clicking outside
    document.addEventListener("click", (event) => {
      const isKeyboard = this.elements.main.contains(event.target);
      const isInput = event.target.classList.contains("use-keyboard-input");

      if (!isKeyboard && !isInput) {
        this.close();
      }
    });
  },

  _createIconHTML(iconClass) {
    if (!iconClass) return "";
    return `<span class="${iconClass}"></span>`;
  },

  _createKeyBtn(iconClass, class1, onclick, class2) {
    this.keyElement = document.createElement("button");

    this.keyElement.setAttribute("type", "button");
    this.keyElement.classList.add("keyboard__key");

    if (class1) this.keyElement.classList.add(class1);
    if (class2) this.keyElement.classList.add(class2);

    if (iconClass) {
      this.keyElement.innerHTML = this._createIconHTML(iconClass);
    }

    if (onclick) {
      this.keyElement.addEventListener("click", onclick);
    }
  },

  _createKeys() {
    const fragment = document.createDocumentFragment();

    this.properties.keyLayout.forEach((key) => {
      const insertLineBreak =
        ["backspace", "p", "enter", "?"].includes(key);

      switch (key) {
        case "backspace":
          this._createKeyBtn(
            "backspace",
            "keyboard__key--wide",
            () => {
              this.properties.value =
                this.properties.value.slice(0, -1);
              this._updateValueInTarget();
            }
          );
          break;

        case "caps":
          this._createKeyBtn(
            "keyboard_capslock",
            "keyboard__key--activatable",
            () => {
              this.elements.capsKey.classList.toggle(
                "keyboard__key--active"
              );
              this._toggleCapsLock();
            },
            "keyboard__key--wide"
          );
          this.elements.capsKey = this.keyElement;
          break;

        case "enter":
          this._createKeyBtn(
            "keyboard_return",
            "keyboard__key--wide",
            () => {
              this.properties.value += "\n";
              this._updateValueInTarget();
            }
          );
          break;

        case "space":
          this._createKeyBtn(
            null,
            "keyboard__key--extra--wide",
            () => {
              this.properties.value += " ";
              this._updateValueInTarget();
            }
          );
          this.keyElement.textContent = "Space";
          break;

        case "done":
          this._createKeyBtn(
            "check_circle",
            "keyboard__key--dark",
            () => {
              this.close();
            },
            "keyboard__key--wide"
          );
          break;

        default:
          this._createKeyBtn(null);

          this.keyElement.textContent = key.toLowerCase();

          this.keyElement.addEventListener("click", () => {
            this.properties.value += this.properties.capsLock
              ? key.toUpperCase()
              : key.toLowerCase();
            this._updateValueInTarget();
          });
          break;
      }

      fragment.appendChild(this.keyElement);

      if (insertLineBreak) {
        fragment.appendChild(document.createElement("br"));
      }
    });

    return fragment;
  },

  _updateValueInTarget() {
    if (this.properties.currentInput) {
      this.properties.currentInput.value =
        this.properties.value;
    }
  },

  _toggleCapsLock() {
    this.properties.capsLock = !this.properties.capsLock;

    this.elements.keys.forEach((key) => {
      if (key.childElementCount === 0) {
        key.textContent = this.properties.capsLock
          ? key.textContent.toUpperCase()
          : key.textContent.toLowerCase();
      }
    });
  },

  open(initialValue) {
    this.properties.value = initialValue || "";
    this.elements.main.classList.remove("keyboard--hidden");
  },

  close() {
    this.elements.main.classList.add("keyboard--hidden");
    this.properties.currentInput = null;
  },
};

window.addEventListener("DOMContentLoaded", () => {
  Keyboard.init();
});