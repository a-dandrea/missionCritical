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

// Handle group creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
    $group_name = trim($_POST['group_name']);
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
                if (filter_var($user_id, FILTER_VALIDATE_INT)) {
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
                    $stmt->execute();
                } else {
                    throw new Exception("Invalid user ID detected.");
                }
            }

            $db->commit();
            $message = "Group '$group_name' created successfully! Group ID: $group_id";
        } catch (Exception $e) {
            $db->rollBack();
            $message = "Error creating group: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a valid group name and select at least one user.";
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

            $message = "Successfully joined group with ID: $group_id";
        } catch (PDOException $e) {
            $message = "Error joining group: " . $e->getMessage();
        }
    } else {
        $message = "Please enter a valid Group ID.";
    }
}

// Fetch users from the database for display
try {
    $userQuery = "SELECT user_id, username FROM users";
    $users = $db->query($userQuery)->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $users = [];
    $message = "Error fetching users: " . $e->getMessage();
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
        .user-checkbox {
            background-color: #b49bc8;
            padding: 5px;
            border-radius: 5px;
            display: flex;
            align-items: center;
            margin-bottom: 5px;
        }
        .user-checkbox input {
            margin-right: 10px;
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
            <label for="group_name">Group Name:</label>
            <input type="text" id="group_name" name="group_name" required>

            <h3>Select Users:</h3>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                    <div class="user-checkbox">
                        <input type="checkbox" name="selected_users[]" value="<?php echo $user['user_id']; ?>">
                        <?php echo htmlspecialchars($user['username']); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No users available.</p>
            <?php endif; ?>

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
</body>
</html>

