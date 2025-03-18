document.addEventListener("DOMContentLoaded", function () {
    const categorySelect = document.getElementById("category");
    const leaderboardTable = document.querySelector("#leaderboard-table tbody");

    function updateLeaderboard() {
        const category = categorySelect.value;

        fetch("fetch_leaderboard.php?category=" + category)
            .then(response => response.json())
            .then(data => {
                leaderboardTable.innerHTML = ""; // Clear existing data

                let rank = 1;
                data.forEach(row => {
                    leaderboardTable.innerHTML += `
                        <tr>
                            <td>${rank}</td>
                            <td>${row.fullName}</td>
                            <td>${row.goal} ${category === "distance" ? "miles" : "kcal"}</td>
                            <td>${row.currentStatus} ${category === "distance" ? "miles" : ""}</td>
                            <td>${row.goalCompletion}%</td>
                        </tr>`;
                    rank++;
                });
            })
            .catch(error => console.error("Error loading leaderboard:", error));
    }

    categorySelect.addEventListener("change", updateLeaderboard);

    updateLeaderboard(); // Load initial data on page load
});

