<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

if (!isset($_SESSION['user_id'])) {
   header("Location: login.php");
   exit();
}

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    exit("Database connection failed: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            updateLeaderboard();
        });

        function updateLeaderboard() {
            const category = document.getElementById("category").value;
            
            fetch("fetch_leaderboard.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "category=" + category
            })
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById("leaderboard-body");
                tableBody.innerHTML = "";
                
                data.forEach((row) => {
                    const tr = document.createElement("tr");
                    tr.innerHTML = `
                        <td>${row.rank}</td>
                        <td>${row.fullName}</td>
                        <td>${row.goal}</td>
                        <td>${row.currentValue}</td>
                        <td>${row.percentage}</td>
                    `;
                    tableBody.appendChild(tr);
                });
            })
            .catch(error => console.error("Error fetching leaderboard data:", error));
        }
    </script>
</head>
<body>
    <h1>Leaderboard</h1>
    <label for="category">Select Category:</label>
    <select name="category" id="category" onchange="updateLeaderboard()">
        <option value="calories">Calories</option>
        <option value="steps">Steps</option>
        <option value="distance">Distance (miles)</option>
    </select>

    <table id="leaderboard-table" border="1">
        <thead>
            <tr>
                <th>Rank</th>
                <th>User</th>
                <th>Goal</th>
                <th>Current Status</th>
                <th>Percentage of Goal Completion</th>
            </tr>
        </thead>
        <tbody id="leaderboard-body">
            <!-- Data will be inserted here dynamically -->
        </tbody>
    </table>
</body>
</html>

