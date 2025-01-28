<?php
$host = "10.0.3.15";
$port = "5433";
$dbname = "carto_db";
$user = "postgres";
$password = "postgres";

// Connexion à PostgreSQL
$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Erreur de connexion : " . pg_last_error());
}
// Requête pour récupérer les données GeoJSON
$query = "SELECT ST_AsGeoJSON(geom) AS geojson FROM your_table_name";
$result = pg_query($conn, $query);

if (!$result) {
    die("Erreur dans la requête : " . pg_last_error());
}
$features = [];
while ($row = pg_fetch_assoc($result)) {
    $geojson = json_decode($row['geojson'], true);
    $features[] = [
        "type" => "Feature",
        "geometry" => $geojson,
        "properties" => [] // Ajoutez ici les propriétés si nécessaire
    ];
}
echo json_encode([
    "type" => "FeatureCollection",
    "features" => $features
]);

pg_close($conn);
?>
