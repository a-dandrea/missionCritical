<?php
session_start();
require_once 'db_connection.php'; // Your database connection logic

if (!isset($_SESSION['user_id'])) {
    echo "Error: User not logged in.";
    exit();
}

$userID = $_SESSION['user_id'];

try {
    $stmt = $db->prepare("
        SELECT g.group_name 
        FROM groups g
        JOIN user_groups ug ON g.group_id = ug.group_id
        WHERE ug.user_id = ?
    ");
    $stmt->execute([$userID]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error fetching groups: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Groups</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Your Groups</h2>
        <ul>
            <?php if (!empty($groups)): ?>
                <?php foreach ($groups as $group): ?>
                    <li><?php echo htmlspecialchars($group['group_name']); ?></li>
                <?php endforeach; ?>
            <?php else: ?>
                <li>You are not part of any groups yet.</li>
            <?php endif; ?>
        </ul>
    </div>
</body>
</html>

