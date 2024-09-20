<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>
</head>

<body>
    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-md-4">
                <img id="imagePreview" class="img-fluid" src="https://www.freeiconspng.com/uploads/no-image-icon-6.png" width="200px" alt="Preview">
            </div>
            <div class="col-md-8">
                <h5 class="card-title">Capture Image</h5>
                <input type="file" id="imageInput" class="d-none" accept="image/*" capture="user" onchange="handleImageInput(this);" />
                <button class="btn btn-primary mt-3" onclick="document.getElementById('imageInput').click();">
                    Open Camera
                </button>
                <form action="map.html" method="get" class="mt-3">
                    <div class="mb-3">
                        <label for="latitudeInput" class="form-label">Latitude</label>
                        <input type="text" class="form-control" id="latitudeInput" name="lat" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label for="longitudeInput" class="form-label">Longitude</label>
                        <input type="text" class="form-control" id="longitudeInput" name="lon" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label for="dateTime" class="form-label">Datetime</label>
                        <input type="text" class="form-control" id="dateTime" name="date" readonly disabled>
                    </div>
                    <div class="mb-3">
                        <label for="locationAddress" class="form-label">Location Address</label>
                        <input type="text" id="locationAddress" class="form-control" readonly disabled>
                    </div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                        Save Data
                    </button>
                    <button id="clearButton" class="btn btn-secondary" type="button">Clear</button>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Upload Details?</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You want to save the data?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="submitForm()">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById("clearButton").addEventListener("click", clearFields);
    });

    function clearFields() {
        document.getElementById("imageInput").value = "";
        document.getElementById("imagePreview").src = "https://www.freeiconspng.com/uploads/no-image-icon-6.png";
        document.getElementById("latitudeInput").value = "";
        document.getElementById("longitudeInput").value = "";
        document.getElementById("dateTime").value = "";
        document.getElementById("locationAddress").value = "";
        document.getElementById("latitudeInput").disabled = true;
        document.getElementById("longitudeInput").disabled = true;
        document.getElementById("dateTime").disabled = true;
    }

    function handleImageInput(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("imagePreview").src = e.target.result;
                EXIF.getData(input.files[0], function() {
                    var lat = EXIF.getTag(this, "GPSLatitude");
                    var lon = EXIF.getTag(this, "GPSLongitude");
                    var date = EXIF.getTag(this, "DateTimeOriginal");

                    if (lat && lon) {
                        var latRef = EXIF.getTag(this, "GPSLatitudeRef") || "N";
                        var lonRef = EXIF.getTag(this, "GPSLongitudeRef") || "W";
                        lat = (lat[0] + lat[1] / 60 + lat[2] / 3600) * (latRef === "N" ? 1 : -1);
                        lon = (lon[0] + lon[1] / 60 + lon[2] / 3600) * (lonRef === "W" ? -1 : 1);

                        document.getElementById("latitudeInput").value = lat;
                        document.getElementById("longitudeInput").value = lon;
                        document.getElementById("latitudeInput").disabled = false;
                        document.getElementById("longitudeInput").disabled = false;

                        // Get the address from the coordinates
                        getAddressFromCoordinates(lat, lon);
                    } else {
                        document.getElementById("latitudeInput").value = "No GPS data";
                        document.getElementById("longitudeInput").value = "No GPS data";
                        document.getElementById("locationAddress").value = "";
                    }

                    if (date) {
                        document.getElementById("dateTime").value = date;
                        document.getElementById("dateTime").disabled = false;
                    } else {
                        document.getElementById("dateTime").value = "No date data";
                    }
                });
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function getAddressFromCoordinates(lat, lon) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.display_name) {
                    document.getElementById("locationAddress").value = data.display_name;
                } else {
                    document.getElementById("locationAddress").value = "No address found";
                }
            })
            .catch(error => {
                console.error("Error fetching address:", error);
                document.getElementById("locationAddress").value = "Error fetching address";
            });
    }

    function submitForm() {
        const lat = document.getElementById("latitudeInput").value;
        const lon = document.getElementById("longitudeInput").value;

        // Save coordinates to local storage
        localStorage.setItem('lastLatitude', lat);
        localStorage.setItem('lastLongitude', lon);

        document.querySelector('form').submit();
    }
    </script>
</body>
</html>
