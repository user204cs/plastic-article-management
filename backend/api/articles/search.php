<?php
// Gestion CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    exit();
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

include_once __DIR__ . '/../../config/database.php';
include_once __DIR__ . '/../../classes/Product.php';

// Main logic with exception handling
try {
    $database = new Database();
    $db = $database->getConnection();
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    $product = new Product($db);
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    
    // Protection contre les requêtes trop courtes
    if (strlen($q) < 2) {
        echo json_encode(['success' => true, 'data' => []]);
        exit();
    }
    
    $stmt = $product->searchArticles($q);
    // Vérifier si la requête a échoué
    if ($stmt === false) {
        // Erreur ODBC claire pour débogage
        $odbcError = odbc_errormsg($db);
        throw new Exception('Recherche articles échouée: ' . $odbcError);
    }
    
    $rows = [];
    while ($row = odbc_fetch_array($stmt)) {
        $rows[] = [
            'ref' => $row['ref'],
            'designation' => $row['designation']
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $rows]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
