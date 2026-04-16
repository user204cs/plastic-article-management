<?php
class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $login;
    public $mdp;
    public $nom;
    public $profil;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function authenticate() {
        $query = "SELECT id, login, mdp, nom, profil FROM " . $this->table_name . " WHERE login = ?";
        
        $stmt = odbc_prepare($this->conn, $query);
        if (!odbc_execute($stmt, [$this->login])) {
            return false;
        }

        if (odbc_fetch_row($stmt)) {
            $stored_mdp = odbc_result($stmt, 'mdp');
            // Use plain text comparison since passwords are stored as plain text
            if($this->mdp === $stored_mdp) {
                $this->id = odbc_result($stmt, 'id');
                $this->login = odbc_result($stmt, 'login');
                $this->nom = odbc_result($stmt, 'nom');
                $this->profil = odbc_result($stmt, 'profil');
                return true;
            }
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " (login, mdp, nom, profil) VALUES (?, ?, ?, ?)";

        $stmt = odbc_prepare($this->conn, $query);

        $this->login = htmlspecialchars(strip_tags($this->login));
        $this->mdp = htmlspecialchars(strip_tags($this->mdp));
        $this->nom = htmlspecialchars(strip_tags($this->nom));
        $this->profil = htmlspecialchars(strip_tags($this->profil));

        $this->mdp = password_hash($this->mdp, PASSWORD_DEFAULT);

        return odbc_execute($stmt, [
            $this->login,
            $this->mdp,
            $this->nom,
            $this->profil
        ]);
    }
}
?>
