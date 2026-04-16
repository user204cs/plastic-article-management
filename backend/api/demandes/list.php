<?php
// API Officielle pour lister les demandes
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

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
            'message' => 'Erreur fatale du serveur.',
            'error_details' => [
                'type' => $error['type'],
                'message' => $error['message'],
                'file' => $error['file'],
                'line' => $error['line'],
            ],
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $host = "Srvapp";
    $db_name = "BD_STAGE";
    $username = "stagiaire";
    $password = "STAGE2025";
    
    $dsn = "DRIVER={SQL Server};SERVER={$host};Database={$db_name};";
    $conn = odbc_connect($dsn, $username, $password);
    
    if (!$conn) {
        throw new Exception('ODBC connection failed: ' . odbc_errormsg());
    }
    
    // MODIFICATION : Ajout de la colonne 'couleur'
    $sql = "SELECT TOP 100 
                d.id,
                d.AR_ref AS reference,
                d.type_demande,
                d.AR_Design,
                d.AR_Design_Duplication,
                d.statut,
                d.created_at,
                d.couleur,
                COALESCE(u.nom, 'Utilisateur inconnu') AS demandeur_nom
            FROM BD_STAGE.dbo.demandes d
            LEFT JOIN BD_STAGE.dbo.users u ON d.demandeur = u.id
            ORDER BY d.created_at DESC";
    
    $stmt = odbc_prepare($conn, $sql);
    if (!$stmt) {
        throw new Exception('ODBC prepare failed: ' . odbc_errormsg($conn));
    }
    
    if (!odbc_execute($stmt)) {
        throw new Exception('ODBC execute failed: ' . odbc_errormsg($conn));
    }
    
    $records = array();
    
    while ($row = odbc_fetch_array($stmt)) {
        // Correction de l'encodage
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = mb_convert_encoding($value, 'UTF-8', 'ISO-8859-1');
            }
        }

        // MODIFICATION : 'nom' affiche toujours AR_Design
        $nomProduit = $row['AR_Design'] ? $row['AR_Design'] : 'Nom non disponible';
        
        $type = ((int)$row['type_demande'] === 0 ? 'Archive' : 'Duplication');
        $status = $row['statut'] === 'en_attente' ? 'pending'
                : ($row['statut'] === 'valide' ? 'validated' : 'rejected');
        
        $records[] = array(
            'id' => (int)$row['id'],
            'reference' => $row['reference'] ? $row['reference'] : 'REF-' . $row['id'],
            'demandeur' => $row['demandeur_nom'],
            'nom' => $nomProduit,
            'couleur' => $row['couleur'] ? $row['couleur'] : 'N/A', // MODIFICATION : Ajout de la couleur
            'type' => $type,
            'status' => $status,
            'created_at' => $row['created_at']
        );
    }
    
    odbc_free_result($stmt);
    odbc_close($conn);
    
    $json_output = json_encode(array(
        "success" => true,
        "records" => $records,
        "count" => count($records),
        "message" => "Demandes récupérées avec succès"
    ));

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Erreur d\'encodage JSON: ' . json_last_error_msg());
    }
    
    echo $json_output;
    
} catch (Exception $e) {
    if (!headers_sent()) {
        http_response_code(500);
    }
    echo json_encode(array(
        "success" => false,
        "error" => $e->getMessage(),
        "records" => array(),
        "message" => "Erreur lors de la récupération des demandes"
    ));
}
?>