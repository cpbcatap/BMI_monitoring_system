<script>
  $(document).ready(function() {
    const CATEGORIES_JSON_URL = '../assets/json/bmi_result.json';

    function findCategoryByBmi(bmi, categories) {
      return categories.find(c => bmi >= Number(c.min) && bmi <= Number(c.max)) || null;
    }

    function renderList(selector, items, emptyText = 'No data available.') {
      const $list = $(selector);
      $list.empty();
      if (!Array.isArray(items) || items.length === 0) {
        $list.append(`<li>${emptyText}</li>`);
        return;
      }
      items.forEach(item => $list.append(`<li>${item}</li>`));
    }

    function renderText(selector, text, fallback = '--') {
      const val = (typeof text === 'string' && text.trim() !== '') ? text : fallback;
      $(selector).text(val);
    }

    function toNum(v) {
      const n = typeof v === 'string' ? parseFloat(v) : Number(v);
      return Number.isFinite(n) ? n : NaN;
    }

    // 1) Load scan result from sessionStorage
    let saved = null;
    try {
      saved = JSON.parse(sessionStorage.getItem("bmi_last_result") || "null");
    } catch (e) {}

    console.log("Saved from sessionStorage:", saved);

    if (!saved || saved.ok !== true || saved.cmd !== "result") {
      alert("No scanned BMI result found. Please scan again.");
      window.location.href = "bmi_calculator.php";
      return;
    }

    // Optional: ensure it's for this logged-in user (prevents cross-user mixups)
    const currentUserId = "<?php echo $_SESSION['user_id']; ?>";
    if (String(saved.user_id) !== String(currentUserId)) {
      alert("Result does not match current user. Please scan again.");
      sessionStorage.removeItem("bmi_last_result");
      window.location.href = "bmi_calculator.php";
      return;
    }

    const bmi = toNum(saved.bmi);
    if (!Number.isFinite(bmi)) {
      alert("BMI value is invalid. Please scan again.");
      sessionStorage.removeItem("bmi_last_result");
      window.location.href = "bmi_calculator.php";
      return;
    }

    // Fill main BMI UI
    $('.bmi-value').text(bmi.toFixed(1));

    // 2) Load JSON rules and render advice/food/goals based on BMI
    $.ajax({
      url: CATEGORIES_JSON_URL,
      method: 'GET',
      dataType: 'json',
      success: function(json) {
        const categories = json.categories || [];
        const match = findCategoryByBmi(bmi, categories);

        if (!match) {
          $('.bmi-category').text('Unknown');
          renderList('.advice-list', [], 'No advice available.');
          renderList('.food-list', [], 'No food examples available.');
          renderText('.nutrition-goal-text', '--');
          renderText('.nutrition-range-text', '--');
          return;
        }

        $('.bmi-category').text(match.name);
        renderList('.advice-list', match.dietitian_advice, 'No advice available.');
        renderList('.food-list', match.food_examples, 'No food examples available.');
        renderText('.nutrition-goal-text', match.nutrition_goal, '--');
        renderText('.nutrition-range-text', 'BMI Range: ' + (match.nutrition_range || '--'), '--');
      },
      error: function(xhr) {
        console.error('JSON load error:', xhr.status, xhr.responseText);
        alert('An error occurred while loading BMI categories JSON.');
      }
    });
  });
</script>