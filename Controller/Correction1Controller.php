<?php
include_once __DIR__.'/../model/correction1.php';

class Correction1Controller {
    private $correction;

    public function __construct($pdo) {
        $this->correction = new Correction1($pdo);
    }

    public function getAllCorrections() {
        return $this->correction->getAll();
    }

    public function getCorrection($id) {
        return $this->correction->getOne($id);
    }

    public function createCorrection($id_exam, $image2, $remarque, $note, $user_id = null) {
        return $this->correction->create($id_exam, $image2, $remarque, $note, $user_id);
    }

    public function updateCorrection($id_cor, $id_exam, $image2, $remarque, $note, $user_id = null) {
        return $this->correction->update($id_cor, $id_exam, $image2, $remarque, $note, $user_id);
    }

    public function deleteCorrection($id_cor) {
        return $this->correction->delete($id_cor);
    }

    public function getCorrectionsByUser($user_id) {
        return $this->correction->getAllByUserId($user_id);
    }

    public function getCorrectionByExamId($id_exam) {
        return $this->correction->getOneByExamId($id_exam);
    }
}

// Handle POST requests
if (isset($_POST['action'])) {
    $pdo = config::getConnexion();
    $controller = new Correction1Controller($pdo);
    $action = $_POST['action'];

    switch ($action) {
        case 'create':
            $result = $controller->createCorrection(
                $_POST['id_exam'],
                $_POST['image2'],
                $_POST['remarque'],
                $_POST['note'],
                $_POST['user_id'] ?? null
            );
            header('Location: ../View/Backoffice/material-dashboard-master/pages/correction1.php?status=' . ($result ? 'success' : 'error'));
            break;

        case 'update':
            $result = $controller->updateCorrection(
                $_POST['id_cor'],
                $_POST['id_exam'],
                $_POST['image2'],
                $_POST['remarque'],
                $_POST['note'],
                $_POST['user_id'] ?? null
            );
            header('Location: ../View/Backoffice/material-dashboard-master/pages/correction1.php?status=' . ($result ? 'success' : 'error'));
            break;

        case 'delete':
            $result = $controller->deleteCorrection($_POST['id_cor']);
            header('Location: ../View/Backoffice/material-dashboard-master/pages/correction1.php?status=' . ($result ? 'success' : 'error'));
            break;
    }
    exit;
}
?>

