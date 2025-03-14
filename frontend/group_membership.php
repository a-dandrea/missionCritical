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
        // Insert new group
        $insertGroupSql = "INSERT INTO groups (group_name) VALUES (:group_name)";
        $stmt = $db->prepare($insertGroupSql);
        $stmt->bindParam(':group_name', $group_name);
        $stmt->execute();
        
        $group_id = $db->lastInsertId(); // Get the newly created group ID

        // Add selected users to the new group
        foreach ($selected_users as $user_id) {
            $insertUserGroupSql = "INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)";
            $stmt = $db->prepare($insertUserGroupSql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
            $stmt->execute();
        }

        $message = "Group '$group_name' created successfully!";
    } else {
        $message = "Please enter a group name and select at least one user.";
    }
}

// Display users
$userSql = "SELECT * FROM users";
$usersResult = $db->query($userSql);

// Display groups for joining
$groupSql = "SELECT * FROM groups";
$groupsResult = $db->query($groupSql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Group Management</title>
    <link rel="stylesheet" href="style.css"> 
</head>
<header>
   <nav class="navbar">   
      <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      <div class="nav-links">
         <a href="index.php">Home</a>
         <a href="dashboard.php">Dashboard</a>
         <a href="leaderboard.php">Leaderboard</a>
         <a href="workout.php">Workouts</a>
       </div>
   </nav>
</header>
<body>
<div class="container">
    <h1>Group Management</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <!-- Create Group Form -->
    <div class="form-section">
        <h2>Create a New Group</h2>
        <form method="POST">
            <label for="group_name">Group Name:</label>
            <input type="text" id="group_name" name="group_name" required>

            <label>Select Users:</label>
            <div id="user-list">
                <?php while ($user = $usersResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <div>
                        <input 
                            type="checkbox" 
                            id="user_<?= $user['id'] ?>" 
                            name="selected_users[]" 
                            value="<?= $user['id'] ?>"
                            onclick="updateSelectedUsers(this, '<?= htmlspecialchars($user['username']) ?>')"
                        >
                        <label for="user_<?= $user['id'] ?>"><?= htmlspecialchars($user['username']) ?></label>
                    </div>
                <?php endwhile; ?>
            </div>

            <!-- Display selected users -->
            <div class="selected-users" id="selected-users-container" style="display: none;">
                <p>Selected Users:</p>
                <ul class="selected-users-list" id="selected-users-list"></ul>
            </div>

            <button type="submit" name="create_group">Create Group</button>
        </form>
    </div>

    <!-- Join Group Form -->
    <div class="form-section">
        <h2>Join an Existing Group</h2>
        <form method="POST">
            <label for="user_id">Your User ID:</label>
            <input type="text" id="user_id" name="user_id" required>

            <label for="group_id">Select Group:</label>
            <select id="group_id" name="group_id" required>
                <?php while ($group = $groupsResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['group_name']) ?></option>
                <?php endwhile; ?>
            </select>

            <button type="submit" name="join_group">Join Group</button>
        </form>
    </div>
</div>

<script>
    const selectedUsersList = document.getElementById('selected-users-list');
    const selectedUsersContainer = document.getElementById('selected-users-container');

    function updateSelectedUsers(checkbox, username) {
        if (checkbox.checked) {
            const listItem = document.createElement('li');
            listItem.textContent = username;
            listItem.id = `selected-user-${checkbox.value}`;
            selectedUsersList.appendChild(listItem);
        } else {
            const listItem = document.getElementById(`selected-user-${checkbox.value}`);
            if (listItem) listItem.remove();
        }

        // Show or hide selected users container
        selectedUsersContainer.style.display = selectedUsersList.children.length > 0 ? 'block' : 'none';
    }
</script>

</body>
</html>

