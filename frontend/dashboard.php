<?php
session_start();

// Redirect to login page if not logged in
if (!isset($_SESSION['loggedIn']) || $_SESSION['loggedIn'] !== true) {
    echo "<script>alert('Access denied. Please log in first.'); window.location.href='login.php';</script>";
    exit();
}

$api_url = 'http://localhost:5000/user-stats';
$token = $_SESSION['token'] ?? '';

$options = [
    'http' => [
        'header'  => "Authorization: Bearer $token\r\n",
        'method'  => 'GET',
    ]
];
$context  = stream_context_create($options);
$response = @file_get_contents($api_url, false, $context);
$data = $response ? json_decode($response, true) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>MissionCritical - Dashboard</title>
   <link rel="stylesheet" href="https://a-dandrea.github.io/testWebsite/html/style.css">
</head>
<body>
   <header>
      <a href="index.php">
         <img src="https://a-dandrea.github.io/testWebsite/MC Logo.png" alt="MissionCritical Logo" class="logo">
      </a>
      <h1>Dashboard</h1>
      <nav>
         <a href="index.php">Home</a>
         <a href="logout.php">Log Out</a>
      </nav>
   </header>
   <div class="container">
      <section class="stats-overview">
         <h2>Your Stats Overview</h2>
         <ul>
            <li>Steps: <span><?php echo $data['steps'] ?? 'Loading...'; ?></span></li>
            <li>Miles Walked: <span><?php echo $data['miles'] ?? 'Loading...'; ?></span></li>
            <li>Flights Climbed: <span><?php echo $data['flights'] ?? 'Loading...'; ?></span></li>
            <li>Active Minutes: <span><?php echo $data['activeMinutes'] ?? 'Loading...'; ?></span></li>
         </ul>
      </section>
      
      <section class="health-info">
         <h2>Your Health Info</h2>
         <ul>
            <li>Height: <span><?php echo $data['height'] ?? 'Loading...'; ?></span></li>
            <li>Weight: <span><?php echo $data['weight'] ?? 'Loading...'; ?></span></li>
            <li>BMI: <span><?php echo $data['bmi'] ?? 'Loading...'; ?></span></li>
         </ul>
      </section>
      
      <section class="leaderboard">
         <h2>Leaderboard</h2>
         <ol>
            <?php if (!empty($data['leaderboard'])) {
               foreach ($data['leaderboard'] as $user) {
                  echo "<li>{$user['name']} - {$user['steps']} steps</li>";
               }
            } else {
               echo "<li>Loading...</li>";
            } ?>
         </ol>
      </section>
   </div>
</body>
</html>
