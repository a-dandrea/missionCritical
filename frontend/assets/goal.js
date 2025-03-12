console.log("goal.js loaded");

document.getElementById("goal-update-form").addEventListener("submit", function(event) {
    event.preventDefault();  // Prevent page reload

    const goal = document.getElementById("goal").value;

    // Debugging: Log values to check if they're correct
    console.log("Selected Goal:", goal);

    if (goal === "") {
        document.getElementById("message").textContent = "Please select a goal.";
        console.log("No goal selected.");
        return;
    }

    fetch("update_goal.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ goal: goal })  // Ensure proper encoding
    })
    .then(response => response.json())
    .then(data => {
        console.log("Server Response:", data);  // Debugging: Log response
        document.getElementById("message").textContent = data.message;
    })
    .catch(error => console.error("Fetch error:", error));
});
