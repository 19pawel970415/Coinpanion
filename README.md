### A fully documented project of Coinpanion - an app for personal budgeting being a part of the University of Lodz Software Engineering course.

# Expenses Management System - Coinpanion

## Overview

This project is a simple expenses management system that includes user registration, login, profile management, and expenses tracking. Users can register, log in, update their profiles, generate expenses reports, and manage their expenses.

## File Descriptions

### `process_registration.php`

Handles user registration by connecting to the database, processing form data, verifying password match, hashing the password, and inserting the new user into the database.

```php
<?php
$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = $_POST["password"];
    $password_repeat = $_POST["password_repeat"];

    if ($password != $password_repeat) {
        die("Passwords do not match");
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (FirstName, LastName, Email, Username, Password) VALUES ('$first_name', '$last_name', '$email', '$username', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: welcome.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
```

### process_login.php

Handles user login by checking the provided username and password, verifying the password, and redirecting users upon successful login. It also includes an alert function for error messages.

```php
<?php
session_start();

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, username, password FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row["password"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_username"] = $row["username"];
            header("Location: main.php");
            exit();
        } else {
            echo '<script>alert("Incorrect password. Please try again."); window.location.href = "login.html";</script>';
            exit();
        }
    } else {
        echo '<script>alert("User does not exist. Please try again."); window.location.href = "login.html";</script>';
        exit();
    }
}

function function_alert($message) { 
    echo "<script>alert('$message');</script>"; 
}
?>
```

### main.php

Displays user information if logged in, or redirects to the main page if not authorized.


```php
<?php
session_start();

if (isset($_SESSION["user_id"])) {
    echo '<div class="logged-in-info">Logged in as: ' . $_SESSION["user_username"] . ' (User ID: ' . $_SESSION["user_id"] . ')</div>';
} else {
    echo '<div class="unauthorized-message">Unauthorized access, redirecting to the main page...</div>';
    header("Refresh: 3; URL=index.html");
    exit();
}
?>
```

### process_edit_profile.php

Handles profile updates, including updating user details and handling password changes.

```php
<?php
session_start();

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$first_name = $_POST["first_name"];
$last_name = $_POST["last_name"];
$email = $_POST["email"];
$username = $_POST["username"];
$password = $_POST["password"];
$password_repeat = $_POST["password_repeat"];

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$update_sql = "UPDATE users SET FirstName='$first_name', LastName='$last_name', Email='$email', Username='$username', Password='$hashed_password' WHERE id='$user_id'";

if ($conn->query($update_sql) === TRUE) {
    echo '<script>alert("Profile updated successfully."); window.location.href = "profile.php";</script>';
    exit();
} else {
    echo "Error updating profile: " . $conn->error;
}

$select_sql = "SELECT FirstName, LastName, Email, Username FROM users WHERE id = '$user_id'";
$result = $conn->query($select_sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $first_name = $row["FirstName"];
    $last_name = $row["LastName"];
    $email = $row["Email"];
    $username = $row["Username"];
} else {
    echo "Error fetching user data: User not found";
    exit();
}
?>
```

### generate_report.php

Generates an expense report based on user-selected parameters such as date and category.

```php
<?php
session_start();

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$categoriesQuery = "SELECT DISTINCT category FROM expenses WHERE user_id = {$_SESSION["user_id"]}";
$categoriesResult = $conn->query($categoriesQuery);
$existingCategories = [];
while ($categoryRow = $categoriesResult->fetch_assoc()) {
    $existingCategories[] = $categoryRow["category"];
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["start-date"]) && isset($_GET["end-date"])) {
        $startDate = date("Y-m-d", strtotime($_GET["start-date"]));
        $endDate = date("Y-m-d", strtotime($_GET["end-date"]));
        $selectedCategories = isset($_GET["category"]) ? $_GET["category"] : [];

        $categoryCondition = "";
        if (!empty($selectedCategories)) {
            $categoryCondition = "AND category IN ('" . implode("','", $selectedCategories) . "')";
        }

        $query = "SELECT * FROM expenses 
                  WHERE user_id = {$_SESSION["user_id"]} 
                  AND date BETWEEN '$startDate' AND '$endDate' 
                  $categoryCondition";
        $result = $conn->query($query);

        echo '<table>';
        echo '<tr><th>Date</th><th>Amount</th><th>Category</th><th>Description</th></tr>';
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["date"] . "</td>";
            echo "<td>" . $row["amount"] . "</td>";
            echo "<td>" . $row["category"] . "</td>";
            echo "<td>" . $row["expense_name"] . "</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<div class="error-message">Missing required data for report generation.</div>';
    }
}
?>
```

