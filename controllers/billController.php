<?php
require_once 'models/entities/bill.php';
require_once 'models/entities/report.php';
require_once 'models/entities/category.php';

class BillController {
    private $billModel;
    private $reportModel;
    private $categoryModel;

    public function __construct() {
        $this->billModel = new Bill();
        $this->reportModel = new Report();
        $this->categoryModel = new Category();
    }

    public function index() {
        // Obtener todos los gastos
        $bills = $this->billModel->getAll();
        
        // Obtener todas las categorías y reportes para el formulario
        $categories = $this->categoryModel->getAll();
        $reports = $this->reportModel->getAll();
        
        // Cargar la vista de gastos
        include 'views/bill/create.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $value = $_POST['value'];
            $categoryId = $_POST['idCategory'];
            $reportId = $_POST['idReport'];
            
            // Validar que el valor sea mayor que cero
            if ($value <= 0) {
                echo "El valor del gasto debe ser mayor que cero";
                return;
            }
            
            // Verificar que la categoría exista
            $category = $this->categoryModel->getById($categoryId);
            if (!$category) {
                echo "La categoría seleccionada no existe";
                return;
            }
            
            // Verificar que el reporte exista
            $report = $this->reportModel->getById($reportId);
            if (!$report) {
                echo "El reporte seleccionado no existe";
                return;
            }
            
            // Crear el gasto
            $result = $this->billModel->create($value, $categoryId, $reportId);
            
            if ($result) {
                header('Location: index.php?controller=report&action=view&id=' . $reportId);
            } else {
                echo "Error al crear el gasto";
            }
        } else {
            // Obtener todas las categorías y reportes para el formulario
            $categories = $this->categoryModel->getAll();
            $reports = $this->reportModel->getAll();
            
            // Mostrar formulario para crear gasto
            include 'views/bill/create.php';
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=bill');
            return;
        }
        
        $billId = $_GET['id'];
        
        // Obtener el gasto específico
        $bill = $this->billModel->getById($billId);
        
        if (!$bill) {
            echo "El gasto no existe";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $value = $_POST['value'];
            $categoryId = $_POST['idCategory'];
            
            // Validar que el valor sea mayor que cero
            if ($value <= 0) {
                echo "El valor del gasto debe ser mayor que cero";
                return;
            }
            
            // Verificar que la categoría exista
            $category = $this->categoryModel->getById($categoryId);
            if (!$category) {
                echo "La categoría seleccionada no existe";
                return;
            }
            
            // Actualizar el gasto (solo valor y categoría, no se puede cambiar el reporte)
            $result = $this->billModel->update($billId, $value, $categoryId);
            
            if ($result) {
                header('Location: index.php?controller=report&action=view&id=' . $bill['idReport']);
            } else {
                echo "Error al actualizar el gasto";
            }
        } else {
            // Obtener todas las categorías para el formulario
            $categories = $this->categoryModel->getAll();
            
            // Mostrar formulario para editar gasto
            include 'views/bill/create.php';
        }
    }

    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=bill');
            return;
        }
        
        $billId = $_GET['id'];
        
        // Obtener el gasto para conocer su reporte asociado
        $bill = $this->billModel->getById($billId);
        
        if (!$bill) {
            echo "El gasto no existe";
            return;
        }
        
        // Eliminar el gasto
        $result = $this->billModel->delete($billId);
        
        if ($result) {
            header('Location: index.php?controller=report&action=view&id=' . $bill['idReport']);
        } else {
            echo "Error al eliminar el gasto";
        }
    }
}
?>