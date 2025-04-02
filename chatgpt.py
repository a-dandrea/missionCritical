# Ky Nguyen 2025-03-26
# This program aims to integrate ChatGPT's API into missionCritical;s fitness app project to provide smart workout suggestions 

# sending prompt to GPT via API
import openai

print("OpenAI package imported successfully!")

openai.api_key = "your-api-key"

def get_workout_plan(user_input):
   prompt = (
      f"Create  a weekly workout plan for someone with these preferences:\n"
      f"Goal: {user_input['goal']}\n"
      f"Fitness Level: {user_input['workout_type']}\n"
      f"Time per Session: {user_input['time_per_session']}\n"
      f"Days per Weel: {user_input['days_per_week']}\n"
      f"Format the response in a clear weekly schedule."
   )

   response = openai.ChatCompletion.creat(
      model="gpt-4",
      messages=[{"role": "user", "content": prompt}]
   )
  
   return response['choices'][0]['message']['content']

