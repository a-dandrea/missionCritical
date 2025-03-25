document.getElementById("graphForm").addEventListener("submit", function(event) {
   event.preventDefault(); // Prevent form submission

   let year = document.getElementById("year").value;
   let month = document.getElementById("month").value;
   let user_id = document.getElementById("user_id").value;

   let baseURL = "./frontend/images/";
   let imageName = "weightGraph_" + user_id + "_" + year + "_" + month;
   let extention = ".png";

   // Call PHP script to generate the graph
   fetch(`../backend/display_graph.php?year=${year}&month=${month}`)
    .then(response => response.json())  // Expect JSON
    .then(data => {
        console.log("Server response:", data);
        if (data.status === "success") {
            document.getElementById("graphImage").src = data.path + "?" + new Date().getTime();
            document.getElementById("graphImage").style.display = "block";
        } else {
            console.error("Error:", data.message);
        }
    })
    .catch(error => console.error("Fetch error:", error));

});

document.getElementById("graphImage").src = baseURL + imageName + extension;