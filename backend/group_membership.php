<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "fitness_app");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle group join requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $group_id = intval($_POST['group_id']);

    // Check if user is already in the group
    $checkSql = "SELECT * FROM user_groups WHERE user_id = ? AND group_id = ?";
    $stmt = $conn->prepare($checkSql);
    $stmt->bind_param("ii", $user_id, $group_id);
    $stmt->execute();
    $checkResult = $stmt->get_result();

    if ($checkResult->num_rows > 0) {
        $message = "You are already a member of this group.";
    } else {
        // Add user to the group
        $insertSql = "INSERT INTO user_groups (user_id, group_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bind_param("ii", $user_id, $group_id);

        if ($stmt->execute()) {
            $message = "Successfully joined the group!";
        } else {
            $message = "Error joining the group: " . $stmt->error;
        }
    }
}

// Display groups
$groupSql = "SELECT * FROM groups";
$groupsResult = $conn->query($groupSql);

// Display group members
$selectedGroupId = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
$members = [];

if ($selectedGroupId) {
    $memberSql = "
        SELECT users.username 
        FROM users 
        JOIN user_groups ON users.id = user_groups.user_id
        WHERE user_groups.group_id = ?";
    
    $stmt = $conn->prepare($memberSql);
    $stmt->bind_param("i", $selectedGroupId);
    $stmt->execute();
    $membersResult = $stmt->get_result();

    while ($row = $membersResult->fetch_assoc()) {
        $members[] = $row['username'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Membership</title>
</head>
<body>
    <h1>Join a Group</h1>

    <?php if (isset($message)): ?>
        <p style="color: <?= strpos($message, 'Success') !== false ? 'green' : 'red'; ?>;">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="user_id">User ID:</label>
        <input type="text" id="user_id" name="user_id" required>

        <label for="group_id">Select Group:</label>
        <select id="group_id" name="group_id" required>
            <?php while ($group = $groupsResult->fetch_assoc()): ?>
                <option value="<?= $group['id'] ?>"><?= htmlspecialchars($group['group_name']) ?></option>
            <?php endwhile; ?>
        </select>

        <button type="submit">Join Group</button>
    </form>

    <h2>View Group Members</h2>
    <form method="GET" action="">
        <label for="view_group">Select Group:</label>
        <select id="view_group" name="group_id">
            <option value="">-- Select a Group --</option>
            <?php 
            $groupsResult->data_seek(0); // Reset pointer for reuse
            while ($group = $groupsResult->fetch_assoc()): 
            ?>
                <option value="<?= $group['id'] ?>" 
                    <?= $selectedGroupId == $group['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['group_name']) ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit">View Members</button>
    </form>

    <?php if ($selectedGroupId): ?>
        <h3>Group Members:</h3>
        <?php if (empty($members)): ?>
            <p>No members found in this group.</p>
        <?php else: ?>
            <ul>
                <?php foreach ($members as $member): ?>
                    <li><?= htmlspecialchars($member) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>

