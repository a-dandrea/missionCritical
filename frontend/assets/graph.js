document.getElementById("graphForm").addEventListener("submit", function(event) {
   event.preventDefault(); // Prevent form submission

   let year = document.getElementById("year").value;
   let month = document.getElementById("month").value;

   // Call PHP script to generate the graph
   fetch(`../backend/display_graph.php?year=${year}&month=${month}`)
       .then(response => response.text())
       .then(data => {
         console.log("Server response:", data);
         if (data.status === "success") {
             document.getElementById("graphImage").src = data.path + "?" + new Date().getTime();
             document.getElementById("graphImage").style.display = "block";
         } else {
             console.error("Error:", data.message);
         }
     })
       .catch(error => console.error("Error:", error));
});