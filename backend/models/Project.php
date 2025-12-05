<?php
class Project {
    private $conn;
    private $table_name = "proyectos";

    // Properties matching DB columns
    public $id;
    public $titulo;
    public $descripcion;
    public $pedania_id;
    public $pedania_nombre;
    public $actividad;
    public $duracion;
    public $frecuencia;
    public $material_voluntario;
    public $material_organizacion;
    public $punto_encuentro;
    public $transporte;
    public $notas_importantes;
    public $imagen_url;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function read($pedania_filter = 'all', $actividad_filter = 'all') {
        $query = "SELECT * FROM " . $this->table_name . " WHERE 1=1";
        $params = [];

        if ($pedania_filter !== 'all') {
            $query .= " AND pedania_id = :pedania";
            $params[':pedania'] = $pedania_filter;
        }

        if ($actividad_filter !== 'all') {
            $query .= " AND actividad = :actividad";
            $params[':actividad'] = $actividad_filter;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt;
    }

    public function readOne() {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->titulo = $row['titulo'];
            $this->descripcion = $row['descripcion'];
            $this->pedania_id = $row['pedania_id'];
            $this->pedania_nombre = $row['pedania_nombre'];
            $this->actividad = $row['actividad'];
            $this->duracion = $row['duracion'];
            $this->frecuencia = $row['frecuencia'];
            $this->material_voluntario = $row['material_voluntario'];
            $this->material_organizacion = $row['material_organizacion'];
            $this->punto_encuentro = $row['punto_encuentro'];
            $this->transporte = $row['transporte'];
            $this->notas_importantes = $row['notas_importantes'];
            $this->imagen_url = $row['imagen_url'];
            return true;
        }
        return false;
    }

    public function create() {
        $query = "INSERT INTO " . $this->table_name . " SET
            titulo=:titulo, descripcion=:descripcion, pedania_id=:pedania_id, pedania_nombre=:pedania_nombre,
            actividad=:actividad, duracion=:duracion, frecuencia=:frecuencia, material_voluntario=:material_voluntario,
            material_organizacion=:material_organizacion, punto_encuentro=:punto_encuentro, transporte=:transporte,
            notas_importantes=:notas_importantes, imagen_url=:imagen_url";

        $stmt = $this->conn->prepare($query);

        // Sanitize and bind (omitted full sanitation for brevity, handled by PDO bind usually ok safely)
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":pedania_id", $this->pedania_id);
        $stmt->bindParam(":pedania_nombre", $this->pedania_nombre);
        $stmt->bindParam(":actividad", $this->actividad);
        $stmt->bindParam(":duracion", $this->duracion);
        $stmt->bindParam(":frecuencia", $this->frecuencia);
        $stmt->bindParam(":material_voluntario", $this->material_voluntario);
        $stmt->bindParam(":material_organizacion", $this->material_organizacion);
        $stmt->bindParam(":punto_encuentro", $this->punto_encuentro);
        $stmt->bindParam(":transporte", $this->transporte);
        $stmt->bindParam(":notas_importantes", $this->notas_importantes);
        $stmt->bindParam(":imagen_url", $this->imagen_url);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function update() {
        $query = "UPDATE " . $this->table_name . " SET
            titulo=:titulo, descripcion=:descripcion, pedania_id=:pedania_id, pedania_nombre=:pedania_nombre,
            actividad=:actividad, duracion=:duracion, frecuencia=:frecuencia, material_voluntario=:material_voluntario,
            material_organizacion=:material_organizacion, punto_encuentro=:punto_encuentro, transporte=:transporte,
            notas_importantes=:notas_importantes
            WHERE id=:id";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":titulo", $this->titulo);
        $stmt->bindParam(":descripcion", $this->descripcion);
        $stmt->bindParam(":pedania_id", $this->pedania_id);
        $stmt->bindParam(":pedania_nombre", $this->pedania_nombre);
        $stmt->bindParam(":actividad", $this->actividad);
        $stmt->bindParam(":duracion", $this->duracion);
        $stmt->bindParam(":frecuencia", $this->frecuencia);
        $stmt->bindParam(":material_voluntario", $this->material_voluntario);
        $stmt->bindParam(":material_organizacion", $this->material_organizacion);
        $stmt->bindParam(":punto_encuentro", $this->punto_encuentro);
        $stmt->bindParam(":transporte", $this->transporte);
        $stmt->bindParam(":notas_importantes", $this->notas_importantes);
        $stmt->bindParam(":id", $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function delete() {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $this->id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
?>
