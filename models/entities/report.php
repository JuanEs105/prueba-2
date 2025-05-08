<?php
require_once 'models/drivers/conexDB.php';

class Report {
    private $db;

    public function __construct() {
        $conexDb = new ConexDb();
        $this->db = $conexDb->getConexion();
    }

    public function getAll() {
        try {
            $query = "SELECT * FROM reports ORDER BY year DESC, CASE 
                    WHEN month = 'Enero' THEN 1
                    WHEN month = 'Febrero' THEN 2
                    WHEN month = 'Marzo' THEN 3
                    WHEN month = 'Abril' THEN 4
                    WHEN month = 'Mayo' THEN 5
                    WHEN month = 'Junio' THEN 6
                    WHEN month = 'Julio' THEN 7
                    WHEN month = 'Agosto' THEN 8
                    WHEN month = 'Septiembre' THEN 9
                    WHEN month = 'Octubre' THEN 10
                    WHEN month = 'Noviembre' THEN 11
                    WHEN month = 'Diciembre' THEN 12
                END DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener los reportes: " . $e->getMessage();
            return [];
        }
    }

    public function getById($id) {
        try {
            $query = "SELECT * FROM reports WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error al obtener el reporte: " . $e->getMessage();
            return null;
        }
    }

    public function exists($month, $year) {
        try {
            $query = "SELECT COUNT(*) FROM reports WHERE month = :month AND year = :year";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            echo "Error al verificar existencia del reporte: " . $e->getMessage();
            return false;
        }
    }

    public function create($month, $year) {
        try {
            $query = "INSERT INTO reports (month, year) VALUES (:month, :year)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':month', $month);
            $stmt->bindParam(':year', $year, PDO::PARAM_INT);
            $stmt->execute();
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            echo "Error al crear el reporte: " . $e->getMessage();
            return false;
        }
    }
}
?>