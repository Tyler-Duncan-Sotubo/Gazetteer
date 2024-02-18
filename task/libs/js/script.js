$(document).ready(function () {
  const $spinner = $('<div class="spinner"></div>'); // Create a spinner element

  // Wikipedia Search
  $("#wikipedia").click(function () {
    // Check if the search query is empty
    if ($("#query").val() === "") {
      alert("Please enter a search query");
      $spinner.remove();
      return;
    }

    $("#output").append($spinner); // Append loading spinner on Click

    $.ajax({
      url: "libs/php/getWikipediaInfo.php",
      type: "POST",
      dataType: "json",
      data: {
        q: $("#query").val(), // Get the search query
      },
      success: function (result) {
        $("#output").empty(); // Clear the output div
        $spinner.remove(); // Remove the spinner on success

        // Loop through the result and create a div element for each object
        $.each(result["data"], function (index, object) {
          // Create a div element to represent each object
          const $mainDiv = $('<div class="resultContainer"></div>'); // Create a div element to hold the object properties
          const $rightDiv = $('<div class="right"></div>'); // Create a right-div element to hold the object properties
          const $leftDiv = $('<div class="left"></div>'); // Create a left-div element to hold the object properties

          // Create an image element to hold the thumbnail image
          let $image = $("<img>").attr("src", object?.thumbnailImg);

          // If the object has no thumbnail image, use a default image
          if (object?.thumbnailImg === undefined) {
            $image = $("<img>").attr("src", "libs/img/no-image.png");
          }

          // Populate the div with object properties
          $rightDiv.append("<p>Title: " + object.title + "</p>"); // Append the title to the right-div
          $rightDiv.append("<p>Summary: " + object.summary + "</p>"); // Append the summary to the right-div
          $rightDiv.append(
            "<p>URL: <a href=" +
              "https://" +
              object.wikipediaUrl +
              " target='_blank'" +
              ">" +
              object.title +
              "</a></p>"
          ); // Append the URL to the right-div
          $leftDiv.append($image); // Append the image to the left-div

          // Append the div to the to main div
          $mainDiv.append($leftDiv);
          $mainDiv.append($rightDiv);

          $("#output").append($mainDiv); // Append to the output div
        });
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error: " + errorThrown);
      },
    });
  });

  // Weather Observation API
  $("#weather").click(function () {
    $("#output").append($spinner); // Append loading spinner on Click

    // get the weather info
    $.ajax({
      url: "libs/php/getWeatherInfo.php",
      type: "POST",
      dataType: "json",
      data: {
        location: $("#selCoordinate").val(), // Get the selected location
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
          renderWeatherInfo("Date & Time", result["data"]["datetime"], "") // Append the date and time to the div
        );
        $div.append(
          renderWeatherInfo("Temperature", result["data"]["temperature"], "°C") // Append the temperature to the div
        );
        $div.append(
          renderWeatherInfo("humidity", result["data"]["humidity"], "%") // Append the humidity to the div
        );
        $div.append(renderWeatherInfo("clouds", result["data"]["clouds"], "")); // Append the clouds to the div
        $div.append(
          renderWeatherInfo("Wind Speed", result["data"]["windSpeed"], "m/s") // Append the wind speed to the div
        );
        $div.append(
          renderWeatherInfo("Country Code", result["data"]["countryCode"], "") // Append the country code to the div
        );

        // Append the div to the output div
        $("#output").append($div);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error: " + errorThrown);
      },
    });
  });

  // Get Postal Code
  $("#postcode").click(function () {
    $("#output").append($spinner); // Append loading spinner on Click

    // get the postal code info
    $.ajax({
      url: "libs/php/getPostalCode.php",
      type: "POST",
      dataType: "json",
      data: {
        location: $("#selPostCode").val(), // Get the selected location
      },
      success: function (result) {
        $("#output").empty(); // Clear the output div
        $spinner.remove(); // Remove the spinner on success
        $div = $("<div></div>"); // Create a div element to hold the postcode info

        // Function to render the postcode info
        const renderPostalCode = (name, data) => {
          return `<h2>${name}: <span>${data}</span></h2>`;
        };

        // Append the postcode info to the div
        $div.append(
          renderPostalCode("Postal Code", result["data"][0]["postalCode"]) // Append the postal code to the div
        );
        $div.append(
          renderPostalCode("Country", result["data"][0]["adminName1"]) // Append the country to the div
        );
        $div.append(renderPostalCode("State", result["data"][0]["adminName2"])); // Append the state to the div
        $div.append(
          renderPostalCode("CountryCode", result["data"][0]["countryCode"]) // Append the country code to the div
        );

        $("#output").append($div); // Append the div to the output div
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error: " + errorThrown);
      },
    });
  });
});
