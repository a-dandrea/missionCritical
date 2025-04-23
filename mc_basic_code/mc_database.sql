CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    age INT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    passwordHash VARCHAR(255) NOT NULL,
    dateOfBirth DATE NULL,
    gender ENUM('M', 'F', 'Other') NULL,
    weight FLOAT NULL,
    height FLOAT NULL,
    goals INT NULL,
    activity_level INT NULL,
    privilege INT NULL,
    createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE groups (
    group_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    type ENUM('Family', 'Friends', 'Coworkers') NOT NULL,
    parental_control BOOLEAN DEFAULT NULL, -- Only applicable for Family groups
    description TEXT NULL
);

CREATE TABLE user_groups (
    user_id INT,
    group_id INT,
    PRIMARY KEY (user_id, group_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (group_id) REFERENCES groups(group_id) ON DELETE CASCADE
);

CREATE TABLE workouts (
   workoutID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   workoutType VARCHAR(100) NOT NULL,
   duration INT NOT NULL, -- in minutes
   caloriesBurned INT NOT NULL,
   --heartRate INT NOT NULL,
   startTime DATETIME NOT NULL,
   endTime DATETIME NOT NULL,
   stepCount INT NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id)
);

CREATE TABLE exercises (
   exerciseID INT PRIMARY KEY AUTO_INCREMENT,
   workoutID INT,
   exerciseName VARCHAR(100) NOT NULL,
   sets INT NOT NULL,
   repsPerSet INT NOT NULL,
   weight DECIMAL(5, 2) NULL, -- in LBS
   FOREIGN KEY (workoutID) REFERENCES workouts(workoutID)
);

CREATE TABLE nutrition (
   nutritionID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   foodItem VARCHAR(255) NOT NULL,
   calories INT NOT NULL,
   protein DECIMAL(5, 2) NULL, -- in grams
   fats DECIMAL(5, 2) NULL, -- in grams
   mealTime DATETIME NOT NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id)
);

CREATE TABLE progress (
   progressID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   weight DECIMAL(5, 2) NULL, -- in LBS
   bodyFat DECIMAL(5, 2) NULL, -- in percentage
   muscleMass DECIMAL(5, 2) NULL, -- in kg
   recordedAT DATETIME NOT NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id)
);

CREATE TABLE subscription (
   subscriptionID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   planName VARCHAR(100) NOT NULL,
   price DECIMAL(6,2) NOT NULL,
   startDate DATETIME NOT NULL,
   endDate DATETIME NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id)
);

CREATE TABLE payments (
   paymentID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   subscriptionID INT,
   amountPaid DECIMAL(6,2) NOT NULL,
   paymentDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
   paymentMethod VARCHAR(50) NOT NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id),
   FOREIGN KEY (subscriptionID) REFERENCES subscription(subscriptionID)
);

CREATE TABLE admin (
   adminID INT PRIMARY KEY AUTO_INCREMENT,
   username VARCHAR(100) NOT NULL,
   passwordHash VARCHAR(255) NOT NULL,
   role ENUM('Super Admin', 'Support', 'Manager') NOT NULL
);
