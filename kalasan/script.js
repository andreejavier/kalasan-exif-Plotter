var map = L.map('map').setView([14.350878, 122.299805], 5);

L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
}).addTo(map);

var markers = L.markerClusterGroup();

fetch('api.php') // Replace with your actual API endpoint URL
    .then(function(response) {
        return response.json();
    })
    .then(function(data) {
        // Process the fetched data
        data.forEach(function(record) {
            var lat = parseFloat(record.record_lat);
            var lon = parseFloat(record.record_lon);
            var loc = record.record_loc;
            
            var marker = L.marker(new L.LatLng(lat, lon), {
                title: loc
            });
            marker.bindPopup(loc);
            markers.addLayer(marker);
        });

        map.addLayer(markers);
    })
    .catch(function(error) {
        console.log('Error fetching data:', error);
    });
