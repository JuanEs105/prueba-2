<?php
require_once 'models/drivers/conexDB.php';

class Category {
    private $db;

    public function __construct() {
        $conexDb = new ConexDb();
        $this->db = $conexDb->getConexion();
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM categories ORDER BY name";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener las categorías: " . $e->getMessage();
            return [];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener la categoría: " . $e->getMessage();
            return null;
        }
    }

    public function create($name, $percentage) {
        try {
            $query = "INSERT INTO categories (name, percentage) VALUES (:name, :percentage)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':percentage', $percentage);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al crear la categoría: " . $e->getMessage();
            return false;
        }
    }

    public function update($id, $name, $percentage) {
        try {
            $query = "UPDATE categories SET name = :name, percentage = :percentage WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':percentage', $percentage);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error al actualizar la categoría: " . $e->getMessage();
            return false;
        }
    }

    public function delete($id) {
        try {
            $query = "DELETE FROM categories WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "Error al eliminar la categoría: " . $e->getMessage();
            return false;
        }
    }
}
?>