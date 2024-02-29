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
        const c = result.data[0].components.country;
        const countryCode = result.data[0].components["ISO_3166-1_alpha-2"];
        const currentCountry = countryCode + "," + c;

        // Set the country select to the current country
        $("#countrySelect").val(currentCountry);

        // Display Cities
        displayCities(countryCode);
        // Display Airports
        displayAirports(countryCode);
        // display universities
        displayUniversities(countryCode);
        // display borders
        displayBorders(countryCode);
        // display weather
        displayWeather(countryCode);
        // display country info
        UiLogic.displayCountryInfo(result.data[0]);
        // display currency
        UiLogic.displayCurrencyInfo(result.data[0]);
        // display wikipedia
        handleWikipedia(result.data[0].components.country);
        // send ISO to handleNews function
        handleNews(countryCode);
      },
      error: function (jqXHR, status, errorThrown) {
        Helpers.errorHandler(status);
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
    // Create a marker
    var Icon = L.icon({
      iconUrl: "img/Home.png",
      iconSize: [45, 42], // size of the icon
    });
    L.marker([lat, lng], {
      icon: Icon,
    })
      .addTo(map)
      .bindPopup("You are here")
      .openPopup();
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
function onEachFeature(feature, layer) {
  layer.bindPopup(feature.properties.name);
  layer.on("mouseover", function (e) {
    this.openPopup();
  });
  layer.on("mouseout", function (e) {
    this.closePopup();
  });
}

// Create a GeoJSON layer for Borders
let geoBorder = L.geoJSON([], {
  onEachFeature: function (feature, layer) {
    layer.bindPopup(feature.properties.name);
  },
  style: myStyle,
});

// Create a GeoJSON layer for Universities
let uniGeoJson = L.geoJSON([], {
  onEachFeature: onEachFeature,
  pointToLayer: function (feature, latlng) {
    var Icon = L.icon({
      iconUrl: "img/school.png",
      iconSize: [45, 42], // size of the icon
    });
    return L.marker(latlng, { icon: Icon });
  },
});

// Create a GeoJSON layer for Airports
let airportJson = L.geoJSON([], {
  onEachFeature: onEachFeature,
  pointToLayer: function (feature, latlng) {
    var Icon = L.icon({
      iconUrl: "img/airport.png",
      iconSize: [45, 42], // size of the icon
    });
    return L.marker(latlng, { icon: Icon });
  },
});

// Create a GeoJSON layer for Cities
let cityJson = L.geoJSON([], {
  onEachFeature: onEachFeature,
  pointToLayer: function (feature, latlng) {
    var Icon = L.icon({
      iconUrl: "img/location.png",
      iconSize: [45, 42], // size of the icon
    });
    return L.marker(latlng, { icon: Icon });
  },
});

let overlayMaps = {
  Borders: geoBorder,
  Universities: uniGeoJson,
  Airports: airportJson,
  Cities: cityJson,
};

// Add the baseMaps & overLayMaps to the map
L.control.layers(baseMaps, overlayMaps).addTo(map);

// Reusable function to get the points from data array on each api call
const getPoints = (result) => {
  const featured = [];
  $.each(result.data, function (index) {
    featured.push({
      type: "Feature",
      geometry: {
        type: "Point",
        coordinates: [result.data[index].lng, result.data[index].lat],
      },
      properties: {
        name: result.data[index].name,
      },
    });
  });
  return featured;
};

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
      // get the points from the result
      const featured = getPoints(result);
      // create a GeoJSON layer
      const cities = {
        type: "FeatureCollection",
        features: featured,
      };
      // add the GeoJSON layer to the map
      cityJson.addData({
        type: "FeatureCollection",
        features: cities.features,
      });
      // add the markers to the map
      markers.addLayer(cityJson).addTo(map);
    },
    error: function (jqXHR, status, errorThrown) {
      // handle errors
      Helpers.errorHandler(status);
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
      // get the points from the result
      const featured = getPoints(result);
      // create a GeoJSON layer
      const airports = {
        type: "FeatureCollection",
        features: featured,
      };
      // add the GeoJSON layer to the map
      airportJson.addData({
        type: "FeatureCollection",
        features: airports.features,
      });
      // add the markers to the map
      markers.addLayer(airportJson).addTo(map);
    },
    error: function (jqXHR, status, errorThrown) {
      // handle errors
      Helpers.errorHandler(status);
    },
  });
};
// display airport

const displayUniversities = (code) => {
  $.ajax({
    url: "libs/php/markers.php",
    type: "POST",
    data: {
      countryCode: code,
      action: "university",
    },
    success: function (result) {
      // get the points from the result
      const featured = getPoints(result);
      // create a GeoJSON layer
      const Universities = {
        type: "FeatureCollection",
        features: featured,
      };
      // add the GeoJSON layer to the map
      uniGeoJson.addData({
        type: "FeatureCollection",
        features: Universities.features,
      });
      markers.addLayer(uniGeoJson).addTo(map);
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
      UiLogic.displayWeatherCities(result);
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      city: city,
      action: "weather",
    },
    success: function (result) {
      UiLogic.displayWeather(result);
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
    },
  });
};

// --------------------------> Currency <------------------------------- //
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
      const convertedRate = Helpers.formatResult(result);
      $("#currencyResult").text(convertedRate);
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
const handleWikipedia = (country) => {
  $.ajax({
    url: "libs/php/modalControllers.php",
    type: "POST",
    dataType: "json",
    data: {
      q: country, // Get the search query
      action: "wikipedia",
    },
    success: function (result) {
      $("#wikipedia").empty();
      // Loop through the result and create a div element for each object
      $.each(result["data"], function (index, object) {
        UiLogic.displayWeatherInfo(object, country);
      });
      if ($("#wikipedia").is(":empty")) {
        $("#wikipedia").append("<h5>No Wikipedia data available</h5>");
      }
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
    },
  });
};

// --------------------------> Latest News  <------------------------------- //
const handleNews = (code) => {
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
        UiLogic.displayNewsInfo(news);
      });
      if ($("#news").is(":empty")) {
        $("#news").append("<h2>No news available</h2>");
      }
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
      markers.clearLayers();
      geoBorder.clearLayers();
      cityJson.clearLayers();
      airportJson.clearLayers();
      uniGeoJson.clearLayers();
      // get the coordinates from the result and set the map view
      const coords = result.data[0].geometry;
      map = map.setView([coords.lat, coords.lng], 7.5);
      // send the result to the UiLogic.displayCountryInfo function
      UiLogic.displayCountryInfo(result.data[0]);
      // get return value from displayCities function and send to displayWeather function
      displayCities(code);
      // send cities to displayWeather function
      displayWeather(code);
      // send ISO from displayBorders function
      displayBorders(code);
      // send ISO to displayAirports function
      displayAirports(code);
      // send ISO to Ui.displayCurrencyInfo function
      UiLogic.displayCurrencyInfo(result.data[0]);
      // send country to handleWikipedia function
      handleWikipedia(result.data[0].components.country);
      // send ISO to handleNews function
      handleNews(code);
      // send ISO to displayUniversities function
      displayUniversities(code);
    },
    error: function (jqXHR, status, errorThrown) {
      Helpers.errorHandler(status);
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
