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
              $$insertGroupSql = "INSERT INTO groups (username, group_type) VALUES (:username, :group_type)"; stmt->bindParam(':username', $username);
              $stmt->bindParam(':group_type', $group_type);
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
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create or Join Group</title>
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
            <a href="journal.php">Journal</a>
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

