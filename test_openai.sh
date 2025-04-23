curl https://api.openai.com/v1/chat/completions \
   -H "Authorization: Bearer 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" \
   -H "Content-Type:application/json" \
   -d '{
      "model": "gpt-3.5-turbo",
      "messages": [{"role": "user", "content": "Hello, who are you?"}]
   }'
