<?php
require_once 'models/entities/income.php';
require_once 'models/entities/report.php';

class IncomeController {
    private $incomeModel;
    private $reportModel;

    public function __construct() {
        $this->incomeModel = new Income();
        $this->reportModel = new Report();
    }

    public function index() {
        // Obtener todos los ingresos
        $incomes = $this->incomeModel->getAll();
        
        // Obtener todos los reportes para el formulario
        $reports = $this->reportModel->getAll();
        
        // Cargar la vista de ingresos
        include 'views/form_income.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $value = $_POST['value'];
            $reportId = $_POST['idReport'];
            
            // Validar que el valor sea mayor que cero
            if ($value <= 0) {
                echo "El valor del ingreso debe ser mayor que cero";
                return;
            }
            
            // Verificar que el reporte exista
            $report = $this->reportModel->getById($reportId);
            if (!$report) {
                echo "El reporte seleccionado no existe";
                return;
            }
            
            // Verificar si ya existe un ingreso para este reporte
            $existingIncome = $this->incomeModel->getByReportId($reportId);
            if ($existingIncome) {
                echo "Ya existe un ingreso para este mes y año";
                return;
            }
            
            // Crear el ingreso
            $result = $this->incomeModel->create($value, $reportId);
            
            if ($result) {
                header('Location: index.php?controller=report&action=view&id=' . $reportId);
            } else {
                echo "Error al crear el ingreso";
            }
        } else {
            // Obtener todos los reportes para el formulario
            $reports = $this->reportModel->getAll();
            
            // Mostrar formulario para crear ingreso
            include 'views/form_income.php';
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=income');
            return;
        }
        
        $incomeId = $_GET['id'];
        
        // Obtener el ingreso específico
        $income = $this->incomeModel->getById($incomeId);
        
        if (!$income) {
            echo "El ingreso no existe";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $value = $_POST['value'];
            
            // Validar que el valor sea mayor que cero
            if ($value <= 0) {
                echo "El valor del ingreso debe ser mayor que cero";
                return;
            }
            
            // Actualizar solo el valor del ingreso (no se puede cambiar el mes ni el año)
            $result = $this->incomeModel->update($incomeId, $value);
            
            if ($result) {
                header('Location: index.php?controller=report&action=view&id=' . $income['idReport']);
            } else {
                echo "Error al actualizar el ingreso";
            }
        } else {
            // Mostrar formulario para editar ingreso
            include 'views/form_income.php';
        }
    }
}
?>