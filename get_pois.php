<?php
header('Content-Type: application/json; charset=utf-8');

require_once 'db_config.php';

try {
    $sql = "SELECT id, name, category, latitude, longitude, description FROM poi";
    $stmt = $pdo->query($sql);
    $pois = $stmt->fetchAll();

    echo json_encode($pois);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Query failed'
    ]);
}
