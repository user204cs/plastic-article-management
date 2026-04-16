<?php
class Product {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function searchArticles($query) {
        // Utilise le nom complet de la table pour accéder à une autre base de données
        // Filtre seulement les produits finis (références commençant par "5")
        $search_query = "SELECT AR_Ref AS ref, AR_Design AS designation
                         FROM [G_SIMECTEST2].[dbo].[F_ARTICLE]
                         WHERE AR_Design LIKE ?  AND AR_Ref LIKE '5%'
                         ORDER BY AR_Design ASC";

        $stmt = odbc_prepare($this->conn, $search_query);
        $search_term = "%" . htmlspecialchars(strip_tags($query)) . "%";
        
        if (!odbc_execute($stmt, [$search_term])) {
            return false;
        }
        return $stmt;
    }
}
?>
