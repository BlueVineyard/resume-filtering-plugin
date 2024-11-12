jQuery(document).ready(function ($) {
  var categoriesShown = 5;

  function fetchFilteredResumes(page = 1) {
    var form = $("#resume-filter-form");
    $.ajax({
      url: rfp_ajax.ajax_url,
      type: "post",
      data: form.serialize() + "&action=fetch_filtered_resumes&page=" + page,
      success: function (response) {
        if (response.success) {
          $("#resume-results").html(response.data.resume_results);
          $("#total-results").text(
            response.data.total_resumes + " Resume results"
          );
          setMinHeight(); // Call the function to set the min height
        }
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error: " + textStatus + ", " + errorThrown);
      },
    });
  }

  function setMinHeight() {
    var resumeCards = $(".ae_resume_card");
    var combinedHeight = 0;
    for (var i = 0; i < 7 && i < resumeCards.length; i++) {
      combinedHeight += $(resumeCards[i]).outerHeight();
    }
    $("#ae_resume_filter_wrapper").css("min-height", combinedHeight + "px");
  }

  // Function to initialize dropdowns
  function initDropdown(dropdown, labelSelector, inputSelector) {
    const label = dropdown.find(labelSelector);
    const options = dropdown.find(".dropdown__select-option");

    label.on("click", function () {
      dropdown.toggleClass("open");
    });

    options.each(function () {
      $(this).on("click", function () {
        label.text($(this).text());
        $(inputSelector).val($(this).data("value"));
        fetchFilteredResumes(); // Fetch resumes immediately after selecting an option
        dropdown.removeClass("open");
      });
    });
  }

  // Initialize dropdowns for Date Post and Location filters
  const dateDropdown = $(".dropdown").first();
  const locationDropdown = $(".dropdown").last();

  initDropdown(dateDropdown, ".dropdown__filter-selected span", "#date_post");
  initDropdown(
    locationDropdown,
    ".dropdown__filter-selected span",
    "#resume_location"
  );

  /**
   * Custom dropdown for Homepage Location filter
   */
  $(document).on("click", function (e) {
    if (!$(e.target).closest(".dropdown").length) {
      dateDropdown.removeClass("open");
      locationDropdown.removeClass("open");
    }
  });

  // Fetch resumes on form input changes
  $("#resume-filter-form").on(
    "input change",
    "input, select",
    fetchFilteredResumes
  );

  $("#toggle-more-categories").on("click", function () {
    var categories = $(
      '#resume-filter-form input[name="resume_category[]"]'
    ).parent();
    categories.slice(categoriesShown, categoriesShown + 5).show();
    categoriesShown += 5;

    if (categoriesShown >= categories.length) {
      $(this).hide();
    }
  });

  // Initially hide categories beyond the first 5
  $('#resume-filter-form input[name="resume_category[]"]')
    .parent()
    .slice(categoriesShown)
    .hide();

  /**
   * Apply Filters Button
   */
  $("#apply-filters").on("click", function () {
    fetchFilteredResumes();
  });

  /**
   * Reset Filters Button
   */
  $("#reset-filters").on("click", function () {
    var formElement = $("#resume-filter-form")[0];

    if (formElement) {
      formElement.reset(); // Reset the form fields if form element exists

      dateDropdown
        .find(".dropdown__filter-selected span")
        .text(dateDropdown.find(".dropdown__select-option").first().text());
      $("#date_post").val(
        dateDropdown.find(".dropdown__select-option").first().data("value")
      );

      locationDropdown
        .find(".dropdown__filter-selected span")
        .text(locationDropdown.find(".dropdown__select-option").first().text());
      $("#resume_location").val(
        locationDropdown.find(".dropdown__select-option").first().data("value")
      );

      $('input[name="search_query"]').val(""); // Reset the search input field

      fetchFilteredResumes();
    } else {
      console.log("Error: resume-filter-form not found.");
    }
  });

  /**
   * Handle pagination clicks
   */
  $(document).on("click", ".pagination a", function (e) {
    e.preventDefault();
    var page = $(this).attr("href").split("page=")[1];
    fetchFilteredResumes(page);
  });

  /**
   * Initial fetch to load the first set of resumes
   */
  fetchFilteredResumes();
});

/**
 * Bookmark Script
 */
jQuery(document).ready(function ($) {
  // Show tooltip on hover
  $(document).on("mouseenter", "[data-tooltip]", function () {
    var $this = $(this);
    var tooltipText = $this.attr("data-tooltip");

    // Create and append the tooltip to the hovered element
    var tooltip = $('<span class="tooltip"></span>').text(tooltipText);
    $this.append(tooltip);

    // Position the tooltip
    tooltip
      .css({
        top: $this.height() + 10 + "px", // 10px below the element
        left: "50%",
        transform: "translateX(-50%)",
        position: "absolute",
      })
      .fadeIn();
  });

  // Hide tooltip on mouse leave
  $(document).on("mouseleave", "[data-tooltip]", function () {
    $(this).find(".tooltip").remove();
  });

  // Using event delegation to bind events to dynamically loaded elements

  // Show the bookmark details when "add-bookmark" is clicked
  $(document).on("click", ".add-bookmark", function (e) {
    e.preventDefault();
    $(this).siblings(".bookmark-details").fadeIn();
  });

  // Close the bookmark details popup on clicking outside of it
  $(document).mouseup(function (e) {
    var container = $(".bookmark-details");

    if (!container.is(e.target) && container.has(e.target).length === 0) {
      container.fadeOut();
    }
  });

  // Reinitialize visibility settings when new content is loaded via AJAX
  $(document).on("ajaxComplete", function () {
    // Initially hide the bookmark details
    $(".bookmark-details").hide();
  });
});
