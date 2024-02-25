import { convertDate, formatResult } from "./helpers.js";

const osm = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 10,
  attribution: "© OpenStreetMap",
});

const osmHOT = L.tileLayer(
  "https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png",
  {
    maxZoom: 10,
    attribution: "© OpenStreetMap",
  }
);

let baseMaps = {
  Street: osm,
  StreetHOT: osmHOT,
};

const myStyle = {
  color: "#4497b2",
  opacity: 1,
  fillOpacity: 0.1,
  fillColor: "#849",
  dashArray: "6, 4",
  weight: 5,
};

// Create a GeoJSON layer for Borders
let geoBorder = L.geoJSON([], {
  onEachFeature: function (feature, layer) {
    layer.bindPopup(feature.properties.name);
  },
  style: myStyle,
});

// Create the map object with options
let map = L.map("map", {
  layers: [osm],
});

// Create a marker cluster group
let markers = L.markerClusterGroup();
map.addLayer(markers);

// Add the baseMaps to the map
let layerControl = L.control.layers(baseMaps).addTo(map);

// ---------------------------> User Location and Current User <----------------------------- //
(async () => {
  try {
    // Get current position using navigator.geolocation
    const position = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject);
    });

    // Get the latitude and longitude from the position object
    let lat = position.coords.latitude;
    let lng = position.coords.longitude;

    // Get the country from the coordinates
    $.ajax({
      url: "libs/php/country.php",
      type: "POST",
      dataType: "json",
      data: {
        lat: lat,
        lng: lng,
        key: "coords",
      },
      success: function (result) {
        // Get the country code and name
        const c = result.data[0].components.country;
        const countryCode = result.data[0].components["ISO_3166-1_alpha-2"];
        const currentCountry = countryCode + "," + c;

        // Set the country select to the current country
        $("#countrySelect").val(currentCountry);

        // Display Cities
        displayCities(countryCode);
        // Display Airports
        displayAirports(countryCode);
        // display borders
        displayBorders(countryCode);
        // display weather
        displayWeather(countryCode);
        // display country info
        displayCountryInfo(result.data[0]);
        // display currency
        displayCurrencyInfo(result.data[0]);
        // display wikipedia
        displayWikipedia(result.data[0].components.country);
        // send ISO to displayNews function
        displayNews(countryCode);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        console.log("Error: " + errorThrown);
      },
    });

    // remove preloader
    $("#preloader").remove();
    // initialize the map on the "map" div with a given center and zoom
    map = map.setView([lat, lng], 7);
    L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
      maxZoom: 12,
      attribution:
        '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);
  } catch (error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        alert("User denied the request for Geolocation.");
        break;
      case error.POSITION_UNAVAILABLE:
        alert("Location information is unavailable.");
        break;
      default:
        alert("An unknown error occurred:", error.message);
        break;
    }
  }
})();

// ---------------------------> Marker Logics <----------------------------- //

// Display Cities //
const displayCities = (code) => {
  $.ajax({
    url: "libs/php/markers.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "city",
    },
    success: function (result) {
      $.each(result.data, function (index) {
        // Create a marker
        var Icon = L.icon({
          iconUrl: "img/location-pin.png",
          iconSize: [40, 46], // size of the icon
        });
        const cityMarker = L.marker(
          [result.data[index].lat, result.data[index].lng],
          {
            icon: Icon,
          }
        );
        // Add a popup to the marker
        cityMarker.bindPopup(result.data[index].name);

        // Add mouseover and mouseout events to the marker
        cityMarker.on("mouseover", function (e) {
          this.openPopup();
        });
        cityMarker.on("mouseout", function (e) {
          this.closePopup();
        });

        // Add the marker to the markers layer
        markers.addLayer(cityMarker).addTo(map);
      });
      // Add the markers layer to the map
      layerControl.addOverlay(markers, "Cities & Airports");
    },
    error: function (jqXHR, exception) {
      console.log("Option select");
    },
  });
};

