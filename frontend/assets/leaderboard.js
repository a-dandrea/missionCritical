/* 
const leaderboardData = {
    calories: [
        { username: 'User1', goal: 1500, value: 1200 },
        { username: 'User2', goal: 1200, value: 1000 },
        { username: 'User3', goal: 1000, value: 850 }
    ],
    steps: [
        { username: 'User1', goal: 20000, value: 15000 },
        { username: 'User2', goal: 18000, value: 12000 },
        { username: 'User3', goal: 15000, value: 10000 }
    ],
    distance: [
        { username: 'User1', goal: 60, value: 50 },
        { username: 'User2', goal: 50, value: 40 },
        { username: 'User3', goal: 40, value: 30 }
    ]
};
*/

let leaderboardData = {};

// Function to fetch data from PHP and update the page
function sendRequest() {
    // Create new XMLHttpRequest (AJAX)
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "leaderboard.php", true); // Specify the PHP file that will return leaderboard
    xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && xhr.status == 200)
            try {
                leaderboardData = JSON.parse(xhr.responseText);
                updateLeaderboard();
            } catch (error) {
                console.error("Error parsing JSON response:", error);
                document.getElementById("response").innerHTML = xhr.responseText;
            }
    };
    xhr.send(); // Send the request to the server
}

// Function to update the leaderboard based on the selected category
function updateLeaderboard() {
    const category = document.getElementById('category').value; // Get selected category
    const tableBody = document.getElementById('leaderboard-table').getElementsByTagName('tbody')[0];

    // Clear existing table rows
    tableBody.innerHTML = '';

    // Get the data for the selected category
    const data = leaderboardData[category];

    // Sort the data based on percentage of goal completion (descending order)
    data.sort((a, b) => {
        const percentageA = (a.value / a.goal) * 100;
        const percentageB = (b.value / b.goal) * 100;
        return percentageB - percentageA; // Sort in descending order
    });

    // Populate the table with data
    data.forEach((entry, index) => {
        const percentage = ((entry.value / entry.goal) * 100).toFixed(2); // Calculate percentage
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
window.onload = sendRequest;

