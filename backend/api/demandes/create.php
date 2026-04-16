<?php
// Enable error display for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Gestion CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    exit();
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=UTF-8');

$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'Pas de données reçues ou JSON invalide']);
    exit;
}

try {
    require_once '../../config/database.php';
    require_once '../../classes/Demande.php';
    
    $database = new Database();
    $db = $database->getConnection();

    $type = isset($data['type']) ? intval($data['type']) : 0;
    $reference = $data['reference'] ?? '';

    // Pour l'archivage, le code racine est la référence complète.
    // Pour la duplication, il est extrait de la référence source.
    if ($type === 1) { // Duplication
        $sourceRef = $data['reference'] ?? '';
        $codeRacine = explode('-', $sourceRef)[0];
    } else { // Archivage
        $codeRacine = $reference;
    }
    
    $designation = '';
    $famille = '';
    $duplicationDesignation = '';
    if (!empty($codeRacine)) {
        $stmt = odbc_prepare($db,
            "SELECT A.AR_Design AS designation, F.FA_intitule AS famille
             FROM [G_SIMECTEST2].[dbo].[F_ARTICLE] A
             JOIN [G_SIMECTEST2].[dbo].[F_FAMILLE] F ON A.FA_CodeFamille = F.FA_CodeFamille
             WHERE A.AR_Ref = ?"
        );
        if (odbc_execute($stmt, [$codeRacine]) && odbc_fetch_row($stmt)) {
            $designation = odbc_result($stmt, 'designation');
            $famille = odbc_result($stmt, 'famille');
        }
    }
    // For archival requests, ensure designation and famille are non-empty
    if ($type === 0) {
        if (empty($designation)) {
            $designation = $reference;
        }
        if (empty($famille)) {
            $famille = '';
        }
    }
    // Pour duplication, on prend la nouvelle désignation passée
    if ($type === 1 && !empty($data['productName'])) {
        $duplicationDesignation = $data['productName'];
    } else if ($type === 1 && !empty($data['couleur']) && !empty($designation)) {
        // Pour duplication: nom_article-couleur (ex: PANIER-beige)
        $duplicationDesignation = $designation . '-' . $data['couleur'];
    } else {
        // Pour archivage, envoyer une chaîne vide plutôt que NULL
        $duplicationDesignation = ''; // empty string for archivage
    }
    
    $demandeurId = 1;
    if (!empty($data['demandeur'])) {
        $stmt = odbc_prepare($db, "SELECT id FROM users WHERE nom = ? OR login = ?");
        if (odbc_execute($stmt, [$data['demandeur'], $data['demandeur']]) && odbc_fetch_row($stmt)) {
            $demandeurId = intval(odbc_result($stmt, 'id'));
        }
    }

    // Déterminer couleur et GenererBF (for archive, use empty string instead of null)
    $couleur = ($type === 0) ? '' : (isset($data['couleur']) ? $data['couleur'] : '');
    $GenererBF = isset($data['GenererBF']) ? intval($data['GenererBF']) : 2;
    // Pour archivage, exécuter insertion directe pour éviter problèmes de préparation
    if ($type === 0) {
        $refEsc = str_replace("'", "''", $reference);
        $codeEsc = str_replace("'", "''", $codeRacine);
        $famEsc = str_replace("'", "''", $famille);
        $desEsc = str_replace("'", "''", $designation);
        $colEsc = str_replace("'", "''", $couleur);
        $sqlRaw = "INSERT INTO demandes (AR_ref, demandeur, code_racine, type_demande, famille, AR_Design, AR_Design_Duplication, statut, couleur, GenererBF, created_at) 
                   VALUES ('{$refEsc}', {$demandeurId}, '{$codeEsc}', 0, '{$famEsc}', '{$desEsc}', '', 'en_attente', '{$colEsc}', {$GenererBF}, GETDATE())";
        $resRaw = odbc_exec($db, $sqlRaw);
        if ($resRaw) {
            echo json_encode(['success' => true, 'message' => 'Demande d\'archivage créée avec succès']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Échec insertion direct', 'db_error' => odbc_errormsg($db), 'sql' => $sqlRaw]);
        }
        exit();
    }
    // Préparer l'insertion en incluant les nouvelles colonnes, created_at généré en base
    $sql = "INSERT INTO demandes 
            (AR_ref, demandeur, code_racine, type_demande, famille, AR_Design, AR_Design_Duplication, statut, couleur, GenererBF, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, GETDATE())";

    $stmt = odbc_prepare($db, $sql);
    // Diagnostic: check prepare success
    if ($stmt === false) {
        $dbError = odbc_errormsg($db);
        echo json_encode([
            'success' => false,
            'error' => 'Préparation de la requête INSERT échouée',
            'db_error' => $dbError,
            'sql' => $sql
        ]);
        exit();
    }
    $params = [
        $reference,
        $demandeurId,
        $codeRacine,
        $type,
        $famille,
        $designation,
        $duplicationDesignation,
        'en_attente',
        $couleur,
        $GenererBF
    ];
    $result = odbc_execute($stmt, $params);
    
    if ($result) {
        echo json_encode([
            'success' => true,
            'message' => 'Demande créée avec succès'
        ]);
    } else {
        $dbError = odbc_errormsg($db);
        // Diagnostic: include params to debug insertion failure
        echo json_encode([
            'success' => false,
            'error' => 'Échec insertion',
            'db_error' => $dbError,
            'params' => $params
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>