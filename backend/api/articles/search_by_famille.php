<?php
// Gestion CORS et pré-flight
if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit;
}
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        throw new Exception('Database connection failed');
    }
    
    $q = $_GET['q'] ?? '';
    $famille = $_GET['famille'] ?? '';
    
    if (strlen($q) < 2 || empty($famille)) {
        echo json_encode(['success' => true, 'data' => []]);
        exit;
    }
    
    // Test si F_ARTICLE existe
    $test_sql = "SELECT COUNT(*) as cnt FROM [G_SIMECTEST2].[dbo].[F_ARTICLE]";
    $test_stmt = odbc_exec($db, $test_sql);
    
    if (!$test_stmt) {
        throw new Exception('Table F_ARTICLE non accessible: ' . odbc_errormsg($db));
    }
    
    // Recherche des articles filtrés par famille dans G_SIMECTEST2
    $sql = "SELECT A.AR_Ref AS ref, A.AR_Design AS designation
            FROM [G_SIMECTEST2].[dbo].[F_ARTICLE] A
            WHERE A.FA_CodeFamille = ? 
            AND A.AR_Design LIKE ?  
            AND A.AR_Ref LIKE '5%'
            ORDER BY A.AR_Design ASC";
    
    $stmt = odbc_prepare($db, $sql);
    $search_term = "%" . htmlspecialchars(strip_tags($q)) . "%";
    
    if (!odbc_execute($stmt, [$famille, $search_term])) {
        throw new Exception('Recherche articles échouée: ' . odbc_errormsg($db));
    }
    
    $rows = [];
    while ($row = odbc_fetch_array($stmt)) {
        $rows[] = [
            'ref' => trim($row['ref']),
            'designation' => trim($row['designation'])
        ];
    }
    
    echo json_encode(['success' => true, 'data' => $rows]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
