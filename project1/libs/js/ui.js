import Helpers from "./helpers.js";

export default class UiLogic {
  // Display the weather icon based on the weather condition
  static weatherIcons = (condition) => {
    switch (condition) {
      case "moderate rain":
        return "img/rain.png";
      case "light rain":
        return "img/light-rain.png";
      case "scattered clouds":
        return "img/scattered.png";
      case "broken clouds":
        return "img/broken.png";
      case "sky is clear":
        return "img/sun.png";
      default:
        return "img/rain.png";
    }
  };

  // Display the weather information
  static displayWeather(result) {
    $("#today").empty();
    $("#weatherDays").empty();
    if (result.data === null) {
      $("#today").append(
        '<div style="width: 100%;">' +
          "<p style='font-size:3rem; font-weight:bold;'>" +
          "No weather data available" +
          "</p>" +
          "</div>"
      );
    } else {
      // Display the weather for today
      const today = result.data[0];
      $("#today").append(
        '<div style="width: 100%;">' +
          "<h5>Today's Weather</h5>" +
          "<p style='font-size:3rem; font-weight:bold;'>" +
          today.temp.max +
          "°C</p>" +
          "<p style='font-size:1rem; font-weight:bold;'>" +
          today.weather[0].description +
          "</p>" +
          "</div> <div style='width: 100%;'>" +
          "<img src='" +
          UiLogic.weatherIcons(today.weather[0].description) +
          "'>" +
          "</div>"
      );
      // Display the next 6 days weather forecast
      const forecast = result.data.slice(1, 8);
      forecast.forEach((day) => {
        $("#weatherDays").append(
          "<div>" +
            "<p>" +
            Helpers.convertDate(day.dt) +
            "</p>" +
            "<p>" +
            day.temp.max +
            "°C</p>" +
            "<img src='" +
            UiLogic.weatherIcons(day.weather[0].description) +
            "'>" +
            "</div>"
        );
      });
    }
  }

  static displayWeatherCities(result) {
    $.each(result.data, function (index) {
      $("#citiesSelect").append(
        '<option value="' +
          result.data[index].name +
          '">' +
          result.data[index].name +
          "</option>"
      );
    });
  }

  // Display the currency code and name
  static displayCurrencyInfo(result) {
    // Clear the currency input and output
    $("#amount").val("");
    $("#currencyResult").text("0:00");

    // Display the currency code and name
    const currency = result.annotations.currency;
    $("#currencyCode").text(currency.iso_code);
    $("#currencyName").text(currency.name);
    $("#currencySym").text(currency.symbol);
    $("#resultCode").text(currency.iso_code);
  }

  static displayWeatherInfo(object, country) {
    if (object.title === country) {
      // Create a div element to represent each object
      const $mainDiv = $('<div class="resultContainer"></div>'); // Create a div element to hold the object properties
      const $rightDiv = $('<div class="right"></div>'); // Create a right-div element to hold the object properties
      const $leftDiv = $('<div class="left"></div>'); // Create a left-div element to hold the object properties

      // Create an image element to hold the thumbnail image
      let $image = $("<img>").attr("src", object?.thumbnailImg);

      // Populate the div with object properties
      $rightDiv.append("<p>" + object.summary + "</p>"); // Append the summary to the right-div
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

      $("#wikipedia").append($mainDiv); // Append to the output div
    } else if (object.title !== country) {
      return;
    }
  }

  static displayCountryInfo(result) {
    let countryInfo = result;
    $("#country").text(countryInfo.components.country);
    $("#continent").text(countryInfo.components.continent);
    const countryCode = countryInfo.components.country_code.toUpperCase();
    $("#country_code").text(countryCode);
    $("#timezone").text(countryInfo.annotations.timezone.name);
    $("#currency").text(countryInfo.annotations.currency.name);
    $("#calling").text(countryInfo.annotations.callingcode);
    $("#drive").text(countryInfo.annotations.roadinfo.drive_on);
    $("#speed").text(countryInfo.annotations.roadinfo.speed_in);
  }

  static displayNewsInfo(news) {
    $("#news").append(
      "<div>" +
        "<h5>" +
        news.title +
        "</h5>" +
        "<p>" +
        news.source.name +
        "</p>" +
        "<a href=" +
        news.url +
        " target='_blank'" +
        ">Read more</a>" +
        "<hr>" +
        "</div>"
    );
  }
}
