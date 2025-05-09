import matplotlib.pyplot as plt
import mysql.connector
import sys
import os
from datetime import datetime, timedelta

# Read year, month, and user ID from command-line arguments
year = int(sys.argv[1])
month = int(sys.argv[2])
user_id = int(sys.argv[3])

BACKGROUND_COLOR = 'black'
LABEL_COLOR = 'white'

# Connect to database
conn = mysql.connector.connect(
   host="joecool.highpoint.edu",
   user="ejerrier",
   password="1788128",
   database="csc4710_S25_missioncritical",
   charset="utf8mb4",
   collation="utf8mb4_general_ci"
)
cursor = conn.cursor()

# Generate all dates for the month
days_in_month = (datetime(year, month % 12 + 1, 1) - timedelta(days=1)).day
all_dates = [f"{year}-{month:02d}-{day:02d}" for day in range(1, days_in_month + 1)]

# Get weights from database for the specific user
cursor.execute("""
   SELECT DATE(recordedAT) AS log_date, weight
   FROM progress
   WHERE YEAR(recordedAT) = %s AND MONTH(recordedAT) = %s AND user_id = %s
""", (year, month, user_id))

# Store data from database
data = {date: None for date in all_dates}  # Initialize all days as None
for log_date, weight in cursor.fetchall():
   data[str(log_date)] = weight  # Fill actual values from database

cursor.close()
conn.close()

# Prepare data for graph
dates = []
weights = []

for date, weight in data.items():
   if weight is not None:  # Only plot existing data points
      dates.append(datetime.strptime(date, "%Y-%m-%d"))
      weights.append(weight)

# Set graph appearance
plt.rcParams['text.color'] = LABEL_COLOR
plt.rcParams['axes.labelcolor'] = LABEL_COLOR
plt.rcParams['axes.edgecolor'] = LABEL_COLOR
plt.rcParams['xtick.color'] = LABEL_COLOR
plt.rcParams['ytick.color'] = LABEL_COLOR

fig = plt.figure(figsize=(10, 5))
fig.patch.set_facecolor(BACKGROUND_COLOR)
ax = fig.add_subplot(1, 1, 1)
ax.set_facecolor(BACKGROUND_COLOR)

# Plot data
if dates and weights:
   plt.plot(dates, weights, label="Weight", color='yellow', linestyle='solid', linewidth=1,
         marker="*", markerfacecolor='yellow')

# Sort data by date to ensure correct chronological order
if dates:
   dates, weights = zip(*sorted(zip(dates, weights)))
else:
   dates, weights = [], []

# Convert dates to string format for plotting and ensure correct x-tick order
dates = [date.strftime("%Y-%m-%d") for date in dates]
all_dates_dt = [datetime.strptime(date, "%Y-%m-%d") for date in all_dates]  # Convert all_dates to datetime
all_dates_dt.sort()  # Ensure sorted order
all_dates = [date.strftime("%Y-%m-%d") for date in all_dates_dt]  # Convert back to string

# Set all dates as x-ticks in the correct order
plt.xticks(all_dates, rotation=90)

plt.xlabel("Date")
plt.ylabel("Weight (lbs)")
plt.title(f"Weight Log for {datetime(year, month, 1).strftime('%B %Y')}")
plt.grid(True, linestyle="--", alpha=0.6)

# Ensure output directory exists
output_path = f"./frontend/images/weightGraph_{user_id}_{year}_{month}.png"
os.makedirs(os.path.dirname(output_path), exist_ok=True)

# Save the graph
plt.savefig(output_path, bbox_inches='tight')
print(output_path)  # Print the file path for PHP