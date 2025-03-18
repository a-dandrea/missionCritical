<?php
session_start();
ini_set('session.use_only_cookies', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debug session contents
if (!isset($_SESSION['user_id'])) {
    echo "User not logged in.";
    var_dump($_SESSION); // Debugging line to check session variables
    exit();
}

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

$user_id = $_SESSION['user_id'];

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch user data
    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, goals, activity_level, privilege FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "User not found.";
        exit();
    }

    // Fetch user's groups
    $sql_groups = "SELECT g.username FROM groups g 
                   JOIN user_groups ug ON g.group_id = ug.group_id
                   WHERE ug.user_id = :user_id";
    $stmt_groups = $db->prepare($sql_groups);
    $stmt_groups->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt_groups->execute();
    $groups = $stmt_groups->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Close database connections
$stmt->closeCursor();
$stmt_groups->closeCursor();
?>

