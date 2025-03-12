# add this line to all files that need to be connected to the database
from db_connector import connect_db

# To connect to the database
# inside functions
# conn = connect_db()
# cursor = conn.cursor()
# cursor.execute("SQL code")
# conn.close() - at end

class Workouts:
    def __init__(self, runPace, runSpeed, runDistance, heartRate, caloriesBurned, runTime):
        self.runPace = runPace  # in min/mile
        self.runSpeed = runSpeed  # in mph
        self.runDistance = runDistance  # in miles
        self.heartRate = heartRate  # in bpm
        self.caloriesBurned = caloriesBurned  # in kcal
        self.runTime = runTime  # in minutes

class User:
   def __init__(self, fname, lname, age, email, password, dob, gender, weight, height, goals, activity_level, privilege): 
      self.fname = fname
      self.lname = lname
      self.age = age
      self.email = email
      self.password = password
      self.dob = dob
      self.gender = gender  # m/f
      self.weight = weight  # in lbs
      self.height = height  # in in
      self.goals = goals
      self.activity_level = activity_level # for total daily energy expenditure (tdee)
      self.privilege = privilege  # Parent or Child  
      self.workouts = None

      self.weight_kg = round(self.weight * 0.45359, 2)  #kg 
      self.height_cm = round(self.height * 2.54, 2)  #cm 

   def calculate_bmi(self):
      try:
         if self.weight <= 0:
            return "Error: Weight must be greater than zero."
         return round(703 * self.weight / (self.height ** 2), 2)
      except ZeroDivisionError:
         return "Error: Height cannot be zero."
   
   def add_workouts(self, runPace, runSpeed, runDistance, heartRate, caloriesBurned, runTime):
        self.workouts = Workouts(runPace, runSpeed, runDistance, heartRate, caloriesBurned, runTime)

   def calculate_bmr(self):
      if self.gender == "m":
         return round(88.362 + (13.397 * self.weight_kg) + (4.799 * self.height_cm) - (5.677 * self.age),2)
      elif self.gender == "f": 
         return round(447.593 + (9.247 * self.weight_kg) + (3.098 * self.height_cm) - (4.330 * self.age), 2)
      else:  
         return "Error: Gender must be entered as either m or f."
   
   def calculate_tdee(self):
      bmr = self.calculate_bmr()
      return round(bmr * self.activity_level, 2)
  
   def calculate_distance(self):
      return round((self.running_speed * self.running_time) / 60, 2)
   
   def calculate_pace(self):
        if self.running_speed > 0:
            return round(60 / self.running_speed, 2)
        return "N/A"

   def  calculate_running_calories(self):
        met_values = {
            4: 6.0,  # 4 mph (brisk walk)
            5: 8.3,  # 5 mph (moderate run)
            6: 9.8,  # 6 mph (steady run)
            7: 11.0,  # 7 mph
            8: 11.8,  # 8 mph
            9: 12.8,  # 9 mph
            10: 14.5  # 10+ mph (fast run)
        }

        # Find closest MET value for the given speed
        closest_speed = min(met_values.keys(), key=lambda x: abs(x - self.running_speed))
        met = met_values[closest_speed]

        # Calories burned formula: (MET * weight_kg * time in hours)
        return round(met * self.weight_kg * (self.running_time / 60), 2) 
   
   def display_info(self):
      workout_info = (f" Running Pace: {self.workouts.runPace} min/mile\n"
                      f" Running Speed: {self.workouts.runSpeed} mph\n"
                      f" Distance Run: {self.workouts.runDistance} miles\n"
                      f" Heart Rate: {self.workouts.heartRate} bpm\n"
                      f" Calories Burned: {self.workouts.caloriesBurned} kcal\n"
                      f" Running Time: {self.workouts.runTime} min\n")
      return (f" First Name:   {self.fname}\n"
              f" Last Name:    {self.lname}\n"
              f" Age:    {self.age}\n"
              f" Email:  {self.email}\n"
              f" Password: {self.password}\n"
              f" DoB: {self.dob}\n"
              f" Gender: {self.gender}\n"
              f" Weight: {self.weight} lbs ({self.weight_kg} kg)\n" 
              f" Height: {self.height} in ({self.height_cm} cm)\n" 
              f" Goals:  {self.goals}\n" 
              f" BMI:    {self.calculate_bmi()}\n" 
              f" BMR:    {self.calculate_bmr()} calories per day\n" 
              f" TDEE:   {self.calculate_tdee()} calories per day (based on activity level)\n"
              f" {workout_info}")
   
   def calculate_tdee(self):
      bmr = self.calculate_bmr()
      return round(bmr * self.activity_level, 2)


