<?php
// Database connection
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

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $group_name = htmlspecialchars($_POST['group_name']);
    $selected_users = $_POST['selected_users'] ?? [];

    if (!empty($group_name) && !empty($selected_users)) {
        try {
            $db->beginTransaction();

            // Insert new group
            $insertGroupSql = "INSERT INTO groups (group_name) VALUES (:group_name)";
            $stmt = $db->prepare($insertGroupSql);
            $stmt->bindParam(':group_name', $group_name);
            $stmt->execute();

            $group_id = $db->lastInsertId();

            // Add selected users to the new group
            $insertUserGroupSql = "INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)";
            $stmt = $db->prepare($insertUserGroupSql);

            foreach ($selected_users as $user_id) {
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $db->commit();
            $message = "Group '$group_name' created successfully with selected users!";
        } catch (Exception $e) {
            $db->rollBack();
            $message = "Error creating group: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a group name and select at least one user.";
    }
}

// Display users
$userSql = "SELECT * FROM users";
$usersResult = $db->query($userSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Management</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Anonymous+Pro');

        body, html {
            height: 100%;
            background-color: #f3f8f2;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            background-color: #ffffff;
            border: 2px solid #a0cab0;
            border-radius: 10px;
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #4CAF50;
            font-size: 28px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        input[type="text"] {
            padding: 8px;
            border: 2px solid #a0cab0;
            border-radius: 5px;
            width: 100%;
        }

        .checkbox-container {
            text-align: left;
            padding: 10px 0;
            max-height: 150px;
            overflow-y: auto;
            border: 1px solid #a0cab0;
            border-radius: 5px;
        }

        .checkbox-item {
            margin-bottom: 5px;
        }

        button {
            background-color: #4CAF50;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .message {
            color: #4CAF50;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create a New Group</h1>

        <?php if (isset($message)): ?>
            <p class="message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="group_name">Group Name:</label>
            <input type="text" id="group_name" name="group_name" required>

            <label>Select Users:</label>
            <div class="checkbox-container">
                <?php while ($user = $usersResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="checkbox-item">
                        <input type="checkbox" name="selected_users[]" value="<?= $user['id'] ?>">
                        <?= htmlspecialchars($user['username']) ?>
                    </div>
                <?php endwhile; ?>
            </div>

            <button type="submit" name="create_group">Create Group</button>
        </form>
    </div>
</body>
</html>

