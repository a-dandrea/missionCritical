/* 
  2 const leaderboardData = {
  3     calories: [
  4         { username: 'User1', goal: 1500, value: 1200 },
  5         { username: 'User2', goal: 1200, value: 1000 },
  6         { username: 'User3', goal: 1000, value: 850 }
  7     ],
  8     steps: [
  9         { username: 'User1', goal: 20000, value: 15000 },
 10         { username: 'User2', goal: 18000, value: 12000 },
 11         { username: 'User3', goal: 15000, value: 10000 }
 12     ],
 13     distance: [
 14         { username: 'User1', goal: 60, value: 50 },
 15         { username: 'User2', goal: 50, value: 40 },
 16         { username: 'User3', goal: 40, value: 30 }
 17     ]
 18 };
 19 */

let leaderboardData = {}; // Object to store fetched leaderboard data

// Function to fetch data from PHP and update the page
function sendRequest() {
    let category = document.getElementById('category').value; // Get selected category
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "leaderboard.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            try {
                leaderboardData = JSON.parse(xhr.responseText);
                updateLeaderboard();
            } catch (error) {
                console.error("Error parsing JSON:", error);
                document.getElementById("response").innerHTML = xhr.responseText;
            }
        }
    };
    xhr.send("category=" + encodeURIComponent(category)); // Send category to PHP
}

// Function to update the leaderboard based on the selected category
function updateLeaderboard() {
    const category = document.getElementById('category').value;
    const tableBody = document.getElementById('leaderboard-table').getElementsByTagName('tbody')[0];

    tableBody.innerHTML = ''; // Clear existing table rows

    const data = leaderboardData[category] || [];
    if (data.length === 0) {
        tableBody.innerHTML = "<tr><td colspan='5'>No data available</td></tr>";
        return;
    }

    data.sort((a, b) => (b.value / b.goal) - (a.value / a.goal)); // Sort by goal completion %

    data.forEach((entry, index) => {
        const percentage = ((entry.value / entry.goal) * 100).toFixed(2);
        const row = tableBody.insertRow();
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>${entry.username}</td>
            <td>${entry.goal}</td>
            <td>${entry.value}</td>
            <td>${percentage}%</td>
        `;
    });
}

// Initialize leaderboard when page loads
document.addEventListener("DOMContentLoaded", function () {
    document.getElementById("category").addEventListener("change", sendRequest);
    sendRequest(); // Load initial data
});

