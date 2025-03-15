<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['index']) && isset($_POST['updated_task'])) {
    $index = (int) $_POST['index'];
    $updated_task = trim($_POST['updated_task']);

    if (!empty($updated_task) && isset($_SESSION['tasks'][$index])) {
        $_SESSION['tasks'][$index] = $updated_task;
    }
}

header("Location: index.php");
exit();
?>
