<?php
include_once __DIR__.'/../model/correction1.php';

if (isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action == 'create') {
        Correction::create($_POST['id_cor'], $_POST['id_exam'], $_POST['image2'], $_POST['remarque'], $_POST['note']);
        header('Location: ../backoffice/corection1.php');
    } elseif ($action == 'update') {
        Correction::update($_POST['id_cor'], $_POST['id_exam'], $_POST['image2'], $_POST['remarque'], $_POST['note']);
        header('Location: ../backoffice/corection1.php');
    } elseif ($action == 'delete') {
        Correction::delete($_POST['id_cor']);
        header('Location: ../backoffice/corection1.php');
    }
}
?>

