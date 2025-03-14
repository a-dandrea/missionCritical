<?php
session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}

$popupMessage = ''; // Message for JavaScript pop-up

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $username = trim($_POST['username']);
    $description = trim($_POST['description']);  // Optional group description

    if (!empty($username)) {
        try {
            $db->beginTransaction();

            // Insert new group
            $insertGroupSql = "INSERT INTO groups (username, description) VALUES (:username, :description)";
            $stmt = $db->prepare($insertGroupSql);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':description', $description);
            $stmt->execute();

            $group_id = $db->lastInsertId();

            $db->commit();
            $popupMessage = "Group '$username' created successfully! Group ID: $group_id";
        } catch (Exception $e) {
            $db->rollBack();
            $message = "Error creating group: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a valid username.";
    }
}

// Handle user joining a group by Group ID
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_group'])) {
    $group_id = trim($_POST['group_id']);
    $user_id = $_SESSION['user_id'] ?? null; // Assume user is logged in and their ID is stored in session

    if (!empty($group_id) && filter_var($group_id, FILTER_VALIDATE_INT) && $user_id) {
        try {
            $joinGroupSql = "INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)";
            $stmt = $db->prepare($joinGroupSql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->execute();

            $popupMessage = "Successfully joined group with ID: $group_id";
        } catch (PDOException $e) {
            $message = "Error joining group: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a valid Group ID.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create or Join Group</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0e6f6;
            color: #4b0082;
        }
        .form-container {
            background-color: #d8b9f2;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            margin: 50px auto;
        }
        .submit-btn {
            background-color: #7d3c98;
            color: #ffffff;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #5a2e7e;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Create a New Group</h2>

        <?php if (!empty($message)) : ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username (Group Name):</label>
            <input type="text" id="username" name="username" required>
            <br>
            <label for="description">Description (optional):</label>
            <textarea id="description" name="description"></textarea>
            <br>
            <button type="submit" name="create_group" class="submit-btn">Create Group</button>
        </form>

        <hr>

        <h2>Join a Group</h2>
        <form method="POST">
            <label for="group_id">Enter Group ID:</label>
            <input type="text" id="group_id" name="group_id" required>
            <br>
            <button type="submit" name="join_group" class="submit-btn">Join Group</button>
        </form>
    </div>

    <?php if (!empty($popupMessage)) : ?>
        <script>
            alert("<?php echo $popupMessage; ?>");
        </script>
    <?php endif; ?>
</body>
</html>

