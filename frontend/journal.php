<?php
session_start();

$dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
$username = 'ejerrier';
$password = '1788128';

$isLoggedIn = isset($_SESSION['user_id']);
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get selected week offset from GET request
$selectedWeekOffset = isset($_GET['week']) ? intval($_GET['week']) : 0;

// Get the Monday and Sunday of the selected week
$today = new DateTime();
$startOfWeek = clone $today;
$startOfWeek->modify('monday this week');
$startOfWeek->modify("-{$selectedWeekOffset} week");
$endOfWeek = clone $startOfWeek;
$endOfWeek->modify('+6 days');

$startFormatted = $startOfWeek->format('F j');
$endFormatted = $endOfWeek->format('F j, Y');

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT firstName, lastName, email, age, gender, weight, height, goal1, goal2, goal3, goal4, activity_level, privilege FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $sql = "SELECT daily_step_goal, daily_calorie_goal, daily_active_goal, daily_outside_goal, daily_sleep_goal, daily_water_goal FROM users WHERE user_id = :user_id";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $goals = $stmt->fetch(PDO::FETCH_ASSOC);

    $params = [
        ':user_id' => $user_id,
        ':start_date' => $startOfWeek->format('Y-m-d'),
        ':end_date' => $endOfWeek->format('Y-m-d')
    ];

    function fetchSum($db, $column, $table) {
        global $params;
        $sql = "SELECT SUM($column) AS total FROM $table 
                WHERE date >= :start_date AND date <= :end_date 
                AND user_id = :user_id";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
    }

    $daily_steps = fetchSum($db, 'daily_step_count', 'daily_steps');
    $daily_active_minutes = fetchSum($db, 'daily_active_minutes', 'daily_active_minutes');
    $daily_water_intake = fetchSum($db, 'daily_water_intake', 'daily_water_intake');
    $daily_sleep = fetchSum($db, 'daily_sleep_hours', 'daily_sleep_log');
    $daily_time_outside = fetchSum($db, 'daily_time_outdoors', 'daily_time_outdoors');

    if (!$user || !$goals) {
        echo "User or goals not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Mission Logs</title>
   <link rel="icon" href="images/astronaut.png">
   <link rel="stylesheet" href="style.css"> 
</head>
<header>
<nav class="navbar">   
    <div class="dropdown">
      <a href="index.php" class="dropbtn">
         <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
      </a>
      <div class="dropdown-content">
            <a href="subcriptions.php">Subscriptions</a>
            <a href="payment.php">Payment</a>
      </div>
   </div>
   <div class="nav-links">
      <a href="dashboard.php">Dashboard</a>
      <a href="journal.php">Mission Logs</a>
      <a href="leaderboard.php">Leaderboard</a>
      <a href="workout.php">Workouts</a>
      <a href="recipe.php">Recipes</a>
      <?php if ($isLoggedIn): ?>
         <a href="logout.php" class="logout-button">Logout</a>
      <?php endif; ?>
   </div>
</nav>
</header>
<body>
<div class="container">
   <h2>Mission Logs</h2>

   <div class="box full" style="margin-bottom: 2rem;">
   <form method="GET" style="text-align: center; margin-bottom: 1rem;">
      <label for="week"><strong>Select Week:</strong></label>
      <select name="week" id="week" onchange="this.form.submit()">
         <?php
         for ($i = 0; $i < 10; $i++) {
            $weekStart = (new DateTime())->modify('monday this week')->modify("-$i week");
            $weekEnd = (clone $weekStart)->modify('+6 days');
            $value = $i;
            $label = $weekStart->format('M j') . " – " . $weekEnd->format('M j, Y');
            $selected = ($i == $selectedWeekOffset) ? "selected" : "";
            echo "<option value=\"$value\" $selected>$label</option>";
         }
         ?>
      </select>
   </form>
      <p style="text-align:center;"><strong>Choose a Goal to View Weekly Progress:</strong></p>
      <select id="goal-select" onchange="showProgress(this.value)">
         <option value="">-- Select a Goal --</option>
         <option value="steps">Steps</option>
         <option value="active">Active Minutes</option>
         <option value="water">Water Intake</option>
         <option value="sleep">Sleep</option>
         <option value="outdoors">Time Outdoors</option>
      </select>

      <?php
      $progressGoals = [
         'steps' => ['label' => 'Weekly Step Progress', 'value' => $daily_steps, 'goal' => $goals['daily_step_goal'], 'unit' => 'steps'],
         'active' => ['label' => 'Weekly Active Minutes Progress', 'value' => $daily_active_minutes, 'goal' => $goals['daily_active_goal'], 'unit' => 'minutes'],
         'water' => ['label' => 'Weekly Water Intake Progress', 'value' => $daily_water_intake, 'goal' => $goals['daily_water_goal'], 'unit' => 'oz'],
         'sleep' => ['label' => 'Weekly Sleep Progress', 'value' => $daily_sleep, 'goal' => $goals['daily_sleep_goal'], 'unit' => 'hours'],
         'outdoors' => ['label' => 'Weekly Time Outdoors Progress', 'value' => $daily_time_outside, 'goal' => $goals['daily_outside_goal'], 'unit' => 'minutes'],
      ];

      foreach ($progressGoals as $key => $info):
         $weekly_goal = 7 * $info['goal'];
         $percentage = ($weekly_goal > 0) ? ($info['value'] / $weekly_goal) * 100 : 0;
      ?>
         <div id="<?php echo $key; ?>" class="goal-progress" style="display:none; margin-top: 1rem;">
            <label for="<?php echo $key; ?>-progress"><?php echo $info['label']; ?></label>
            <progress id="<?php echo $key; ?>-progress" value="<?php echo $info['value']; ?>" max="<?php echo $weekly_goal; ?>" style="width:100%; height:30px;"></progress>
            <label><?php echo round($percentage, 2); ?>% of your weekly <?php echo strtolower($key); ?> goal.</label><br>
            <span><?php echo number_format($info['value'], ($key === 'sleep' ? 1 : 0)); ?> / <?php echo number_format($weekly_goal, ($key === 'sleep' ? 1 : 0)); ?> <?php echo $info['unit']; ?></span>
         </div>
      <?php endforeach; ?>
   </div>
</div>


<div class="container">
      <h2>Log New Mission</h2>
      <form id="habit-form">
         <label>Journal Date: <input type="date" name="date" required></label><br>
         <label>Steps: <input type="number" name="steps"></label><br>
         <label>Active Minutes: <input type="number" name="active_minutes"></label><br>
         <label>Water Intake (oz): <input type="number" name="water"></label><br>
         <label>Hours Slept: <input type="number" step="0.1" name="sleep"></label><br>
         <label>Time Outdoors (minutes): <input type="number" name="outdoor_time"></label><br>
         <button type="submit">Submit</button>
      </form>

      <?php
function getHabitLogs($db, $table, $column, $month, $year, $user_id) {
    try {
        $query = "SELECT DATE(date) as date, $column FROM $table
                  WHERE MONTH(date) = :month AND YEAR(date) = :year AND user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':month' => $month,
            ':year' => $year,
            ':user_id' => $user_id
        ]);
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
    } catch (PDOException $e) {
        echo "<p>Error fetching from $table: " . $e->getMessage() . "</p>";
        return [];
    }
}

