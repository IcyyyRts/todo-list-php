<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['remove'])) {
    $index = (int) $_GET['remove'];

    if (isset($_SESSION['completed'][$index])) {
        unset($_SESSION['completed'][$index]);
        $_SESSION['completed'] = array_values($_SESSION['completed']); // Re-index array
    }
}

header("Location: index.php");
exit();
?>
