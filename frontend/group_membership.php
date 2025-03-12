<!DOCTYPE html>
<html>
 <head>
     <title>Group Membership</title>
     <style>
         body { font-family: Arial, sans-serif; margin: 20px; }
         .message { padding: 10px; border: 1px solid; margin-bottom: 15px; }
         .success { background-color: #d4edda; color: #155724; }
         .error { background-color: #f8d7da; color: #721c24; }
     </style>
 </head>
 <body>
     <h1>Group Membership Management</h1><?php
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

// Handle group join requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $group_id = intval($_POST['group_id']);

    // Check if user is already in the group
    $checkSql = "SELECT * FROM user_groups WHERE user_id = :user_id AND group_id = :group_id";
    $stmt = $db->prepare($checkSql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $message = "You are already a member of this group.";
    } else {
        // Add user to the group
        $insertSql = "INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)";
        $stmt = $db->prepare($insertSql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':group_id', $group_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $message = "Successfully joined the group!";
        } else {
            $message = "Error joining the group.";
        }
    }
}

// Display groups
$groupSql = "SELECT * FROM groups";
$groupsResult = $db->query($groupSql);

// Display group members
$selectedGroupId = isset($_GET['group_id']) ? intval($_GET['group_id']) : null;
$members = [];

if ($selectedGroupId) {
    $memberSql = "
        SELECT users.username
        FROM users
        JOIN user_groups ON users.id = user_groups.user_id
        WHERE user_groups.group_id = :group_id";
        
    $stmt = $db->prepare($memberSql);
    $stmt->bindParam(':group_id', $selectedGroupId, PDO::PARAM_INT);
    $stmt->execute();
    $members = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Group Membership</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .message { padding: 10px; border: 1px solid; margin-bottom: 15px; }
        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <h1>Group Membership Management</h1>

