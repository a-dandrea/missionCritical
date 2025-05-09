import os
os.environ['MPLCONFIGDIR'] = '/tmp/matplotlib'
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.dates as mdates
import mysql.connector
import sys
from datetime import datetime, timedelta

import traceback

sys.stderr = open('/tmp/python_error.log', 'w')
def log_exception(exc_type, exc_value, exc_tb):
    traceback.print_exception(exc_type, exc_value, exc_tb, file=sys.stderr)

sys.excepthook = log_exception

try:
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
      SELECT DATE(date) AS log_date, daily_step_count AS steps
      FROM daily_steps
      WHERE YEAR(date) = %s AND MONTH(date) = %s AND user_id = %s
   """, (year, month, user_id))

   # Store data from database
   data = {date: 0 for date in all_dates}  # Initialize all days as None
   for log_date, step_count in cursor.fetchall():
      data[str(log_date)] = step_count  # Fill actual values from database

   cursor.execute("""
      SELECT daily_step_goal
      FROM users
      WHERE user_id = %s
   """, (user_id,))

   # Get daily step goal for the user
   goal_result = cursor.fetchone()
   daily_step_goal = goal_result[0] if goal_result else 0

   cursor.close()
   conn.close()

   # Prepare data for graph
   dates = [datetime.strptime(date, "%Y-%m-%d") for date in data.keys()]
   steps = list(data.values())



   # Set graph appearance
   plt.rcParams['text.color'] = LABEL_COLOR
   plt.rcParams['axes.labelcolor'] = LABEL_COLOR
   plt.rcParams['axes.edgecolor'] = LABEL_COLOR
   plt.rcParams['xtick.color'] = LABEL_COLOR
   plt.rcParams['ytick.color'] = LABEL_COLOR

   fig, ax = plt.subplots(figsize=(12, 6))
   fig.patch.set_facecolor(BACKGROUND_COLOR)
   ax.set_facecolor(BACKGROUND_COLOR)

   # Format x-axis
   ax.xaxis.set_major_locator(mdates.DayLocator(interval=1))  # Show every day
   ax.xaxis.set_major_formatter(mdates.DateFormatter("%Y-%m-%d"))  # Format as YYYY-MM-DD


   # Plot data
   ax.bar(dates, steps, label="Steps", color='yellow', width=0.6)

   # Convert dates to string format for plotting and ensure correct x-tick order
   all_dates_dt = [datetime.strptime(date, "%Y-%m-%d") for date in all_dates]  # Convert all_dates to datetime
   all_dates_dt.sort()  # Ensure sorted order
   all_dates = [date.strftime("%Y-%m-%d") for date in all_dates_dt]  # Convert back to string

   # Set all dates as x-ticks in the correct order
   plt.xticks(all_dates, rotation=90)
   plt.ylim(bottom=0)  # Ensure y-axis starts from 0 for better visualization

   plt.axhline(y=daily_step_goal, color='navy', linestyle='--', label='Daily Step Goal')

   plt.xlabel("Date")
   plt.ylabel("Steps")
   plt.title(f"Step Log for {datetime(year, month, 1).strftime('%B %Y')}")
   plt.grid(True, linestyle="--", alpha=0.6)

   # Ensure output directory exists
   output_path = f"./frontend/images/stepGraph_{user_id}_{year}_{month}.png"
   os.makedirs(os.path.dirname(output_path), exist_ok=True)

   # Save the graph
   plt.savefig(output_path, bbox_inches='tight')
   print(output_path)  # Print the file path for PHP
except Exception as e:
   print("Exception occurred:", file=sys.stderr)
   traceback.print_exc(file=sys.stderr)
   sys.exit(1)