jQuery(document).ready(function ($) {
  $(".nav-tab").on("click", function (e) {
    e.preventDefault();
    $(".nav-tab").removeClass("nav-tab-active");
    $(this).addClass("nav-tab-active");
    $(".tab-content").hide();
    $($(this).attr("href")).show();
  });
  $(".nav-tab").first().click();
  $(".add-field").on("click", function () {
    var category = $(this).data("category");
    var count = $("#theme-demo-fields-" + category + " tr").length / 4;
    var newField =
      "<tr>" +
      '<th scope="row"><label for="theme_demo_image_' +
      category +
      "_" +
      count +
      '">Image URL ' +
      (count + 1) +
      "</label></th>" +
      '<td><input type="text" name="theme_demo_shortcode_fields_' +
      category +
      "[" +
      count +
      '][image]" /></td>' +
      //'<td><button type="button" class="remove-field">Remove</button></td>' +
      //'<td><button type="button" class="remove-field">Remove</button></td>' +
      "</tr><tr>" +
      '<th scope="row"><label for="theme_demo_link_' +
      category +
      "_" +
      count +
      '">Demo Link ' +
      (count + 1) +
      "</label></th>" +
      '<td><input type="text" name="theme_demo_shortcode_fields_' +
      category +
      "[" +
      count +
      '][link]" /></td>' +
      "</tr><tr>" +
      '<th scope="row"><label for="theme_demo_button_id_' +
      category +
      "_" +
      count +
      '">Button ID ' +
      (count + 1) +
      "</label></th>" +
      '<td><input type="text" name="theme_demo_shortcode_fields_' +
      category +
      "[" +
      count +
      '][button_id]" /></td>' +
      "</tr><tr>" +
      '<td colspan="3"><hr></td>' +
      "</tr>";
    $("#theme-demo-fields-" + category).append(newField);
  });

  // Remove field
  $(document).on("click", ".remove-field", function () {
    var $fieldSet = $(this)
      .closest(".tab-content")
      .find("tr")
      .last()
      .prevAll("tr")
      .slice(0, 4);
    if ($fieldSet.length > 0) {
      $fieldSet.remove();
    }
  });
});
