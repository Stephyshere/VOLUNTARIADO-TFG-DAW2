<?php
class User {
    private $conn;
    private $table_name = "usuarios";

    public $id;
    public $email;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function emailExists() {
        $query = "SELECT id, password_hash, role FROM " . $this->table_name . " WHERE email = :email LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->id = $row['id'];
            $this->password = $row['password_hash']; // Hash from DB
            $this->role = $row['role'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET email=:email, password_hash=:password_hash, role='voluntario'";
        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->password = htmlspecialchars(strip_tags($this->password));
        $password_hash = password_hash($this->password, PASSWORD_DEFAULT);

        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":password_hash", $password_hash);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
