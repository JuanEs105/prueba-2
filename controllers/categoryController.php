<?php
require_once 'models/entities/category.php';
require_once 'models/entities/bill.php';

class CategoryController {
    private $categoryModel;
    private $billModel;

    public function __construct() {
        $this->categoryModel = new Category();
        $this->billModel = new Bill();
    }

    public function index() {
        // Obtener todas las categorías
        $categories = $this->categoryModel->getAll();
        
        // Cargar la vista de categorías
        include 'views/form_category.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $percentage = $_POST['percentage'];
            
            // Validar que el porcentaje sea mayor que cero y menor o igual a 100
            if ($percentage <= 0 || $percentage > 100) {
                echo "El porcentaje debe ser mayor que cero y no superar el 100%";
                return;
            }
            
            // Crear la categoría
            $result = $this->categoryModel->create($name, $percentage);
            
            if ($result) {
                header('Location: index.php?controller=category');
            } else {
                echo "Error al crear la categoría";
            }
        } else {
            // Mostrar formulario para crear categoría
            include 'views/form_category.php';
        }
    }

    public function edit() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=category');
            return;
        }
        
        $categoryId = $_GET['id'];
        
        // Obtener la categoría específica
        $category = $this->categoryModel->getById($categoryId);
        
        if (!$category) {
            echo "La categoría no existe";
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'];
            $percentage = $_POST['percentage'];
            
            // Validar que el porcentaje sea mayor que cero y menor o igual a 100
            if ($percentage <= 0 || $percentage > 100) {
                echo "El porcentaje debe ser mayor que cero y no superar el 100%";
                return;
            }
            
            // Actualizar la categoría
            $result = $this->categoryModel->update($categoryId, $name, $percentage);
            
            if ($result) {
                header('Location: index.php?controller=category');
            } else {
                echo "Error al actualizar la categoría";
            }
        } else {
            // Mostrar formulario para editar categoría
            include 'views/form_category.php';
        }
    }

    public function delete() {
        if (!isset($_GET['id'])) {
            header('Location: index.php?controller=category');
            return;
        }
        
        $categoryId = $_GET['id'];
        
        // Verificar si la categoría está asociada a algún gasto
        $billsWithCategory = $this->billModel->getByCategory($categoryId);
        
        if (count($billsWithCategory) > 0) {
            echo "No se puede eliminar la categoría porque está asociada a gastos";
            return;
        }
        
        // Eliminar la categoría
        $result = $this->categoryModel->delete($categoryId);
        
        if ($result) {
            header('Location: index.php?controller=category');
        } else {
            echo "Error al eliminar la categoría";
        }
    }
}
?>