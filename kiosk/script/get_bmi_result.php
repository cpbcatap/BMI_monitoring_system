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

      items.forEach(item => {
        $list.append(`<li>${item}</li>`);
      });
    }

    function renderText(selector, text, fallback = '--') {
      const val = (typeof text === 'string' && text.trim() !== '') ? text : fallback;
      $(selector).text(val);
    }


    // 1) Get BMI from API
    $.ajax({
      url: '../api/get_bmi_result.php',
      method: 'GET',
      dataType: 'json',
      success: function(response) {
        console.log('API Response:', response);

        if (!(response.ok && response.data && response.data.length > 0)) {
          alert('No BMI result found for the user.');
          return;
        }

        const record = response.data[0];
        const bmi = parseFloat(record.BMI);

        if (isNaN(bmi)) {
          alert('BMI value is invalid.');
          return;
        }

        $('.bmi-value').text(bmi.toFixed(1));

        // 2) Load JSON rules
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
              renderFooterReminders(json.important_reminders);
              return;
            }

            // LEFT section
            $('.bmi-category').text(match.name);
            renderList('.advice-list', match.dietitian_advice, 'No advice available.');

            // RIGHT section
            renderList('.food-list', match.food_examples, 'No food examples available.');
            renderText('.nutrition-goal-text', match.nutrition_goal, '--');
            renderText('.nutrition-range-text', 'BMI Range: ' + (match.nutrition_range || '--'), '--');

          },
          error: function(xhr) {
            console.error('JSON load error:', xhr.status, xhr.responseText);
            alert('An error occurred while loading BMI categories JSON.');
          }
        });

      },
      error: function(xhr) {
        console.error('BMI API error:', xhr.status, xhr.responseText);
        alert('An error occurred while fetching the BMI result.');
      }
    });

  });
</script>