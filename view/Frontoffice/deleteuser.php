<?php
include '../../controller/UserController.php';

// Create an instance of the UserController
$userController = new UserController();

// Check if the 'id' parameter exists in the URL
if (isset($_GET['id'])) {
    // Get the id of the user to delete
    $id_user = $_GET['id'];

    // Call the deleteUser method to delete the user
    $deleteSuccess = $userController->deleteUser($id_user);

    // After deletion, redirect to the list page
    if ($deleteSuccess) {
        header('Location: userList.php'); // Redirect to the user list page after successful deletion
    } else {
        echo "Error deleting user."; // Display an error message if deletion fails
    }
} else {
    echo "Invalid ID."; // If no ID is provided, show an error message
}
?>
