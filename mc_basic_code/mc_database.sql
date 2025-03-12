CREATE TABLE users (
   user_id INT PRIMARY KEY AUTO_INCREMENT,
   firstName VARCHAR(255) NOT NULL,
   lastName VARCHAR(255) NOT NULL,
   age INT NOT NULL, 
   email VARCHAR(255) NOT NULL,
   passwordHash VARCHAR(255) NOT NULL,
   dateOfBirth DATE NULL,
   gender ENUM('M', 'F', 'Other') NULL,
   weight FLOAT NOT NULL,
   height FLOAT NOT NULL, 
   goals INT NOT NULL, 
   activity_level INT NOT NULL, 
   privilege INT NOT NULL,
   createdAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE workouts (
   workoutID INT PRIMARY KEY AUTO_INCREMENT,
   userID INT,
   workoutType VARCHAR(100) NOT NULL,
   duration INT NOT NULL, -- in minutes
   caloriesBurned INT NOT NULL,
   startTime DATETIME NOT NULL,
   endTime DATETIME NOT NULL,
   FOREIGN KEY (userID) REFERENCES users(user_id)
);

CREATE TABLE exercises (
   exerciseID INT PRIMARY KEY AUTO_INCREMENT,
   workoutID INT,
   exerciseName VARCHAR(100) NOT NULL,
   sets INT NOT NULL,
   reps INT NOT NULL,
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
