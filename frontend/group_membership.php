<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Create a New Group</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Anonymous+Pro');

        body, html {
            height: 100%;
        }

        h1 {
            text-align: center;
            font-size: 30px;
            margin-bottom: 10px;
        }

        a:link { color: #a0cab0; }
        a:visited { color: #6cab67; }
        a:hover { color: #6cab67; }

        .wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f3f8f2;
        }

        .register-container {
            width: 100%;
            display: flex;
            flex-direction: column;
            transition: .5s ease-in-out;
        }

        .form-box {
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 30px;
            width: 450px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            margin-top: 5px;
        }

        .checkbox-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 10px;
        }

        .checkbox-container label {
            background: #e3e3e3;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 12px 0;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
        }

        button:hover {
            background-color: #45a049;
        }

        .message {
            text-align: center;
            padding: 10px;
            border-radius: 6px;
            margin-top: 10px;
        }

        .success { background-color: #d4edda; color: #155724; }
        .error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="register-container">
            <div class="form-box">
                <h1>Create a New Group</h1>

                <?php
                session_start();

                $dsn = 'mysql:host=joecool.highpoint.edu;dbname=csc4710_S25_missioncritical';
                $username = 'ejerrier';
                $password = '1788128';

                try {
                    $db = new PDO($dsn, $username, $password);
                    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    exit("<p class='message error'>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>");
                }

                // Fetch all users
                $userSql = "SELECT id, username FROM users";
                $usersResult = $db->query($userSql);

                // Handle group creation
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    $group_name = htmlspecialchars($_POST['group_name']);
                    $selected_users = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];

                    if (empty($group_name) || empty($selected_users)) {
                        echo "<p class='message error'>Please provide a group name and select at least one user.</p>";
                    } else {
                        // Create the group
                        $createGroupSql = "INSERT INTO groups (group_name) VALUES (:group_name)";
                        $stmt = $db->prepare($createGroupSql);
                        $stmt->bindParam(':group_name', $group_name);

                        if ($stmt->execute()) {
                            $newGroupId = $db->lastInsertId(); // Get the ID of the new group

                            // Add users to the new group
                            $insertUserGroupSql = "INSERT INTO user_groups (user_id, group_id) VALUES (:user_id, :group_id)";
                            $stmt = $db->prepare($insertUserGroupSql);

                            foreach ($selected_users as $user_id) {
                                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                                $stmt->bindParam(':group_id', $newGroupId, PDO::PARAM_INT);
                                $stmt->execute();
                            }

                            echo "<p class='message success'>Group '$group_name' created successfully!</p>";
                        } else {
                            echo "<p class='message error'>Error creating the group.</p>";
                        }
                    }
                }
                ?>

                <!-- Group Creation Form -->
                <form method="POST">
                    <label for="group_name">Group Name:</label>
                    <input type="text" id="group_name" name="group_name" required>

                    <label>Select Users:</label>
                    <div class="checkbox-container">
                        <?php while ($user = $usersResult->fetch(PDO::FETCH_ASSOC)): ?>
                            <label>
                                <input type="checkbox" name="selected_users[]" value="<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['username']) ?>
                            </label>
                        <?php endwhile; ?>
                    </div>

                    <button type="submit">Create Group</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