### logout.php

Handles user logout by clearing session data and destroying the session.

```php
<?php
session_start();
session_unset();
session_destroy();
?>
```

### table.php

Displays user expenses with filtering and allows editing and deleting of expenses.

```php
<?php
session_start();

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
$searchCategory = $_GET["category"] ?? '';
$searchDate = $_GET["date"] ?? '';
$searchDescription = $_GET["description"] ?? '';
$searchAmount = $_GET["amount"] ?? '';

$sql = "SELECT id, date, category, expense_name, amount FROM expenses WHERE user_id = $user_id";

if (!empty($searchCategory)) {
    $sql .= " AND category LIKE '%$searchCategory%'";
}

if (!empty($searchDate)) {
    $sql .= " AND date = '$searchDate'";
}

if (!empty($searchDescription)) {
    $sql .= " AND expense_name LIKE '%$searchDescription%'";
}

if (!empty($searchAmount)) {
    $sql .= " AND amount = $searchAmount";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row["date"] . "</td>";
        echo "<td>" . $row["amount"] . "</td>";
        echo "<td>" . $row["category"] . "</td>";
        echo "<td>" . $row["expense_name"] . "</td>";
        echo "<td><a href='#' onclick='openEditPopup(" . $row["id"] . ", \"" . $row["date"] . "\", \"" . $row["category"] . "\", \"" . $row["expense_name"] . "\", " . $row["amount"] . ")'><img src='images/edit.png' alt='Edit'></a> ";
        echo "<a href='#' onclick='deleteExpense(" . $row["id"] . ")'><img src='images/delete.png' alt='Delete'></a></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5'>No search results found.</td></tr>";
}
?>
```

### save_expense.php

Saves a new expense into the database.

```php
Skopiuj kod
<?php
function saveExpense($conn, $date, $category, $expenseName, $amount, $userId) {
    $stmt = $conn->prepare("INSERT INTO expenses (date, category, expense_name, amount, user_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }
    $stmt->bind_param("ssssd", $date, $category, $expenseName, $amount, $userId);
    $stmt->execute();
    $stmt->close();
}
?>
```

### edit_expense.php

Edits an existing expense in the database.

```php
<?php
function editExpense($conn, $editedDate, $editedCategory, $editedExpenseName, $editedAmount, $editedExpenseId) {
    $stmt = $conn->prepare("UPDATE expenses SET date = ?, category = ?, expense_name = ?, amount = ? WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }
    $userId = $_SESSION["user_id"];
    $stmt->bind_param("ssssdd", $editedDate, $editedCategory, $editedExpenseName, $editedAmount, $editedExpenseId, $userId);
    $stmt->execute();
    $stmt->close();
}
?>
```

### delete_expense.php

Deletes an existing expense from the database.

```php
<?php
function deleteExpense($conn, $expenseId) {
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");
    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }
    $userId = $_SESSION["user_id"];
    $stmt->bind_param("dd", $expenseId, $userId);
    $stmt->execute();
    $stmt->close();
}
?>
```

## Setup Instructions

### Database Configuration:

Create a MySQL database and import the necessary tables (e.g., users, expenses).
Update the database credentials in the PHP files as needed.

### Deploy the Application:

Place all PHP files in the web server's root directory.
Ensure your web server supports PHP and is correctly configured.

### Testing:

Register a new user via registration.html.
Log in via login.html and test user functionality.
Access main.php to manage expenses and generate reports.

### License

This project is licensed under the MIT License - see the LICENSE file for details.

Feel free to adjust or add additional sections as necessary :)







