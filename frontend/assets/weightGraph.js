document.getElementById("graphForm").addEventListener("submit", function(event) {
   event.preventDefault(); // Prevent form submission

   let year = document.getElementById("year").value;
   let month = document.getElementById("month").value;
   let user_id = document.getElementById("user_id").value;

   // Call PHP script to generate the graph
   try {
   fetch(`../backend/display_graph.php?year=${year}&month=${month}`)
   .then(response => response.json())  // Expect JSON
   .then(data => {
      console.log("Server response:", data);
      if (data.status === "success") {
         document.getElementById("weightGraphImage").src = data.path;
         document.getElementById("weightGraphImage").style.display = "block";
      } else {
         console.error(`Error on line ${new Error().lineNumber}:`, data.message);
      }
   })
   .catch(error => console.error(`Fetch error on line ${new Error().lineNumber}:`, error));

   } catch (error) {
      console.error(`Unexpected error on line ${new Error().lineNumber}:`, error);
   }
});