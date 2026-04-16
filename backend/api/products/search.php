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
require_once '../config/database.php';
require_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$query = isset($_GET['q']) ? $_GET['q'] : '';

if(strlen($query) < 2) {
    echo json_encode(array());
    exit;
}

$product = new Product($db);

 $stmt = $product->searchArticles($query);
$products_arr = array();

if($stmt) {
    while (odbc_fetch_row($stmt)) {
        $product_item = array(
            "ref" => odbc_result($stmt, 'ref'),
            "designation" => odbc_result($stmt, 'designation')
        );
        array_push($products_arr, $product_item);
    }

    http_response_code(200);

    echo json_encode($products_arr);
}
else {
    http_response_code(200);

    echo json_encode(array());
}
?>
