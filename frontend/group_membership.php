<?php
  session_start();
  $isLoggedIn = isset($_SESSION['user_id']); // Check if user is logged in

  $dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
  $username = 'ejerrier';
  $password = '1788128';

  try {
      $db = new PDO($dsn, $username, $password);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      exit("Database connection failed: " . $e->getMessage());
  }
  $sql = "SELECT privilege FROM users WHERE user_id = :user_id";
  $stmt = $db->prepare($sql);
  $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
  $stmt->execute();
  $user_privilege = $stmt->fetchColumn();

  $popupMessage = ''; // Message for JavaScript pop-up

  // Handle group creation
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_group'])) {
      $username = trim($_POST['username']); 
      $group_type = trim($_POST['group_type']);
      $parental_control = isset($_POST['parental_control']) ? 1 : 0;

      if (!empty($username) && !empty($group_type)) {
          try {
              $db->beginTransaction();

              // Insert new group
              $insertGroupSql = "INSERT INTO groups (username, type, parental_control) VALUES (:username, :type, :parental_control)";
              $stmt = $db->prepare($insertGroupSql);
              $stmt->bindParam(':username', $username);
              $stmt->bindParam(':type', $group_type);
              $stmt->bindParam(':parental_control', $parental_control, PDO::PARAM_INT);
              $stmt->execute();


              $group_id = $db->lastInsertId();
              $db->commit();
              $popupMessage = "Group '$username' created successfully! Group ID: $group_id";
          } catch (Exception $e) {
              $db->rollBack();
              $message = "Error creating group: " . $e->getMessage();
          }
      } else {
          $message = "Please enter a valid username and select a group type.";
      }
  }
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_group'])) {
   $group_id = trim($_POST['group_id']);

   if (!empty($group_id) && $isLoggedIn) {
       try {
           $user_id = $_SESSION['user_id'];
           $checkMembership = $db->prepare("SELECT * FROM user_groups WHERE user_id = :user_id AND group_id = :group_id");
           $checkMembership->execute([':user_id' => $user_id, ':group_id' => $group_id]);

           if ($checkMembership->rowCount() == 0) {
               $joinGroup = $db->prepare("INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)");
               $joinGroup->execute([':user_id' => $user_id, ':group_id' => $group_id]);
               $popupMessage = "Successfully joined group ID $group_id!";
           } else {
               $message = "You are already a member of this group.";
           }
       } catch (Exception $e) {
           $message = "Error joining group: " . $e->getMessage();
       }
   } else {
       $message = "You must be logged in and provide a valid Group ID.";
   }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Groups</title>
    <link rel="icon" href="images/astronaut.png">
    <link rel="stylesheet" href="style.css">
    <script>
        function toggleParentalControl() {
            var groupType = document.getElementById("group_type").value;
            var parentalControlDiv = document.getElementById("parental_control_div");
            if (groupType === "Family") {
                parentalControlDiv.style.display = "block";
            } else {
                parentalControlDiv.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <header>
    <nav class="navbar">   
    <div class="dropdown">
    <a href="index.php" class="dropbtn">
  <img src="images/rocket-icon.png" alt="Rocket Menu" class="rocket">
</a>
 <div class="dropdown-content">
         <a href="subscription.php">Subscriptions</a>
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

    <div class="container">
        <div class="form-container">
            <h2>Create a New Group</h2>

            <?php if (!empty($message)) : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>

            <form method="POST">
                <label for="username">Username (Group Name):</label>
                <input type="text" id="username" name="username" required>
                <br>

                <label for="group_type">Group Type:</label>
                <select id="group_type" name="group_type" onchange="toggleParentalControl()" required>
                    <option value="">Select a type</option>
                    <option value="Family">Family</option>
                    <option value="Friends">Friends</option>
                    <option value="Coworkers">Coworkers</option>
                </select>
                <br>

                <div id="parental_control_div" style="display: none;">
                    <label for="parental_control">Enable Parental Control as Admin?</label>
                    <input type="checkbox" id="parental_control" name="parental_control">
                    <br>
                </div>

                <button type="submit" name="create_group" class="submit-btn">Create Group</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h2>Join a Group</h2>
            <form method="POST">
                <label for="group_id">Enter Group ID:</label>
                <input type="text" id="group_id" name="group_id" required>
                <br>
                <button type="submit" name="join_group" class="submit-btn">Join Group</button>
            </form>
        </div>
    </div>

    <?php if (!empty($popupMessage)) : ?>
        <script>
            alert("<?php echo $popupMessage; ?>");
        </script>
    <?php endif; ?>
</body>

<footer style="background: #0f0a66; color:white; padding: 10px 20px;">
    <p>&copy; Copyright Mission Critical Group</p>
</footer>
</html>

