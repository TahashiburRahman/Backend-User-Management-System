<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "formtb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'id' parameter is set in the URL
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Fetch combined data using the provided user ID
    $sqlCombinedData = "SELECT users.*, personal_info.* FROM users LEFT JOIN personal_info ON users.id = personal_info.user_id WHERE users.id = $userID";
    $result = $conn->query($sqlCombinedData);

    // Display the result
    if ($result->num_rows > 0) {
        echo "<h2>Combined Data</h2>";
        echo "<table border='1'>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th>Contact</th>
                    <th>Father's Name</th>
                    <th>Mother's Name</th>
                    <th>Address</th>
                    <th>Action</th>
                </tr>";

        $row = $result->fetch_assoc();
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['password']}</td>
                <td>{$row['contact']}</td>
                <td>{$row['father_name']}</td>
                <td>{$row['mother_name']}</td>
                <td>{$row['address']}</td>
                <td>
                    <a href='edit.php?id={$row['id']}'>Edit</a>
                    <button onclick='deleteRecord({$row['id']})'>Delete</button>
                </td>
            </tr>";
        
        echo "</table>";
    } else {
        echo "<p>No records found</p>";
    }
} else {
    echo "<p>Invalid request</p>";
}

$conn->close();
?>
<script>
    function deleteRecord(userId) {
        if (confirm("Are you sure you want to delete this record?")) {
            // Perform an asynchronous request to delete the record
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'delete.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            // Send the delete_user parameter
            var params = 'delete_user=' + userId;
            xhr.onload = function () {
                // Redirect to another page after deletion
                window.location.reload();
            };

            xhr.send(params);
        }
    }
</script>
