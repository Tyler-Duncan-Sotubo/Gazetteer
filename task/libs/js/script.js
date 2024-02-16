$(window).on("load", function () {
  if ($("#preloader").length) {
    $("#preloader")
      .delay(1000)
      .fadeOut("slow", function () {
        $(this).remove();
      });
  }
});

$(document).ready(function () {
  // Create a spinner element
  const $spinner = $('<div class="spinner"></div>');

  // Wikipedia Search
  $("#wikipedia").click(function () {
    // Check if the search query is empty
    if ($("#query").val() === "") {
      alert("Please enter a search query");
      $spinner.remove();
      return;
    }

    // Append loading spinner on Click
    $("#output").append($spinner);

    $.ajax({
      url: "libs/php/getWikipediaInfo.php",
      type: "POST",
      dataType: "json",
      data: {
        q: $("#query").val(),
      },
      success: function (result) {
        $("#output").empty(); // Clear the output div
        $spinner.remove(); // Remove the spinner on success

        // Loop through the result and create a div element for each object
        $.each(result["data"], function (index, object) {
          // Create a div element to represent each object
          const $mainDiv = $('<div class="resultContainer"></div>');
          const $rightDiv = $('<div class="right"></div>');
          const $leftDiv = $('<div class="left"></div>');
          let $image = $("<img>").attr("src", object?.thumbnailImg);

          // If the object has no thumbnail image, use a default image
          if (object?.thumbnailImg === undefined) {
            $image = $("<img>").attr("src", "libs/img/no-image.png");
          }

          // Populate the div with object properties
          $rightDiv.append("<p>Title: " + object.title + "</p>");
          $rightDiv.append("<p>Summary: " + object.summary + "</p>");
          $rightDiv.append(
            "<p>URL: <a href=" +
              "https://" +
              object.wikipediaUrl +
              " target='_blank'" +
              ">" +
              object.title +
              "</a></p>"
          );
          $leftDiv.append($image);

          // Append the div to the to main div
          $mainDiv.append($leftDiv);
          $mainDiv.append($rightDiv);

          // Append to the output div
          $("#output").append($mainDiv);
        });
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // your error code
        console.log("Error: " + errorThrown);
      },
    });
  });

  // Weather Observation API
  $("#weather").click(function () {
    $("#output").append($spinner);
    $.ajax({
      url: "libs/php/getWeatherInfo.php",
      type: "POST",
      dataType: "json",
      data: {
        location: $("#selCoordinate").val(),
      },
      success: function (result) {
        $("#output").empty(); // Clear the output div
        $spinner.remove(); // Remove the spinner on success
        $div = $("<div></div>"); // Create a div element to hold the weather info

        // Function to render the weather info
        const renderWeatherInfo = (name, data, unit) => {
          return `<h2>${name}: <span>${data} ${unit}</span></h2>`;
        };

        // Append the weather info to the div
        $div.append(
          renderWeatherInfo("Date & Time", result["data"]["datetime"], "")
        );
        $div.append(
          renderWeatherInfo("Temperature", result["data"]["temperature"], "°C")
        );
        $div.append(
          renderWeatherInfo("humidity", result["data"]["humidity"], "%")
        );
        $div.append(renderWeatherInfo("clouds", result["data"]["clouds"], ""));
        $div.append(
          renderWeatherInfo("Wind Speed", result["data"]["windSpeed"], "m/s")
        );
        $div.append(
          renderWeatherInfo("Country Code", result["data"]["countryCode"], "")
        );

        // Append the div to the output div
        $("#output").append($div);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        // your error code
      },
    });
  });
});
