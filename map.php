<?php
require 'geoPHP.inc';

// Connexion à PostgreSQL
$conn = pg_connect("host=192.168.56.1 port=5433 dbname=carto_db user=postgres password=admin");

if (!$conn) {
    die("Erreur de connexion à la base de données.");
}

// Requête pour récupérer les données géométriques
$result = pg_query($conn, "SELECT gid, ST_AsGeoJSON(geom) AS geojson FROM topology.toponymie_services_21_wgs84");

if (!$result) {
    die("Erreur dans la requête.");
}

// Récupérer les données
$features = [];
while ($row = pg_fetch_assoc($result)) {
    $geometry = json_decode($row['geojson']);
    $feature = [
        "type" => "Feature",
        "geometry" => $geometry,
        "properties" => [
            "gid" => $row['gid']
        ]
    ];
    $features[] = $feature;
}

// Créer un GeoJSON
$geojson = [
    "type" => "FeatureCollection",
    "features" => $features
];

header('Content-Type: application/json');
echo json_encode($geojson);

pg_close($conn);
?>
