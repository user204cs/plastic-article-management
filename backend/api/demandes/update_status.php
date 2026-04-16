<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Fonction de débogage pour capturer les erreurs fatales
register_shutdown_function('shutdown_function');
function shutdown_function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: application/json; charset=UTF-8');
        }
        echo json_encode([
            'success' => false,
            'message' => 'Erreur fatale du serveur lors de la mise à jour.',
            'error_details' => $error,
        ]);
    }
}

$data = json_decode(file_get_contents("php://input"));

if (empty($data->id) || empty($data->status)) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Données incomplètes. ID ou statut manquant."
    ]);
    exit();
}

try {
    // Connexion directe et fiable
    $host = "Srvapp";
    $db_name = "BD_STAGE";
    $username = "stagiaire";
    $password = "STAGE2025";
    $dsn = "DRIVER={SQL Server};SERVER={$host};Database={$db_name};";
    $conn = odbc_connect($dsn, $username, $password);

    if (!$conn) {
        throw new Exception('La connexion ODBC a échoué.');
    }

    // Étape 1: Récupérer les informations de la demande AVANT la mise à jour
    $type_demande = null;
    $ar_ref = null;
    $query_select = "SELECT type_demande, AR_ref FROM BD_STAGE.dbo.demandes WHERE id = ?";
    $stmt_select = odbc_prepare($conn, $query_select);
    if (odbc_execute($stmt_select, [$data->id])) {
        if (odbc_fetch_row($stmt_select)) {
            $type_demande = odbc_result($stmt_select, 'type_demande');
            $ar_ref = odbc_result($stmt_select, 'AR_ref');
        }
    }
    odbc_free_result($stmt_select);


    // Étape 2: Mettre à jour le statut de la demande
    $db_status = '';
    switch ($data->status) {
        case 'validated':
            $db_status = 'valide';
            break;
        case 'rejected':
            $db_status = 'rejete';
            break;
        case 'pending':
            $db_status = 'en_attente';
            break;
        default:
            throw new Exception('Statut invalide fourni.');
    }

    $query_update = "UPDATE BD_STAGE.dbo.demandes SET statut = ? WHERE id = ?";
    $stmt_update = odbc_prepare($conn, $query_update);

    if (!$stmt_update) {
        throw new Exception('La préparation de la requête de mise à jour a échoué: ' . odbc_errormsg($conn));
    }

    $success = odbc_execute($stmt_update, [$db_status, $data->id]);

    if ($success) {
        // Étape 3: Si la demande est une "Archive" validée, mettre à jour F_ARTICLE
        if ($type_demande == 0 && $data->status === 'validated' && !empty($ar_ref)) {
            $query_archive = "UPDATE G_SIMECTEST2.dbo.F_ARTICLE SET AR_SOMMEIL = 1 WHERE AR_Ref = ?";
            $stmt_archive = odbc_prepare($conn, $query_archive);
            // On ne bloque pas le succès de la première requête si celle-ci échoue, mais on pourrait logguer l'erreur
            odbc_execute($stmt_archive, [$ar_ref]);
            odbc_free_result($stmt_archive);
        }

        http_response_code(200);
        echo json_encode([
            "success" => true,
            "message" => "Statut mis à jour avec succès."
        ]);
    } else {
        throw new Exception('L\'exécution de la requête de mise à jour a échoué: ' . odbc_errormsg($conn));
    }

    odbc_free_result($stmt_update);
    odbc_close($conn);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur lors de la mise à jour du statut.",
        "error" => $e->getMessage()
    ]);
}
?>
