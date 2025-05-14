<?php
require_once(__DIR__ . "/../../../../controller/typeexamcontroller.php");
header('Content-Type: application/json');

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $controller = new TypeExamController();
    $controller->deleteTypeExamById($id);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'ID manquant']);
}
