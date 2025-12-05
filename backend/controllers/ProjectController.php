<?php
include_once '../config/database.php';
include_once '../models/Project.php';

class ProjectController {
    private $db;
    private $project;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->project = new Project($this->db);
    }

    public function list($pedania, $actividad) {
        $stmt = $this->project->read($pedania, $actividad);
        $num = $stmt->rowCount();
        $projects_arr = [];

        if ($num > 0) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                array_push($projects_arr, $row);
            }
        }
        echo json_encode($projects_arr);
    }

    public function readOne($id) {
        $this->project->id = $id;
        if ($this->project->readOne()) {
            $project_arr = array(
                "id" => $this->project->id,
                "titulo" => $this->project->titulo,
                "descripcion" => $this->project->descripcion,
                "pedania_id" => $this->project->pedania_id,
                "pedania_nombre" => $this->project->pedania_nombre,
                "actividad" => $this->project->actividad,
                "duracion" => $this->project->duracion,
                "frecuencia" => $this->project->frecuencia,
                "material_voluntario" => $this->project->material_voluntario,
                "material_organizacion" => $this->project->material_organizacion,
                "punto_encuentro" => $this->project->punto_encuentro,
                "transporte" => $this->project->transporte,
                "notas_importantes" => $this->project->notas_importantes,
                "imagen_url" => $this->project->imagen_url
            );
            echo json_encode($project_arr);
        } else {
            http_response_code(404);
            echo json_encode(["message" => "Proyecto no encontrado."]);
        }
    }

    public function create($data) {
        $this->project->titulo = $data['title'];
        $this->project->descripcion = $data['description'];
        $this->project->pedania_id = $data['pedaniaId'];
        $this->project->pedania_nombre = $data['pedania'];
        $this->project->actividad = $data['activity'];
        $this->project->duracion = $data['duration'];
        $this->project->frecuencia = $data['frequency'];
        $this->project->material_voluntario = implode("\n", $data['vol_material']);
        $this->project->material_organizacion = implode("\n", $data['org_material']);
        $this->project->punto_encuentro = $data['meeting'];
        $this->project->transporte = $data['transport'];
        $this->project->notas_importantes = $data['notes'];
        $this->project->imagen_url = isset($data['imagen_url']) ? $data['imagen_url'] : 'https://images.unsplash.com/photo-1593113598332-cd288d649433?auto=format&fit=crop&q=80&w=1000';

        if ($this->project->create()) {
            echo json_encode(["success" => true, "message" => "Proyecto creado."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo crear."]);
        }
    }

    public function update($data) {
        $this->project->id = $data['id'];
        $this->project->titulo = $data['title'];
        $this->project->descripcion = $data['description'];
        $this->project->pedania_id = $data['pedaniaId'];
        $this->project->pedania_nombre = $data['pedania'];
        $this->project->actividad = $data['activity'];
        $this->project->duracion = $data['duration'];
        $this->project->frecuencia = $data['frequency'];
        $this->project->material_voluntario = is_array($data['vol_material']) ? implode("\n", $data['vol_material']) : $data['vol_material'];
        $this->project->material_organizacion = is_array($data['org_material']) ? implode("\n", $data['org_material']) : $data['org_material'];
        $this->project->punto_encuentro = $data['meeting'];
        $this->project->transporte = $data['transport'];
        $this->project->notas_importantes = $data['notes'];

        if ($this->project->update()) {
            echo json_encode(["success" => true, "message" => "Proyecto actualizado."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo actualizar."]);
        }
    }

    public function delete($id) {
        $this->project->id = $id;
        if ($this->project->delete()) {
            echo json_encode(["success" => true, "message" => "Proyecto eliminado."]);
        } else {
            echo json_encode(["success" => false, "message" => "No se pudo eliminar."]);
        }
    }
}
?>
