typeexam_delete.php :
<?php
require_once(__DIR__ . "/../../../../controller/TypeExamController.php");

if (isset($_GET['type'])) {
    $type = $_GET['type'];

    $controller = new TypeExamController();
    $controller->deleteTypeExam($type);

    header("Location: typeexam.php?delete=success");
    exit();
} else {
    echo "Type non spécifié.";
}
?>