// display airports //
const displayAirports = (code) => {
  $.ajax({
    url: "libs/php/markers.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "airport",
    },
    success: function (result) {
      $.each(result.data, function (index) {
        var Icon = L.icon({
          iconUrl: "img/airplane.png",
          iconSize: [30, 30], // size of the icon
        });
        const airportMarker = L.marker(
          [result.data[index].lat, result.data[index].lng],
          {
            icon: Icon,
          }
        );
        airportMarker.bindPopup(result.data[index].name);
        airportMarker.on("mouseover", function (e) {
          this.openPopup();
        });
        airportMarker.on("mouseout", function (e) {
          this.closePopup();
        });
        markers.addLayer(airportMarker).addTo(map);
      });
    },
    error: function (jqXHR, exception) {
      console.log("Option select");
    },
  });
};

// display borders //
const displayBorders = (code) => {
  $.ajax({
    url: "libs/php/markers.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "borders",
    },
    success: function (result) {
      const data = result.border.geometry;
      let borders = [];
      if (data.type === "MultiPolygon") {
        data.coordinates.forEach((poly) => {
          let coords = [];
          poly[0].forEach((coord) => {
            const lat = coord[1];
            const lng = coord[0];
            coords.push([lng, lat]);
          });
          borders.push(coords);
        });
      } else {
        data.coordinates[0].forEach((coord) => {
          const lng = coord[0];
          const lat = coord[1];
          borders.push([lng, lat]);
        });
      }
      const border = [
        {
          type: "Feature",
          properties: {
            popupContent: "",
          },
          geometry: {
            type: data.type,
            coordinates: [borders],
          },
        },
      ];
      geoBorder.addData({
        type: "FeatureCollection",
        features: border,
      });
      geoBorder.addTo(map);
      map.fitBounds(geoBorder.getBounds());
    },
    error: function (jqXHR, exception) {
      console.log("Option select");
    },
  });
};

// --------------------------> Weather & Weather Select <------------------------------- //

const displayWeather = (code) => {
  $.ajax({
    url: "libs/php/markers.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "city",
    },
    success: function (result) {
      $("#citiesSelect").empty();
      handleWeatherInfo(result.data[0].name);
      $.each(result.data, function (index) {
        $("#citiesSelect").append(
          '<option value="' +
            result.data[index].name +
            '">' +
            result.data[index].name +
            "</option>"
        );
      });
    },
    error: function (jqXHR, exception) {
      console.log("Option select");
    },
  });
};

$("#citiesSelect").change(function () {
  // get the selected city
  var city = $("#citiesSelect").val();

  // send the selected city to the displayWeather function
  handleWeatherInfo(city);
});

