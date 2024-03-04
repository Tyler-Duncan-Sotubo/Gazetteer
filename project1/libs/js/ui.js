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
          Math.ceil(today.temp.max) +
          "°C</p>" +
          "<p style='font-size:1rem; font-weight:bold; html-transform: capitalize;'>" +
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
          "<div class='d-flex flex-column align-items-center'>" +
            "<p>" +
            Helpers.convertDate(day.dt) +
            "</p>" +
            "<h5>" +
            Math.ceil(day.temp.max) +
            "°C</h5>" +
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
    // Get Currency Name
    const currencyCode = Object.keys(result.currencies)[0];
    const currencyInfo = result.currencies[currencyCode];

    $("#currencyCode").html(currencyCode);
    $("#currencySym").html(currencyInfo.symbol);
    $("#resultCode").html(currencyCode);
  }

  static displayWikipediaInfo(object, country) {
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
        " <a href=" +
          "https://" +
          object.wikipediaUrl +
          " target='_blank'" +
          ">" +
          "<p>Read More</p></a>"
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
    // Get Currency Name
    const currencyCode = Object.keys(result.currencies)[0];
    const currencyInfo = result.currencies[currencyCode];
    const name = currencyInfo.name;
    const symbol = currencyInfo.symbol;

    const countryName = result.name.common;
    let callCode;
    if (countryName === "Russia" || countryName == "United States") {
      callCode = result.idd.root;
    } else {
      callCode = result.idd.root + result.idd.suffixes[0];
    }
    $("#countryName").html(countryName);
    $("#capital").html(result.capital[0]);
    $("#continent").html(result.continents[0]);
    $("#callCode").html(callCode);
    $("#population").html(numeral(result.population).format("0,0"));
    $("#area").html(numeral(result.area).format("0,0"));
    $("#drive").html(result.car.side);
    $("#currencyName").html(name);
    $("#currencySymbol").html(symbol);
  }

  static displayNewsInfo(news) {
    let image;
    if (news.image_url == null) {
      image = "img/news.avif";
    } else {
      image = news.image_url;
    }

    let trimmedTitle = "";

    if (news.title) {
      trimmedTitle =
        news.title.length > 60
          ? news.title.substring(0, 60) + "..."
          : news.title;
    }

    $("#news").append(
      "<div class='d-flex' style='margin-bottom: 20px'>" +
        "<img src='" +
        image +
        "' width='150' height='100' style='margin-right: 20px'>" +
        "<div>" +
        "<a href='" +
        news.link +
        "' class='fw-bold fs-6 html-black ' target='_blank'" +
        ">" +
        trimmedTitle +
        "</a>" +
        "<p class='text-capitalize'>" +
        news.source_id +
        "</p>" +
        "</div>" +
        "</div>"
    );
  }
}
