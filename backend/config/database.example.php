<?php
class Database {
    private $host = "Srvapp";  
    private $db_name = "BD_STAGE";  
    private $username = ""; // Your DB username
    private $password = ""; // Your DB password
    private $conn;

    public function getConnection() {
        // Build DSN string using instance properties
        $dsn = "DRIVER={SQL Server};SERVER={$this->host};Database={$this->db_name};";
        // Attempt connection
        $conn = odbc_connect($dsn, $this->username, $this->password);
        if (!$conn) {
            $errorMsg = odbc_errormsg();
            throw new Exception("Erreur ODBC : {$errorMsg} | DSN: {$dsn}");
        }
        return $conn;
    }
}
?>