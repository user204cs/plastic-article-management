<?php
// Gestion du CORS et pré-flight
if (
    isset($_SERVER['REQUEST_METHOD']) && 
    $_SERVER['REQUEST_METHOD'] === 'OPTIONS'
) {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

include_once __DIR__ . '/../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $q = $_GET['q'] ?? '';
    
    if (strlen($q) < 2) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    
    // Test si F_FAMILLE existe
    $test_sql = "SELECT COUNT(*) as cnt FROM [G_SIMECTEST2].[dbo].[F_FAMILLE]";
    $test_stmt = odbc_exec($db, $test_sql);
    
    if (!$test_stmt) {
        throw new Exception('Table F_FAMILLE non accessible: ' . odbc_errormsg($db));
    }
    
    // Recherche des familles dans G_SIMECTEST2
    $sql = "SELECT FA_CodeFamille AS code, FA_intitule AS intitule
            FROM [G_SIMECTEST2].[dbo].[F_FAMILLE]
            WHERE FA_intitule LIKE ?
            ORDER BY FA_intitule ASC";
    
    $stmt = odbc_prepare($db, $sql);
    $search_term = "%" . htmlspecialchars(strip_tags($q)) . "%";
    
    if (!odbc_execute($stmt, [$search_term])) {
        throw new Exception('Recherche familles échouée: ' . odbc_errormsg($db));
    }
    
    $rows = [];
    while ($row = odbc_fetch_array($stmt)) {
        $rows[] = [
            'code' => trim($row['code']),
            'intitule' => trim($row['intitule'])
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $rows]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
