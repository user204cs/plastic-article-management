<?php
require_once '../config/database.php';
require_once '../classes/Product.php';

$database = new Database();
$db = $database->getConnection();

$product = new Product($db);

$stmt = $product->read();
$products_arr = array();

if($stmt) {
    while (odbc_fetch_row($stmt)) {
        $product_item = array(
            "id" => odbc_result($stmt, 'id'),
            "name" => odbc_result($stmt, 'name'),
            "reference" => odbc_result($stmt, 'reference'),
            "description" => odbc_result($stmt, 'description'),
            "category" => odbc_result($stmt, 'category'),
            "created_at" => odbc_result($stmt, 'created_at')
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
