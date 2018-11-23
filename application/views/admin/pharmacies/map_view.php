
<!DOCTYPE html>
<html>
  <head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Google Maps JavaScript API v3 Example: Geocoding Simple</title>
<style type="text/css">
html, body {
  height: 100%;
  margin: 0;
  padding: 0;
}

#map_canvas {
  height: 100%;
}

@media print {
  html, body {
    height: auto;
  }

  #map_canvas {
    height: 650px;
  }
}
</style>

    <script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyCy0ukcU-KmUDQwyCUyaUQLc66zK0Mk6CU"></script>

    <script>
      var geocoder;
      var map;
      var marker;
      var infowindow = new google.maps.InfoWindow({size: new google.maps.Size(150,50)});
      function initialize() {
        geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(-34.397, 150.644);
        var mapOptions = {
          zoom: 8,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        }
        map = new google.maps.Map(document.getElementById('map_canvas'), mapOptions);
        google.maps.event.addListener(map, 'click', function() {
          infowindow.close();
        });
      }

  function clone(obj){
      if(obj == null || typeof(obj) != 'object') return obj;
      var temp = new obj.constructor(); 
      for(var key in obj) temp[key] = clone(obj[key]);
      return temp;
  }


function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      marker.formatted_address = responses[0].formatted_address;
    } else {
      marker.formatted_address = 'Cannot determine address at this location.';
    }
    infowindow.setContent(marker.formatted_address+"<br>coordinates: "+marker.getPosition().toUrlValue(6));
    infowindow.open(map, marker);
  });
}

      function codeAddress() {
        var address = document.getElementById('address').value;
        geocoder.geocode( { 'address': address}, function(results, status) {
          if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
      if (marker) {
               marker.setMap(null);
               if (infowindow) infowindow.close();
            }
            marker = new google.maps.Marker({
                map: map,
    draggable: true,
                position: results[0].geometry.location
            });
            google.maps.event.addListener(marker, 'dragend', function() {
              // updateMarkerStatus('Drag ended');
               document.getElementById("hidden").value = marker.getPosition().toUrlValue(6);
              geocodePosition(marker.getPosition());
            });
            google.maps.event.addListener(marker, 'click', function() {
              if (marker.formatted_address) {
                infowindow.setContent(marker.formatted_address+"<br>coordinates: "+marker.getPosition().toUrlValue(6));
              } else  {
                infowindow.setContent(address+"<br>coordinates: "+marker.getPosition().toUrlValue(6));
                document.getElementById("hidden").value = marker.getPosition().toUrlValue(6);
              }
              infowindow.open(map, marker);
            });
            google.maps.event.trigger(marker, 'click');
          } else {
            alert('Geocode was not successful for the following reason: ' + status);
          }
        });
      }
    </script>
  </head>
  <body onload="initialize()">
    <div>
      <input id="address" type="textbox" value="Sydney, NSW">
      <input type="button" value="Geocode" onclick="codeAddress()">
        <input id="hidden" type="hidden" value="">
    </div>
    <div id="map_canvas" style="height:90%;top:30px"></div>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"> 
</script> 
<script type="text/javascript"> 
_uacct = "UA-162157-1";
urchinTracker();
</script> 
  </body>
</html>
