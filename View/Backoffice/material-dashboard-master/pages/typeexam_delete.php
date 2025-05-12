<?php
require_once(__DIR__ . "/../../../../controller/TypeExamController.php");

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    $controller = new TypeExamController();
    $deleteResult = $controller->deleteTypeExam($id);

    if ($deleteResult) {
        header("Location: typeexam.php?delete=success");
        exit();
    } else {
        header("Location: typeexam.php?delete_error=" . urlencode("Erreur lors de la suppression du type avec l'ID : " . $id));
        exit();
    }
} else {
    header("Location: typeexam.php?delete_error=" . urlencode("ID non spécifié."));
    exit();
}
?>