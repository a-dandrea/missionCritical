import matplotlib.pyplot as plt
import array as arr
from datetime import datetime, timedelta
from db_connector import connect_db

# Function to prompt user for the year and month
# this can prob be changed later to work with a button or something
# instead of prompting user.
def get_user_date():
   while True:
      try:
         year = int(input("Enter year (YYYY): "))
         month = int(input("Enter month (1-12): "))
         if 1 <= month <= 12:
            return year, month
         else:
            print("Invalid month! Please enter a number between 1 and 12.")
      except ValueError:
         print("Invalid input! Please enter a valid year and month.")


BACKGROUND_COLOR = 'black'
LABEL_COLOR = 'white'

conn = connect_db()
cursor = conn.cursor()

# Get year and month
year, month = get_user_date()

# Set color of axis/title labels
plt.rcParams['text.color'] = LABEL_COLOR
plt.rcParams['axes.labelcolor'] = LABEL_COLOR
plt.rcParams['axes.edgecolor'] = LABEL_COLOR
plt.rcParams['xtick.color'] = LABEL_COLOR
plt.rcParams['ytick.color'] = LABEL_COLOR

# Set background color of graph
fig = plt.figure(figsize=(10,5))
fig.patch.set_facecolor(BACKGROUND_COLOR)
ax = fig.add_subplot(1,1,1)
ax.set_facecolor(BACKGROUND_COLOR)

#Generate all dates for the month
days_in_month = (datetime(year, month % 12 + 1, 1) - timedelta(days=1)).day
all_dates = [f"{year}-{month:02d}-{day:02d}" for day in range(1, days_in_month + 1)]

# Get weights from table
cursor.execute("""
   SELECT DATE(recordedAT) AS log_date, weight
   FROM progress
   WHERE YEAR(recordedAT) = %s AND MONTH(recordedAT) = %s
   """, (year,month))

data = {date: None for date in all_dates} # Initialize all days as None
for log_date, weight in cursor.fetchall():
   data[str(log_date)] = weight # Fill actual values from the database

cursor.close()
conn.close()

#Create DataFrame
dates = list(data.keys())
weights = list(data.values())

# Graph scatter plot
plt.plot(dates, weights, label="Weight", color='yellow', linestyle='solid', linewidth=1,
   marker="*", markerfacecolor='yellow')

plt.xlabel("Date")
plt.ylabel("Weight (lbs)")
plt.title(f"Weight Log for {datetime(year, month, 1).strftime('%B %Y')}")
plt.grid(True, linestyle="--", alpha=0.6)
plt.show()
