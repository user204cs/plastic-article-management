<?php
class Demande {
    private $conn;
    private $table_name = "demandes";

    public $id;
    public $reference;
    public $demandeur;
    public $code_racine;
    public $type_demande;  // 0 = archivage, 1 = duplication
    public $famille;
    public $designation;
    public $produit_source;
    public $statut;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                 (reference, demandeur, code_racine, type_demande, famille, designation, produit_source, statut) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = odbc_prepare($this->conn, $query);

        $this->reference = htmlspecialchars(strip_tags($this->reference));
        $this->demandeur = htmlspecialchars(strip_tags($this->demandeur));
        $this->code_racine = htmlspecialchars(strip_tags($this->code_racine));
        $this->type_demande = htmlspecialchars(strip_tags($this->type_demande));
        $this->famille = htmlspecialchars(strip_tags($this->famille));
        $this->designation = htmlspecialchars(strip_tags($this->designation));
        $this->produit_source = htmlspecialchars(strip_tags($this->produit_source));
        $this->statut = htmlspecialchars(strip_tags($this->statut));

        return odbc_execute($stmt, [
            $this->reference,
            $this->demandeur,
            $this->code_racine,
            $this->type_demande,
            $this->famille,
            $this->designation,
            $this->produit_source,
            $this->statut
        ]);
    }

    public function read() {
        $query = "SELECT d.id, d.reference, d.code_racine, d.type_demande, d.famille, 
                         d.designation, d.produit_source, d.statut, d.created_at, 
                         u.nom as demandeur_nom 
                  FROM " . $this->table_name . " d 
                  LEFT JOIN users u ON d.demandeur = u.id 
                  ORDER BY d.created_at DESC";

        return odbc_exec($this->conn, $query);
    }

    public function getAllDemandes() {
        $query = "SELECT d.id, d.reference, d.code_racine, d.type_demande, d.famille, 
                         d.designation, d.produit_source, d.statut, d.created_at, 
                         u.nom as demandeur_nom 
                  FROM " . $this->table_name . " d 
                  LEFT JOIN users u ON d.demandeur = u.id 
                  ORDER BY d.created_at DESC";

        $stmt = odbc_exec($this->conn, $query);
        $results = [];
        while (odbc_fetch_row($stmt)) {
            $results[] = [
                'id' => odbc_result($stmt, 'id'),
                'reference' => odbc_result($stmt, 'reference'),
                'code_racine' => odbc_result($stmt, 'code_racine'),
                'type_demande' => odbc_result($stmt, 'type_demande'),
                'famille' => odbc_result($stmt, 'famille'),
                'designation' => odbc_result($stmt, 'designation'),
                'produit_source' => odbc_result($stmt, 'produit_source'),
                'statut' => odbc_result($stmt, 'statut'),
                'created_at' => odbc_result($stmt, 'created_at'),
                'demandeur_nom' => odbc_result($stmt, 'demandeur_nom')
            ];
        }
        return $results;
    }

    public function readAll() {
        return $this->read();
    }

    public function updateStatus() {
        $query = "UPDATE " . $this->table_name . " SET statut = ? WHERE id = ?";

        $stmt = odbc_prepare($this->conn, $query);

        $this->statut = htmlspecialchars(strip_tags($this->statut));
        $this->id = htmlspecialchars(strip_tags($this->id));

        return odbc_execute($stmt, [$this->statut, $this->id]);
    }

    /*public function searchProducts($query) {
        $mock_products = [
            ['id' => 1, 'nom' => 'Sac Plastique Standard - 30x40cm', 'reference' => 'SP-30x40-001'],
            ['id' => 2, 'nom' => 'Film Plastique Bio - Rouleau 500m', 'reference' => 'FPB-500-001'],
            ['id' => 3, 'nom' => 'Conteneur Plastique Hermétique - 2L', 'reference' => 'CPH-2L-001'],
            ['id' => 4, 'nom' => 'Bac Plastique Alimentaire', 'reference' => 'BPA-001'],
            ['id' => 5, 'nom' => 'Emballage Biodégradable', 'reference' => 'EB-001']
        ];

        $results = [];
        foreach($mock_products as $product) {
            if(stripos($product['nom'], $query) !== false || stripos($product['reference'], $query) !== false) {
                $results[] = $product;
            }
        }
        
        return $results;
    }*/
}
?>
