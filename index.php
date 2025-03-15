<?php
session_start();

// Initialize session variables if not set
if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

if (!isset($_SESSION['completed'])) {
    $_SESSION['completed'] = [];
}

// Handle adding a new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = trim($_POST['task']);
    if (!empty($task)) {
        $_SESSION['tasks'][] = $task;
    }
    header("Location: index.php");
    exit();
}

// Handle completing a task
if (isset($_GET['complete'])) {
    $index = (int) $_GET['complete'];
    if (array_key_exists($index, $_SESSION['tasks'])) {
        $_SESSION['completed'][] = $_SESSION['tasks'][$index];
        unset($_SESSION['tasks'][$index]);
        $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Re-index array
    }
    header("Location: index.php");
    exit();
}

// Handle deleting a task
if (isset($_GET['delete'])) {
    $index = (int) $_GET['delete'];
    if (array_key_exists($index, $_SESSION['tasks'])) {
        unset($_SESSION['tasks'][$index]);
        $_SESSION['tasks'] = array_values($_SESSION['tasks']); // Re-index array
    }
    header("Location: index.php");
    exit();
}

// Pagination settings
$tasksPerPage = 5;
$currentPageTasks = isset($_GET['tasks_page']) ? (int) $_GET['tasks_page'] : 1;
$currentPageCompleted = isset($_GET['completed_page']) ? (int) $_GET['completed_page'] : 1;

$totalTasks = count($_SESSION['tasks']);
$totalCompleted = count($_SESSION['completed']);

$totalTaskPages = ceil($totalTasks / $tasksPerPage);
$totalCompletedPages = ceil($totalCompleted / $tasksPerPage);

$taskStart = ($currentPageTasks - 1) * $tasksPerPage;
$completedStart = ($currentPageCompleted - 1) * $tasksPerPage;

$taskList = array_slice($_SESSION['tasks'], $taskStart, $tasksPerPage);
$completedList = array_slice($_SESSION['completed'], $completedStart, $tasksPerPage);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function enableEdit(index) {
            let taskText = document.getElementById('task-text-' + index);
            let editBtn = document.getElementById('edit-btn-' + index);

            if (taskText.contentEditable === "true") {
                taskText.contentEditable = "false";
                editBtn.textContent = "Edit";

                fetch("edit_task.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `index=${index}&new_task=${encodeURIComponent(taskText.innerText)}`
                });
            } else {
                taskText.contentEditable = "true";
                taskText.focus();
                editBtn.textContent = "Save";
            }
        }
    </script>
</head>
<body>
<div class="topnav">
    <h1>To-Do List</h1>
</div>

<form class="input-section" method="POST" action="add_task.php">
    <input type="text" name="task" placeholder="Enter a new task" required>
    <button type="submit" class="add-btn">Add Task</button>
</form>

<h3>Task List</h3>
<div class="task-list">
    <?php if (!empty($taskList)): ?>
        <?php foreach ($taskList as $index => $task): ?>
            <div class="task">
                <span id="task-text-<?php echo $index + $taskStart; ?>"><?php echo htmlspecialchars($task); ?></span>
                <div class="task-buttons">
                    <button id="edit-btn-<?php echo $index + $taskStart; ?>" class="edit-btn" onclick="enableEdit(<?php echo $index + $taskStart; ?>)">Edit</button>
                    <a href="?complete=<?php echo $index + $taskStart; ?>" class="complete-btn">Complete</a>
                    <a href="?delete=<?php echo $index + $taskStart; ?>" class="delete-btn">Delete</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty">No tasks available</p>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalTaskPages; $i++): ?>
        <a href="?tasks_page=<?php echo $i; ?><?php echo isset($_GET['completed_page']) ? '&completed_page=' . $_GET['completed_page'] : ''; ?>" 
           class="<?php echo ($i === $currentPageTasks) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

<h3>Completed Tasks</h3>
<div class="completed-tasks">
    <?php if (!empty($completedList)): ?>
        <?php foreach ($completedList as $index => $task): ?>
            <div class="task completed-task">
                <span><?php echo htmlspecialchars($task); ?></span>
                <a href="remove_task.php?remove=<?php echo $index + $completedStart; ?>" class="remove-btn">X</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="empty">No completed tasks</p>
    <?php endif; ?>
</div>

<div class="pagination">
    <?php for ($i = 1; $i <= $totalCompletedPages; $i++): ?>
        <a href="?completed_page=<?php echo $i; ?><?php echo isset($_GET['tasks_page']) ? '&tasks_page=' . $_GET['tasks_page'] : ''; ?>" 
           class="<?php echo ($i === $currentPageCompleted) ? 'active' : ''; ?>">
            <?php echo $i; ?>
        </a>
    <?php endfor; ?>
</div>

</body>
</html>
