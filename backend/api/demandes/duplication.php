<?php
// Enable error display to debug 500 error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);

    if (!$data || !isset($data['demande_id'])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Paramètre demande_id manquant']);
        exit();
    }

    $demandeId = (int) $data['demande_id'];

    // Establish ODBC connection via Database class
    $database = new Database();
    $conn = $database->getConnection();

    // Récupérer les détails de la demande via ODBC
    $sqlDetail = "SELECT AR_ref AS code_racine, AR_Design_Duplication AS ar_design_duplication, couleur
                  FROM [dbo].[demandes]
                  WHERE id = {$demandeId}";
    // Exécuter requête de détails sans émettre de warnings
    $stmtDetail = @odbc_exec($conn, $sqlDetail);
    if ($stmtDetail === false) {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur récupération détails demande',
            'error' => odbc_errormsg($conn)
        ]);
        exit();
    }
    if (!odbc_fetch_row($stmtDetail)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Demande non trouvée']);
        exit();
    }
    $codeRacine = trim(odbc_result($stmtDetail, 'code_racine'));
    $arDesignDuplication = trim(odbc_result($stmtDetail, 'ar_design_duplication'));
    $couleur = trim(odbc_result($stmtDetail, 'couleur'));

    if (empty($arDesignDuplication)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Données de duplication manquantes pour cette demande']);
        exit();
    }

    // Appel de la procédure stockée via ODBC
    $sqlProc = "{CALL [dbo].[DupliArticleCode]('{$codeRacine}', '{$arDesignDuplication}', '{$couleur}')}";
    // Exécuter procédure avec ordre correct des paramètres (code_racine, couleur, AR_Design_Duplication)
    $correctedSqlProc = "{CALL [dbo].[DupliArticleCode]('{$codeRacine}', '{$couleur}', '{$arDesignDuplication}')}";
    $resultProc = @odbc_exec($conn, $correctedSqlProc);
    if ($resultProc !== false) {
        echo json_encode(['success' => true, 'message' => 'Procédure duplication exécutée avec succès']);
    } else {
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Erreur exécution procédure duplication',
            'error' => odbc_errormsg($conn)
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erreur serveur: ' . $e->getMessage()]);
}
