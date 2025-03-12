import mysql.connector

def connect_db():
   # Connect to MySQL
   connection = mysql.connector.connect(
      host="joecool.highpoint.edu",      # Your MySQL host (default is localhost)
      user="knguyen",           # Your MySQL username
      password="knguyen1871644",   # Your MySQL password
      database="csc4710_S25_missioncritical"  # Your database name
   )
   return connection
