<?php
include_once __DIR__.'/../model/correction1.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'create') {
        Correction::create($_POST['id_cor'], $_POST['image2'], $_POST['remarque'], $_POST['note']);
        header('Location: ../backoffice/corection1.php');
    } elseif ($action == 'update') {
        Correction::update($_POST['id_cor'], $_POST['image2'], $_POST['remarque'], $_POST['note']);
        header('Location: ../backoffice/corection1.php');
    } elseif ($action == 'delete') {
        Correction::delete($_POST['id_cor']);
        header('Location: ../backoffice/corection1.php');
    }
}
?>


public function create($id_cor, $image2, $remarque, $note, $id_exam) {
    $sql = "INSERT INTO correction1 (id_cor, image2, remarque, note, id_exam) VALUES (?, ?, ?, ?, ?)";
    $stmt = $this->conn->prepare($sql);

    if ($stmt === false) {
        return "Error: " . $this->conn->errorInfo();
    }

    $stmt->bindValue(1, $id_cor, PDO::PARAM_INT);
    $stmt->bindValue(2, $image2, PDO::PARAM_STR);
    $stmt->bindValue(3, $remarque, PDO::PARAM_STR);
    $stmt->bindValue(4, $note, PDO::PARAM_STR);
    $stmt->bindValue(5, $id_exam, PDO::PARAM_INT);

    $result = $stmt->execute();

    return $result ? true : "Error: " . $this->conn->errorInfo();
}