$month = date('m');
$year = date('Y');
$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

// Fetch habit data
$step_data    = getHabitLogs($db, 'daily_steps', 'daily_step_count', $month, $year, $user_id);
$active_data  = getHabitLogs($db, 'daily_active_minutes', 'daily_active_minutes', $month, $year, $user_id);
$water_data   = getHabitLogs($db, 'daily_water_intake', 'daily_water_intake', $month, $year, $user_id);
$sleep_data   = getHabitLogs($db, 'daily_sleep_log', 'daily_sleep_hours', $month, $year, $user_id);
$outdoor_data = getHabitLogs($db, 'daily_time_outdoors', 'daily_time_outdoors', $month, $year, $user_id);

function renderRow($label, $data, $goal, $daysInMonth, $year, $month) {
   echo "<tr><td><strong>$label</strong></td>";
   for ($day = 1; $day <= $daysInMonth; $day++) {
       $date = sprintf('%04d-%02d-%02d', $year, $month, $day);
       $value = isset($data[$date]) ? $data[$date] : 0;

       // Calculate percentage completion
       $percentage = ($value / $goal) * 100;
       // Define gradient colors based on completion percentage
       if ($percentage >= 100) {
           $color = '#4CAF50';  // Green for 100%
       } elseif ($percentage >= 75) {
           $color = '#8BC34A';  // Light Green for 75%
       } elseif ($percentage >= 50) {
           $color = '#FFEB3B';  // Yellow for 50%
       } elseif ($percentage >= 25) {
           $color = '#FF9800';  // Orange for 25%
       } else {
           $color = '#F44336';  // Red for less than 25%
       }
       
       echo "<td title='$value' style='width: 20px; height: 20px; background: $color; border: 1px solid #aaa;'></td>";
   }
   echo "</tr>";
}

?>

<div style="margin-top: 40px;">
    <h2>Monthly Habit Tracker (<?= date('F Y') ?>)</h2>
    
    <!-- Key for colors -->
    <div style="margin-bottom: 10px; text-align: center;">
        <strong>Progress Legend:</strong>
        <span style="background-color: #4CAF50; padding: 5px; color: white;">100%</span> 
        <span style="background-color: #8BC34A; padding: 5px; color: white;">75%</span> 
        <span style="background-color: #FFEB3B; padding: 5px; color: black;">50%</span> 
        <span style="background-color: #FF9800; padding: 5px; color: white;">25%</span>
        <span style="background-color: #F44336; padding: 5px; color: white;">0%</span>
    </div>
    <div style="overflow-x: auto; max-width: 100%;">
    <table style="border-collapse: collapse; font-size: 12px;">
        <tr><td></td>
        <?php for ($i = 1; $i <= $daysInMonth; $i++): ?>
            <td><?= $i ?></td>
        <?php endfor; ?>
        </tr>
        <?php
            renderRow('Steps', $step_data, $goals['daily_step_goal'], $daysInMonth, $year, $month);
            renderRow('Active Min', $active_data, $goals['daily_active_goal'], $daysInMonth, $year, $month);
            renderRow('Water (oz)', $water_data, $goals['daily_water_goal'], $daysInMonth, $year, $month);
            renderRow('Sleep (hrs)', $sleep_data, $goals['daily_sleep_goal'], $daysInMonth, $year, $month);
            renderRow('Outdoors (min)', $outdoor_data, $goals['daily_outside_goal'], $daysInMonth, $year, $month);
        ?>
    </table>
        </div>
</div>



      <script src="assets/journal.js"></script>
   </div>
   <script>
function showProgress(goal) {
   const sections = document.querySelectorAll('.goal-progress');
   sections.forEach(section => section.style.display = 'none');

   if (goal) {
      const selected = document.getElementById(goal);
      if (selected) {
         selected.style.display = 'block';
      }
   }
}
</script>

</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>