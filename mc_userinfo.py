# add this line to all files that need to be connected to the database
from db_connector import connect_db

# To connect to the database
# inside functions
# conn = connect_db()
# cursor = conn.cursor()
# cursor.execute("SQL code")
# conn.close() - at end

class User:
   def __init__(self, name, age, gender, weight, height, goals, activity_level,privilege):
      self.name = name
      self.age = age
      self.gender = gender  # m/f
      self.weight = weight  # in lbs
      self.height = height  # in in
      self.goals = goals
      self.activity_level = activity_level # for total daily energy expenditure (tdee)
      self.privilege = privilege  # Parent or Child  

      self.weight_kg = round(self.weight * 0.45359, 2)  #kg 
      self.height_cm = round(self.height * 2.54, 2)  #cm 

   def calculate_bmi(self):
      try:
         if self.weight <= 0:
            return "Error: Weight must be greater than zero."
         return round(703 * self.weight / (self.height ** 2), 2)
      except ZeroDivisionError:
         return "Error: Height cannot be zero."

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
   
   def display_info(self):
      return (f" Name:   {self.name}\n"
              f" Age:    {self.age}\n" 
              f" Gender: {self.gender}\n"
              f" Weight: {self.weight} lbs ({self.weight_kg} kg)\n" 
              f" Height: {self.height} in ({self.height_cm} cm)\n" 
              f" Goals:  {self.goals}\n" 
              f" BMI:    {self.calculate_bmi()}\n" 
              f" BMR:    {self.calculate_bmr()} calories per day\n" 
              f" TDEE:   {self.calculate_tdee()} calories per day (based on activity level)\n")
   
   def calculate_tdee(self):
      bmr = self.calculate_bmr()
      return round(bmr * self.activity_level, 2)


def main():
   try: 
      name = input("Enter your name: ")

      age = int(input("Enter your age (7-121 yo): "))
      if age <= 6:
         raise ValueError("Age must be 7 years of age or older.")
      elif age >= 121:
         raise ValueError("Age must be 120 years of age or younger.")

      gender = input("Enter your gender (m/f): ")
      if gender not in ("m", "f"):
         raise ValueError("Gender must be entered as  either 'm' or 'f'.")

      weight = float(input("Enter your weight (lbs): "))
      if weight <= 30:
         raise ValueError("Weight must be greater than 30 lbs.")

      height = float(input("Enter your height (in): "))
      if height <= 30:
         raise ValueError("Height must be greater than 30 inches.")
      
      goals = int(input("""Enter your fitness goal: (0)(1)(2)(3)(4) 
                        (0) No specific goal
                        (1) Maintain weight
                        (2) Lose weight
                        (3) Increase Muscle Mass
                        (4) Improve Stamina
                        """))
      if goals < 0 or goals > 4:
         raise ValueError("You must select 0, 1, 2, 3, or 4.")
      break #exit loop if input is invalid
   except ValueError:
      print ("invlaid input. Please enter a number between 0 and 4.")

      
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

      user = User(name, age, gender, weight, height, goals, activity_level, privilege)
      print("\nUser Data:")
      print(user.display_info())
   
   except ValueError as e:
      print(f"Input error: {e}")

   printf("ky testing github link.")

if __name__ == "__main__":
    main()

