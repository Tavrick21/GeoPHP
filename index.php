<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil GeoPHP</title>
</head>
<body>
    <h1>Bienvenue sur GeoPHP</h1>
    <p>Ce projet utilise <strong>GeoPHP</strong> pour manipuler des données géographiques.</p>
    <ul>
        <li><a href="test.php">Tester GeoPHP</a></li>
        <li><a href="db_test.php">Connexion à la base de données</a></li>
    </ul>
</body>
<head>
    <title>Carte Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <style>
        #map { height: 500px; width: 100%; }
    </style>
</head>
<body>
    <h1>Carte des toponymies</h1>
    <div id="map"></div>
    <script>
        // Initialiser la carte
        var map = L.map('map').setView([47.0, 5.0], 8); // Coordonnées approximatives pour le département 21

        // Ajouter un fond de carte
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19
        }).addTo(map);

        // Charger les données GeoJSON depuis map.php
        fetch('map.php')
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data).addTo(map);
            })
            .catch(error => console.error('Erreur:', error));
    </script>
</body>
<head>
    <meta charset="utf-8">
    <title>Carte Interactive avec Leaflet</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha384-n6LZ5L8CnqEmJiw9L3eyT+Me7UzvWSN9lEKxTkhuyvtBDEZvWxvdSlwx3k0zDAn5" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha384-AyXGp8M5lIOFTNEBvoTivOpzFUMvZtTdU/0MHiJ6MeQlH0FnjFUVRXf37J0CB8fX" crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/proj4js/2.8.0/proj4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/proj4leaflet@1.0.2/src/proj4leaflet.js"></script>
    <style>
        #map {
            height: 100vh;
        }
        .filter-container {
            position: absolute;
            top: 10px;
            left: 10px;
            z-index: 1000;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="filter-container">
        <label for="typeFilter">Filtrer par type :</label>
        <select id="typeFilter">
            <option value="all">Tous</option>
            <option value="type1">Type 1</option>
            <option value="type2">Type 2</option>
        </select>
    </div>
    <div id="map"></div>

    <script>
        // Configuration de la projection Lambert 93
        const crs = new L.Proj.CRS(
            'EPSG:2154',
            '+proj=lcc +lat_1=49.000000000 +lat_2=44.000000000 +lat_0=46.500000000 +lon_0=3.000000000 +x_0=700000.000 +y_0=6600000.000 +ellps=GRS80 +towgs84=0,0,0 +units=m +no_defs',
            {
                resolutions: [8192, 4096, 2048, 1024, 512, 256, 128, 64, 32, 16, 8, 4, 2, 1],
                origin: [0, 12000000]
            }
        );

        // Initialisation de la carte
        const map = L.map('map', {
            crs: L.CRS.EPSG3857, // WGS84 par défaut
            center: [47, 2],
            zoom: 6
        });

        // Ajout de la couche OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Données (à remplacer par vos données GeoJSON)
        const geojsonData = {
            "type": "FeatureCollection",
            "features": [
                {
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [2.3522, 48.8566] // Coordonnées en WGS84 (Paris)
                    },
                    "properties": {
                        "name": "Point 1",
                        "type": "type1"
                    }
                },
                {
                    "type": "Feature",
                    "geometry": {
                        "type": "Point",
                        "coordinates": [4.8357, 45.7640] // Coordonnées en WGS84 (Lyon)
                    },
                    "properties": {
                        "name": "Point 2",
                        "type": "type2"
                    }
                }
            ]
        };

        // Fonction de style
        function getMarkerStyle(feature) {
            return {
                radius: 8,
                fillColor: feature.properties.type === 'type1' ? 'blue' : 'green',
                color: '#000',
                weight: 1,
                opacity: 1,
                fillOpacity: 0.8
            };
        }

        // Ajout des points GeoJSON
        let geojsonLayer = L.geoJSON(geojsonData, {
            pointToLayer: function (feature, latlng) {
                return L.circleMarker(latlng, getMarkerStyle(feature));
            },
            onEachFeature: function (feature, layer) {
                layer.bindPopup(`<strong>${feature.properties.name}</strong><br>Type: ${feature.properties.type}`);
            }
        }).addTo(map);

        // Gestion du filtre
        document.getElementById('typeFilter').addEventListener('change', function (e) {
            const selectedType = e.target.value;
            map.eachLayer(function (layer) {
                if (layer !== geojsonLayer && layer instanceof L.CircleMarker) {
                    map.removeLayer(layer);
                }
            });
            geojsonLayer = L.geoJSON(geojsonData, {
                filter: function (feature) {
                    return selectedType === 'all' || feature.properties.type === selectedType;
                },
                pointToLayer: function (feature, latlng) {
                    return L.circleMarker(latlng, getMarkerStyle(feature));
                },
                onEachFeature: function (feature, layer) {
                    layer.bindPopup(`<strong>${feature.properties.name}</strong><br>Type: ${feature.properties.type}`);
                }
            }).addTo(map);
        });
    </script>
</body>
</html>
