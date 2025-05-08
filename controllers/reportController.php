<?php
require_once 'models/entities/report.php';
require_once 'models/entities/income.php';
require_once 'models/entities/bill.php';
require_once 'models/entities/category.php';

class ReportController {
    private $reportModel;
    private $incomeModel;
    private $billModel;
    private $categoryModel;

    public function __construct() {
        $this->reportModel = new Report();
        $this->incomeModel = new Income();
        $this->billModel = new Bill();
        $this->categoryModel = new Category();
    }

    public function index() {
        // Obtener todos los reportes
        $reports = $this->reportModel->getAll();
        
        // Cargar la vista de informes
        include 'views/reports.php';
    }

    public function view() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=report');
            return;
        }
        
        $reportId = $_GET['id'];
        
        // Obtener el reporte específico
        $report = $this->reportModel->getById($reportId);
        
        if (!$report) {
            echo "El reporte no existe";
            return;
        }
        
        // Obtener el ingreso asociado al reporte
        $income = $this->incomeModel->getByReportId($reportId);
        
        // Obtener los gastos asociados al reporte
        $bills = $this->billModel->getByReportId($reportId);
        
        // Obtener todas las categorías
        $categories = $this->categoryModel->getAll();
        
        // Calcular el total de gastos
        $totalBills = 0;
        foreach ($bills as $bill) {
            $totalBills += $bill['value'];
        }
        
        // Calcular el ahorro (ingreso - gastos)
        $savings = 0;
        if ($income) {
            $savings = $income['value'] - $totalBills;
        }
        
        // Calcular el porcentaje de ahorro
        $savingsPercentage = 0;
        if ($income && $income['value'] > 0) {
            $savingsPercentage = ($savings / $income['value']) * 100;
        }
        
        // Cargar la vista de reporte
        include 'views/form_report.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $month = $_POST['month'];
            $year = $_POST['year'];
            
            // Verificar si ya existe un reporte para este mes y año
            if ($this->reportModel->exists($month, $year)) {
                echo "Ya existe un reporte para este mes y año";
                return;
            }
            
            // Crear el reporte
            $reportId = $this->reportModel->create($month, $year);
            
            if ($reportId) {
                header('Location: index.php?controller=report&action=view&id=' . $reportId);
            } else {
                echo "Error al crear el reporte";
            }
        } else {
            // Mostrar formulario para crear reporte
            include 'views/form_report.php';
        }
    }
}
?>