def main():
   try: 
      fname = input("Enter your First Name: ")

      lname = input("Enter your Last Name: ")


      age = int(input("Enter your age (7-121 yo): "))
      if age <= 6:
         raise ValueError("Age must be 7 years of age or older.")
      elif age >= 121:
         raise ValueError("Age must be 120 years of age or younger.")

      email = input("Enter your Email Address: ")
      
      password  = input("Enter your password: ")
      
      dob = input("Enter your Date of Birth (YYYY-MM-DD): ")

      gender = input("Enter your gender (m/f): ")
      if gender not in ("m", "f"):
         raise ValueError("Gender must be entered as  either 'm' or 'f'.")

      weight = float(input("Enter your weight (lbs): "))
      if weight <= 30:
         raise ValueError("Weight must be greater than 30 lbs.")

      height = float(input("Enter your height (in): "))
      if height <= 30:
         raise ValueError("Height must be greater than 30 inches.")
      
      print("Choose from the following goal options: \n")
      print("(0) No specific goal\n")
      print("(1) Maintain weight\n")
      print("(2) Lose weight \n")
      print("(3) Increase Muscle Mass \n ")
      print("(4) Improve Stamina\n")
     
      goals = int(input("Enter your fitness goal(0 - 4): \n"))
      if goals < 0 or goals > 4:
         raise ValueError("You must select 0, 1, 2, 3, or 4.")
      
      print("\nSelect Your Activity Level:")
      print("1. Sedentary (little or no exercise)")
      print("2. Lightly active (1-3 days/week)")
      print("3. Moderately active (3-5 days/week)")
      print("4. Very active (6-7 days/week)")
      print("5. Super active (athlete, intense training)")

      activity_choice = int(input("Enter your choice (1-5): "))
      activity_levels = {1: 1.2, 2: 1.375, 3: 1.55, 4: 1.725, 5: 1.9}

      if activity_choice not in activity_levels:
            raise ValueError("Invalid choice. Please enter a number between 1 and 5.")

      activity_level = activity_levels[activity_choice]

      print("\nSelect Privilege Level:")
      print("1. Parent")
      print("2. Child")

      privilege_choice = int(input("Enter your choice (1-2): "))
      privilege_levels = {1: "Parent", 2: "Child"}

      if privilege_choice not in privilege_levels:
        raise ValueError("Invalid choice. Please enter 1 for Parent or 2 for Child.")

      privilege = privilege_levels[privilege_choice]

      user = User(fname, lname, age, email, password, dob, gender, weight, height, goals, activity_level, privilege)
      add_workout = input("Would you like to add workout data? (y/n): ").strip().lower()
      print(f"DEBUG: add_workout value = {add_workout}")
      if add_workout == 'y':
         print("\nEnter Workout Data:")
         run_pace = float(input("Enter your running pace (min/mile): "))
         run_speed = float(input("Enter your running speed (mph): "))
         run_distance = float(input("Enter your running distance (miles): "))
         heart_rate = int(input("Enter your heart rate (bpm): "))
         calories_burned = float(input("Enter calories burned: "))
         run_time = float(input("Enter your running time (minutes): "))

         user.add_workouts(run_pace, run_speed, run_distance, heart_rate, calories_burned, run_time)
      
      print("\nUser Data:")
      print(user.display_info())
   
   except ValueError as e:
      print(f"Input error: {e}")


if __name__ == "__main__":
    main()

