import matplotlib.pyplot as plt
import mysql.connector
import sys
from datetime import datetime, timedelta

# Read year and month from command-line arguments
if len(sys.argv) != 3:
    print("Error: Please provide year and month as arguments.")
    sys.exit(1)

try:
    year = int(sys.argv[1])
    month = int(sys.argv[2])

    if not (1 <= month <= 12):
        print("Error: Invalid month. Must be between 1 and 12.")
        sys.exit(1)
except ValueError:
    print("Error: Invalid input. Year and month must be integers.")
    sys.exit(1)

BACKGROUND_COLOR = 'black'
LABEL_COLOR = 'white'

# Connect to database
conn = mysql.connector.connect(
    host="joecool.highpoint.edu",
    user="knguyen",
    password="1871644",
    database="csc4710_S25_missioncritical"
)
cursor = conn.cursor()

# Generate all dates for the month
days_in_month = (datetime(year, month % 12 + 1, 1) - timedelta(days=1)).day
all_dates = [f"{year}-{month:02d}-{day:02d}" for day in range(1, days_in_month + 1)]

# Get weights from database
cursor.execute("""
    SELECT DATE(recordedAT) AS log_date, weight
    FROM progress
    WHERE YEAR(recordedAT) = %s AND MONTH(recordedAT) = %s
""", (year, month))

data = {date: None for date in all_dates}  # Initialize all days as None
for log_date, weight in cursor.fetchall():
    data[str(log_date)] = weight  # Fill actual values from database

cursor.close()
conn.close()

# Prepare data for graph
dates = list(data.keys())
weights = list(data.values())

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
plt.plot(dates, weights, label="Weight", color='yellow', linestyle='solid', linewidth=1,
         marker="*", markerfacecolor='yellow')

plt.xlabel("Date")
plt.ylabel("Weight (lbs)")
plt.title(f"Weight Log for {datetime(year, month, 1).strftime('%B %Y')}")
plt.grid(True, linestyle="--", alpha=0.6)

# Save the graph as an image file
output_path = "../frontend/images/weightGraph.png"  # Adjust path as needed
plt.savefig(output_path, bbox_inches='tight')
print(output_path)  # Print the file path for PHP
