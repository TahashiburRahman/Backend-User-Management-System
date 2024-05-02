<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "formtb";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Disable caching
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $contact = $_POST['contact'];
    $father_name = $_POST['father_name'];
    $mother_name = $_POST['mother_name'];
    $address = $_POST['address'];

    // Insert data into users table
    $sqlUsers = "INSERT INTO users (name, email, password, contact) VALUES ('$name', '$email', '$password', '$contact')";
    $conn->query($sqlUsers);

    // Get the last inserted user ID
    $userID = $conn->insert_id;

    // Insert data into personal_info table
    $sqlPersonalInfo = "INSERT INTO personal_info (user_id, father_name, mother_name, address) VALUES ('$userID', '$father_name', '$mother_name', '$address')";
    $conn->query($sqlPersonalInfo);

    // Redirect to a new page to avoid form resubmission
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
// Check if the form is submitted for deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user'])) {
    $deleteUserID = $_POST['delete_user'];

    // Use transactions for atomic operations
    $conn->begin_transaction();

    try {
        // Select the user's ID before deletion
        $sqlSelectUserID = "SELECT id FROM users WHERE id = '$deleteUserID'";
        $resultSelectUserID = $conn->query($sqlSelectUserID);

        if ($resultSelectUserID->num_rows > 0) {
            // Delete data from personal_info table
            $sqlDeletePersonalInfo = "DELETE FROM personal_info WHERE user_id = '$deleteUserID'";
            $conn->query($sqlDeletePersonalInfo);

            // Delete data from users table
            $sqlDeleteUser = "DELETE FROM users WHERE id = '$deleteUserID'";
            $conn->query($sqlDeleteUser);

            // Commit the transaction
            $conn->commit();

            // Redirect to a new page to avoid form resubmission
            header("Location: ".$_SERVER['PHP_SELF']);
            exit();
        } else {
            echo "Invalid user ID";
        }
    } catch (Exception $e) {
        // Rollback the transaction on error
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
}


// Fetch the data from the users table only, excluding deleted records
$sqlFetchData = "SELECT * FROM users WHERE id IN (SELECT id FROM users)";
$result = $conn->query($sqlFetchData);

$conn->close();
?>

<!-- Rest of your HTML and script code -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" integrity="sha512-jnSuA4Ss2PkkikSOLtYs8BlYIeeIK1h99ty4YfvRPAlzr377vr3CXDb7sb7eEEBYjDtcYj+AjBH3FLv5uSJuXg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer"/>
    <link rel="stylesheet" href="style.css">
    <title>PHP project</title>
</head>
<body>
<!-- navbar -->
<nav class="navbar sticky-top navbar-expand-lg bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">Logo</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fa-solid fa-bars" style="color: #FFD43B;"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto w-100 justify-content-end">
                <li class="nav-item active">
                    <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Contact</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Other</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- slider -->
<div id="carouselExampleAutoplaying" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <img src="balls.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="goku.jpg" class="d-block w-100" alt="...">
        </div>
        <div class="carousel-item">
            <img src="kame.jpg" class="d-block w-100" alt="...">
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleAutoplaying" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<h2>Multi-Table Form</h2>

<!-- Display the form -->
<form action="" method="post">
    <!-- User Information -->
    <label for="name">Name:</label>
    <input type="text" name="name" required>

    <label for="email">Email:</label>
    <input type="email" name="email" required>

    <label for="password">Password:</label>
    <input type="password" name="password" required>

    <label for="contact">Contact:</label>
    <input type="text" name="contact" required>

    <!-- Personal Information -->
    <label for="father_name">Father's Name:</label>
    <input type="text" name="father_name" required>

    <label for="mother_name">Mother's Name:</label>
    <input type="text" name="mother_name" required>

    <label for="address">Address:</label>
    <input type="text" name="address" required>

    <input type="submit" value="Submit">
</form>

<!-- Display the result -->
<?php
if ($result->num_rows > 0) {
    echo "<h2>Users Data</h2>";
    echo "<table border='1'>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Password</th>
                <th>Contact</th>
                <th>View Personal Info</th>
                <th>Action</th>
            </tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['password']}</td>
                <td>{$row['contact']}</td>
                <td><button onclick=\"redirectToCombinedData({$row['id']})\">View</button></td>

            </tr>";
    }
    echo "</table>";
} else {
    echo "<p>No records found</p>";
}
?>
<!--                 <td>
                <button class='edit-button' data-user-id='{$row['id']}'>Edit</button>
                <button class='delete-button' data-user-id='{$row['id']}'>Delete</button>
            </td> -->
<!-- <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Add event listeners to edit buttons
        var editButtons = document.querySelectorAll('.edit-button');
        editButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var userId = this.getAttribute('data-user-id');
                // Redirect to the edit page or implement in-place editing
                // You can use window.location.href or a modal for editing
                alert('Implement editing for user with ID ' + userId);
            });
        });

        // Add event listeners to delete buttons (as you've done before)
        var deleteButtons = document.querySelectorAll('.delete-button');
        deleteButtons.forEach(function (button) {
            button.addEventListener('click', function () {
                var userId = this.getAttribute('data-user-id');
                deleteRecord(userId);
            });
        });

        // Function to handle asynchronous record deletion
        function deleteRecord(userId) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            var params = 'delete_user=' + userId;
            xhr.onload = function () {
                window.location.href = 'http://localhost/forms/index.php';
            };

            xhr.send(params);
        }
    });
</script> -->

<script>
    function redirectToCombinedData(userId) {
        window.location.href = "combined_data.php?id=" + userId;
    }
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.min.js" integrity="sha512-ykZ1QQr0Jy/4ZkvKuqWn4iF3lqPZyij9iRv6sGqLRdTPkY69YX6+7wvVGmsdBbiIfN/8OdsI7HABjvEok6ZopQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="script.js"></script>
</body>
</html>
