document.getElementById("graphForm").addEventListener("submit", function(event) {
   event.preventDefault(); // Prevent form submission

   let year = document.getElementById("year").value;
   let month = document.getElementById("month").value;

   // Call PHP script to generate the graph
   fetch(`display_graph.php?year=${year}&month=${month}`)
       .then(response => response.text())
       .then(data => {
           document.getElementById("graphImage").src = "graph.png?" + new Date().getTime();
           document.getElementById("graphImage").style.display = "block";
       })
       .catch(error => console.error("Error:", error));
});