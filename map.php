<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Map</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <style>
        #map {
            height: 300px; /* Set the height of the map */
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        let map;

        document.addEventListener('DOMContentLoaded', function() {
            initializeMap(); // Call the map initialization function
        });

        function initializeMap() {
            // Initialize the map
            map = L.map('map').setView([0, 0], 2); // Default view

            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap'
            }).addTo(map);

            // Get saved coordinates from local storage
            const lat = localStorage.getItem('lastLatitude');
            const lon = localStorage.getItem('lastLongitude');

            if (lat && lon) {
                // Plot the marker if coordinates exist
                L.marker([lat, lon]).addTo(map).bindPopup('Saved Location').openPopup();
                map.setView([lat, lon], 10); // Zoom to the marker
            }
        }
    </script>
</body>
</html>