// Weather Icons
const weatherIcons = (condition) => {
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

// Weather Info function based on city
const handleWeatherInfo = (city) => {
  $.ajax({
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      city: city,
      action: "weather",
    },
    success: function (result) {
      //
      $("#today").empty();
      $("#weatherDays").empty();
      // If no weather data is available
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
            weatherIcons(today.weather[0].description) +
            "'>" +
            "</div>"
        );

        // Display the next 6 days weather forecast
        const forecast = result.data.slice(1, 8);
        forecast.forEach((day) => {
          $("#weatherDays").append(
            "<div>" +
              "<p>" +
              convertDate(day.dt) +
              "</p>" +
              "<p>" +
              day.temp.max +
              "°C</p>" +
              "<img src='" +
              weatherIcons(day.weather[0].description) +
              "'>" +
              "</div>"
          );
        });
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
};

// --------------------------> Currency Converter <------------------------------- //

// Display Currency Info
const displayCurrencyInfo = (result) => {
  // Clear the currency input and output
  $("#amount").val("");
  $("#currencyResult").text("0:00");

  // Display the currency code and name
  const currency = result.annotations.currency;
  $("#currencyCode").text(currency.iso_code);
  $("#currencyName").text(currency.name);
  $("#currencySym").text(currency.symbol);
  $("#resultCode").text(currency.iso_code);
};

const convertCurrency = (from, to, amount) => {
  $.ajax({
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      from: from,
      to: to,
      amount: amount,
      action: "currency",
    },
    success: function (result) {
      // Format the result and display it
      const convertedRate = formatResult(result);
      $("#currencyResult").text(convertedRate);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
};

$("#amount").on("input", function () {
  const inputValue = $(this).val();
  const defaultCurrency = "GBP";
  const to = $("#currencyCode").text();
  let amount = inputValue;
  if (inputValue === "" || inputValue === 0) {
    amount = 1;
  }
  convertCurrency(defaultCurrency, to, amount);
});

// --------------------------> Wikipedia <------------------------------- //

const displayWikipedia = (country) => {
  $.ajax({
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      q: country, // Get the search query
      action: "wikipedia",
    },
    success: function (result) {
      $("#wikipedia").empty(); // Clear the output div

      // Loop through the result and create a div element for each object
      $.each(result["data"], function (index, object) {
        if (object.title === country) {
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
      });
      if ($("#wikipedia").is(":empty")) {
        $("#wikipedia").append("<h5>No Wikipedia data available</h5>");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
};

// --------------------------> Latest News  <------------------------------- //

const displayNews = (code) => {
  $.ajax({
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      countryCode: code,
      action: "news",
    },
    success: function (result) {
      $("#news").empty();
      $.each(result.data, function (index) {
        const news = result.data[index];
        console.log(news);
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
      });
      if ($("#news").is(":empty")) {
        $("#news").append("<h2>No news available</h2>");
      }
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
};

// --------------------------> Country Select <------------------------------- //

// Get country select options //
$(document).ready(function () {
  $.ajax({
    url: "libs/php/country.php",
    type: "POST",
    dataType: "json",
    data: {
      key: "select",
    },
    success: function (result) {
      $.each(result.data, function (index) {
        $("#countrySelect").append(
          '<option value="' +
            result.data[index].iso +
            "," +
            result.data[index].country +
            '">' +
            result.data[index].country +
            "</option>"
        );
      });
    },
    error: function (jqXHR, exception) {
      console.log("Option select");
    },
  });
});

// Select country and display country info //
$("#countrySelect").change(function () {
  const value = $("#countrySelect").val();
  const code = value.substring(0, 2);
  $.ajax({
    url: "libs/php/country.php",
    type: "POST",
    dataType: "json",
    data: {
      country: $("#countrySelect").val(),
      key: "code",
    },
    success: function (result) {
      // Clear the map layers and markers
      geoBorder.clearLayers();
      markers.clearLayers();
      layerControl.removeLayer(markers);

      // get the coordinates from the result and set the map view
      const coords = result.data[0].geometry;
      map = map.setView([coords.lat, coords.lng], 7.5);

      // send the result to the displayCountryInfo function
      displayCountryInfo(result.data[0]);

      // get return value from displayCities function and send to displayWeather function
      displayCities(code);

      // send cities to displayWeather function
      displayWeather(code);

      // send ISO from displayBorders function
      displayBorders(code);

      // send ISO to displayAirports function
      displayAirports(code);

      // send ISO to displayCurrencyInfo function
      displayCurrencyInfo(result.data[0]);

      // send country to displayWikipedia function
      displayWikipedia(result.data[0].components.country);

      // send ISO to displayNews function
      displayNews(code);
    },
    error: function (jqXHR, textStatus, errorThrown) {
      console.log("Error: " + errorThrown);
    },
  });
});

// display country info //
const displayCountryInfo = (result) => {
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
};

// --------------------------> Modals <------------------------------- //
L.easyButton("fa-info", function (btn, map) {
  $("#countryInfo").modal("show");
}).addTo(map);

L.easyButton("fa-solid fa-cloud", function (btn, map) {
  $("#weatherinfo").modal("show");
}).addTo(map);

L.easyButton("fa-solid fa-wallet", function (btn, map) {
  $("#currencyConverter").modal("show");
}).addTo(map);

L.easyButton("fa-brands fa-wikipedia-w", function (btn, map) {
  $("#wikipediainfo").modal("show");
}).addTo(map);

L.easyButton("fa-solid fa-rss", function (btn, map) {
  $("#newsInfo").modal("show");
}).addTo(map);
