import Helpers from "./helpers.js";
import UiLogic from "./ui.js";

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

// Create the map object with options
let map = L.map("map", {
  layers: [osm],
});

// Create a marker cluster group
let markers = L.markerClusterGroup();
map.addLayer(markers);

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
        const country = result.data[0].components.country;
        const countryCode = result.data[0].components["ISO_3166-1_alpha-2"];
        // Set the country select to the current country
        $("#countrySelect").val(countryCode);
        $("#countrySelect option:selected").text(country);

        $("#exchangeRate").val(result.data[0].annotations.currency.iso_code);
        $("#exchangeRate option:selected").text(
          result.data[0].annotations.currency.name
        );

        console.log(result.data[0]);

        if (result.status.code == 200) {
          $.ajax({
            url: "libs/php/getCountryInfo.php",
            type: "POST",
            dataType: "json",
            data: {
              countryCode: countryCode,
            },
            success: function (result) {
              if (result.status.code == 200) {
                // display country info
                UiLogic.displayCountryInfo(result.data[0]);
                // display currency
                UiLogic.displayCurrencyInfo(result.data[0]);
              } else {
                showToast("Error retrieving country data", 4000, false);
              }
            },
            error: function (jqXHR, status, errorThrown) {
              showToast("Error retrieving country data", 4000, false);
            },
          });
        }
        //calculate exchange rate
        calResult("USD", result.data[0].annotations.currency.iso_code);
        // Display Cities
        displayCities(countryCode);
        // Display Airports
        displayAirports(countryCode);
        // display universities
        displayUniversities(countryCode);
        // display borders
        displayBorders(countryCode);
        // display weather
        displayWeather(countryCode, country);
        // display wikipedia
        handleWikipedia(result.data[0].components.country);
        // send ISO to handleNews function
        handleNews(countryCode);
      },
      error: function (jqXHR, status, errorThrown) {
        showToast("Error retrieving country data", 4000, false);
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
    // handle Errors
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
const myStyle = {
  color: "#4497b2",
  opacity: 1,
  fillOpacity: 0.1,
  fillColor: "#849",
  dashArray: "6, 4",
  weight: 5,
};

// -----------> Marker GeoJSON <------------ //

// Create a GeoJSON layer for Borders
let geoBorder = L.geoJSON([], {
  onEachFeature: function (feature, layer) {
    if (feature.properties && feature.properties.name) {
      layer.bindPopup(feature.properties.name);
    }
  },
  style: myStyle,
});

var airports = L.markerClusterGroup({
  polygonOptions: {
    fillColor: "#fff",
    color: "#000",
    weight: 2,
    opacity: 1,
    fillOpacity: 0.5,
  },
}).addTo(map);

var cities = L.markerClusterGroup({
  polygonOptions: {
    fillColor: "#fff",
    color: "#000",
    weight: 2,
    opacity: 1,
    fillOpacity: 0.5,
  },
}).addTo(map);

var universities = L.markerClusterGroup({
  polygonOptions: {
    fillColor: "#fff",
    color: "#000",
    weight: 2,
    opacity: 1,
    fillOpacity: 0.5,
  },
}).addTo(map);

var airportIcon = L.ExtraMarkers.icon({
  prefix: "fa",
  icon: "fa-plane",
  iconColor: "black",
  markerColor: "white",
  shape: "square",
});

var cityIcon = L.ExtraMarkers.icon({
  prefix: "fa",
  icon: "fa-city",
  markerColor: "green",
  shape: "square",
});

var uniIcons = L.ExtraMarkers.icon({
  prefix: "fa",
  icon: "fa-school",
  markerColor: "orange",
  shape: "square",
});

let overlayMaps = {
  Borders: geoBorder,
  Airports: airports,
  Cities: cities,
  Universities: universities,
};

// Add the baseMaps & overLayMaps to the map
L.control.layers(baseMaps, overlayMaps).addTo(map);

// Display Cities //
const displayCities = (code) => {
  $.ajax({
    url: "libs/php/markers/getCities.php",
    type: "POST",
    data: {
      countryCode: code,
    },
    success: function (result) {
      if (result.status.code == 200) {
        result.data.forEach(function (item) {
          L.marker([item.lat, item.lng], { icon: cityIcon })
            .bindTooltip(
              "<div class='col text-center'><strong>" +
                item.name +
                "</strong><br><i>(" +
                numeral(item.population).format("0,0") +
                ")</i></div>",
              { direction: "top", sticky: true }
            )
            .addTo(cities);
        });
      } else {
        showToast("Error retrieving city data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving city data", 4000, false);
    },
  });
};
// display airports //
const displayAirports = (code) => {
  $.ajax({
    url: "libs/php/markers/getAirports.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "airport",
    },
    success: function (result) {
      if (result.status.code == 200) {
        result.data.forEach(function (item) {
          L.marker([item.lat, item.lng], { icon: airportIcon })
            .bindTooltip(item.name, { direction: "top", sticky: true })
            .addTo(airports);
        });
      } else {
        showToast("Error retrieving airport data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving airport data", 4000, false);
    },
  });
};

const displayUniversities = (code) => {
  $.ajax({
    url: "libs/php/markers/getUniversities.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "university",
    },
    success: function (result) {
      if (result.status.code == 200) {
        result.data.forEach(function (item) {
          L.marker([item.lat, item.lng], { icon: uniIcons })
            .bindTooltip(item.name, { direction: "top", sticky: true })
            .addTo(universities);
        });
      } else {
        showToast("Error retrieving university data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving university data", 4000, false);
    },
  });
};

// display borders //
const displayBorders = (code) => {
  $.ajax({
    url: "libs/php/getBorders.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "borders",
    },
    success: function (result) {
      if (result.status.code == 200) {
        const data = result.border.geometry;
        let borders = [];
        // get the coordinates from the result
        if (data.type === "MultiPolygon") {
          data.coordinates.forEach((poly) => {
            let coords = [];
            poly[0].forEach((coord) => {
              const lat = coord[1];
              const lng = coord[0];
              coords.push([lng, lat]);
            });
            // add the coordinates to the borders array
            borders.push(coords);
          });
        } else {
          // get the coordinates from the result
          data.coordinates[0].forEach((coord) => {
            const lng = coord[0];
            const lat = coord[1];
            // add the coordinates to the borders array
            borders.push([lng, lat]);
          });
        }
        const border = [
          {
            type: "Feature",
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
      } else {
        showToast("Error retrieving border data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving border data", 4000, false);
    },
  });
};

// --------------------------> Weather & Weather Select <------------------------------- //
const displayWeather = (code, country) => {
  $.ajax({
    url: "libs/php/markers/getCities.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "city",
    },
    success: function (result) {
      $("#citiesSelect").empty();
      if (result.status.code == 200) {
        const filtered = result.data.filter((city) => city.name !== country);
        const cities = {
          data: filtered,
        };
        handleWeatherInfo(cities.data[0].name);
        UiLogic.displayWeatherCities(cities);
      } else {
        showToast("Error retrieving city data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving city data", 4000, false);
    },
  });
};

$("#citiesSelect").change(function () {
  // get the selected city
  var city = $("#citiesSelect").val();
  // send the selected city to the displayWeather function
  handleWeatherInfo(city);
});

// Weather Info function based on city
const handleWeatherInfo = (city) => {
  $.ajax({
    url: "libs/php/modals/getWeather.php",
    type: "POST",
    dataType: "json",
    data: {
      city: city,
    },
    success: function (result) {
      if (result.status.code == 200) {
        UiLogic.displayWeather(result);
      } else {
        showToast("Error retrieving weather data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving weather data", 4000, false);
    },
  });
};

// --------------------------> Currency <------------------------------- //

$("#exchangeRate").change(function () {
  $("#fromAmount").val(1);
  const from = "USD";
  const to = $("#exchangeRate").val();
  $("#currencyCode").html(to);
  calResult(from, to);
});

$("#fromAmount").on("keyup", function () {
  const from = "USD";
  const to = $("#exchangeRate").val();
  const amount = $("#fromAmount").val();
  calResult(from, to, amount);
});

$("#fromAmount").on("change", function () {
  const from = "USD";
  const to = $("#exchangeRate").val();
  const amount = $("#fromAmount").val();
  calResult(from, to, amount);
});

const calResult = (from, to, amount = 1) => {
  $.ajax({
    url: "libs/php/modals/getCurrency.php",
    type: "POST",
    dataType: "json",
    data: {
      from: from,
      to: to,
      amount: amount,
    },
    success: function (result) {
      if (result.status.code == 200) {
        const convertedRate = Helpers.formatResult(result.data);
        $("#toAmount").html(convertedRate);
      } else {
        showToast("Error retrieving currency data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving currency data", 4000, false);
    },
  });
};

// currency select
$("document").ready(function () {
  $.ajax({
    url: "libs/php/modals/getCurrencySelect.php",
    type: "POST",
    dataType: "json",
    data: {
      key: "select",
    },
    success: function (result) {
      if (result.status.code == 200) {
        result.data[0].forEach(function (item) {
          $("#exchangeRate").append(
            '<option value="' +
              item.currencyCode +
              '">' +
              item.name +
              "</option>"
          );
        });
      } else {
        showToast("Error retrieving currency data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving currency data", 4000, false);
    },
  });
});

// --------------------------> Wikipedia <------------------------------- //

const handleWikipedia = (country) => {
  $.ajax({
    url: "libs/php/modals/getWikipedia.php",
    type: "POST",
    dataType: "json",
    data: {
      q: country, // Get the search query,
    },
    success: function (result) {
      $("#wikipedia").empty();
      // Loop through the result and create a div element for each object
      if (result.status.code == 200) {
        $.each(result["data"], function (index, object) {
          UiLogic.displayWikipediaInfo(object, country);
        });
      } else {
        showToast("Error retrieving wikipedia data", 4000, false);
      }
      // If the wikipedia div is empty, append a message
      if ($("#wikipedia").is(":empty")) {
        $("#wikipedia").append("<h5>No Wikipedia data available</h5>");
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving wikipedia data", 4000, false);
    },
  });
};

// --------------------------> Latest News  <------------------------------- //
const handleNews = (code) => {
  $.ajax({
    url: "libs/php/modals/getNews.php",
    type: "POST",
    dataType: "json",
    data: {
      countryCode: code,
    },
    success: function (result) {
      $("#news").empty();
      if (result.status.code == 200) {
        $.each(result.data, function (index) {
          const news = result.data[index];
          UiLogic.displayNewsInfo(news);
        });
      } else {
        showToast("Error retrieving news data", 4000, false);
      }
      if ($("#news").is(":empty")) {
        $("#news").append("<h2>No news available</h2>");
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving news data", 4000, false);
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
      if (result.status.code == 200) {
        $.each(result.data, function (index) {
          $("#countrySelect").append(
            '<option value="' +
              result.data[index].iso +
              '">' +
              result.data[index].country +
              "</option>"
          );
        });
      } else {
        showToast("Error retrieving country data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving country data", 4000, false);
    },
  });
});

// Select country and display country info //
$("#countrySelect").change(function () {
  let countryCode = $("#countrySelect").val();
  $.ajax({
    url: "libs/php/getCountryInfo.php",
    type: "POST",
    dataType: "json",
    data: {
      countryCode: countryCode,
    },
    success: function (result) {
      // Clear the select options
      $("#exchangeRate").empty();
      $("#exchangeRate option:selected").empty();

      // Clear the map layers and markers
      markers.clearLayers();
      cities.clearLayers();
      airports.clearLayers();
      universities.clearLayers();
      geoBorder.clearLayers();
      if (result.status.code == 200) {
        // get the coordinates from the result and set the map view
        map = map.setView(result.data[0].latlng, 7.5);

        // send the result to the UiLogic.displayCountryInfo function
        UiLogic.displayCountryInfo(result.data[0]);

        // send the ISO to displayCities function
        displayCities(countryCode);

        // send cities to displayWeather function
        displayWeather(countryCode, result.data[0].name.common);

        // send ISO from displayBorders function
        displayBorders(countryCode);

        // send ISO to displayAirports function
        displayAirports(countryCode);

        // send ISO to Ui.displayCurrencyInfo function
        UiLogic.displayCurrencyInfo(result.data[0]);

        // send country to handleWikipedia function
        handleWikipedia(result.data[0].name.common);

        // send ISO to handleNews function
        handleNews(countryCode);

        // send ISO to displayUniversities function
        displayUniversities(countryCode);

        // get Currency Name and Code of selected country
        const currencyCode = Object.keys(result.data[0].currencies)[0];
        const currencyInfo = result.data[0].currencies[currencyCode];
        const name = currencyInfo.name;

        // set the currency select to the selected country
        $("#exchangeRate").val(currencyCode);
        $("#exchangeRate option:selected").text(name);

        // calculate exchange rate
        calResult("USD", currencyCode);
      } else {
        showToast("Error retrieving country data", 4000, false);
      }
    },
    error: function (jqXHR, status, errorThrown) {
      showToast("Error retrieving country data", 4000, false);
    },
  });
});

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

// --------------------------> Toast <------------------------------- //

function showToast(message, duration) {
  Toastify({
    text: message,
    duration: duration,
    newWindow: true,
    close: true,
    gravity: "top", // `top` or `bottom`
    position: "right", // `left`, `center` or `right`
    stopOnFocus: true, // Prevents dismissing of toast on hover
    style: {
      background: "linear-gradient(to right, #ff0000, #b30000)",
    },
    onClick: function () {}, // Callback after click
  }).showToast();
}
