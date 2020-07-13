var $mapInputCoordinate = $('#map-input-coordinate'),
  map,
  geocoder,
  marker

function putCoordinateMap() {

  var lat,
    lng,
    address_components,
    elm = document.getElementById('map-input-coordinate')

  map = elm
  lat = map.getAttribute('data-lat')
  lng = map.getAttribute('data-lng')

  var myLatlng = new google.maps.LatLng(lat, lng);
  var mapOptions = {
    zoom: 16,
    scrollwheel: false,
    center: myLatlng,
    draggableCursor: 'default',
    styles: [{
      "featureType": "administrative.country",
      "elementType": "geometry.fill",
      "stylers": [{
        "saturation": "-17"
      },
      {
        "lightness": "4"
      },
      {
        "gamma": "3.80"
      },
      {
        "weight": "0.68"
      }
      ]
    },
    {
      "featureType": "administrative.province",
      "elementType": "geometry.fill",
      "stylers": [{
        "saturation": "-13"
      },
      {
        "visibility": "simplified"
      }
      ]
    },
    {
      "featureType": "landscape.natural",
      "elementType": "geometry.fill",
      "stylers": [{
        "visibility": "on"
      },
      {
        "color": "#e0efef"
      }
      ]
    },
    {
      "featureType": "poi",
      "elementType": "geometry.fill",
      "stylers": [{
        "visibility": "on"
      },
      {
        "hue": "#1900ff"
      },
      {
        "color": "#c0e8e8"
      }
      ]
    },
    {
      "featureType": "road",
      "elementType": "geometry",
      "stylers": [{
        "lightness": 100
      },
      {
        "visibility": "simplified"
      }
      ]
    },
    {
      "featureType": "road",
      "elementType": "labels",
      "stylers": [{
        "visibility": "off"
      }]
    },
    {
      "featureType": "transit.line",
      "elementType": "geometry",
      "stylers": [{
        "visibility": "on"
      },
      {
        "lightness": 700
      }
      ]
    },
    {
      "featureType": "water",
      "elementType": "all",
      "stylers": [{
        "color": "#2b7cf7"
      }]
    }
    ]
  }

  geocoder = new google.maps.Geocoder()

  map = new google.maps.Map(map, mapOptions)

  marker = new google.maps.Marker({
    position: myLatlng,
    map: map,
    animation: google.maps.Animation.DROP,
  })

  if (elm.hasAttribute('current-location')) {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function (position) {
        var pos = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        }

        map.setCenter(pos)
        marker.setPosition(pos)

        fillForm(pos)

        flashMessage('ni ni-square-pin', 'Location found.', 'success', 'top', 'center')
      });
    } else {
      flashMessage('ni ni-square-pin', 'The Geolocation service failed.', 'danger', 'top', 'center')
    }
  } else {
    var pos = {
      lat: parseFloat(lat),
      lng: parseFloat(lng)
    }
    // fillForm(pos)
  }

  google.maps.event.addListener(map, 'click', function (event) {
    var pos = {
      lat: event.latLng.lat(),
      lng: event.latLng.lng()
    }
    marker.setPosition(pos)
    fillForm(pos)
  })

  $($mapInputCoordinate.attr('form-search-address')).on('keydown', function (event) {
    if (event.which === 13) {
      event.preventDefault()
      $($mapInputCoordinate.attr('btn-search-address')).click()
    }
  })

  $($mapInputCoordinate.attr('btn-search-address')).on('click', function () {
    var address = $($mapInputCoordinate.attr('form-search-address')).val()
    geocoder.geocode({
      'address': address
    }, function (results, status) {
      if (status == 'OK') {
        map.setCenter(results[0].geometry.location)
        marker.setPosition(results[0].geometry.location)

        fillForm(results[0].geometry.location)

      } else {
        flashMessage('ni ni-square-pin', 'Geocode was not successful for the following reason: ' + status, 'danger', 'top', 'center')
      }
    })
  })

}

function fillForm(pos) {

  var form = $mapInputCoordinate.attr('form')

  $(form).find('#latitude').val(pos.lat)
  $(form).find('#longitude').val(pos.lng)

  geocoder.geocode({ 'location': pos }, function (results, status) {
    if (status == 'OK') {
      var address_components = results[0].address_components
      $(form).find('#city').val(address_components.filter(function (a) {
        return a.types.indexOf('administrative_area_level_2') > -1
      })[0].short_name)
    }
  })

}

if ($mapInputCoordinate.length) {
  google.maps.event.addDomListener(window, 'load', putCoordinateMap)
}