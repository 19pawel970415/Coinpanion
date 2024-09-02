### A fully documented project of Coinpanion - an app for personal budgeting being a part of the University of Lodz Software Engineering course.

# Expenses Management System - Coinpanion

## Overview

This project is a simple expenses management system that includes user registration, login, profile management, and expenses tracking. Users can register, log in, update their profiles, generate expenses reports, and manage their expenses.

## File Descriptions

### `process_registration.php`

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

#### Detailed Explanation of the PHP Script

##### Database Connection Configuration

- `$servername`, `$username`, `$password`, and `$dbname` are variables holding the credentials required to connect to the MySQL database.
- `new mysqli($servername, $username, $password, $dbname)` creates a new MySQLi object to establish a connection with the database.

##### Connection Check

- `$conn->connect_error` checks if there was an error connecting to the database.
- If there's a connection error, the script stops executing using `die()` and outputs an error message.

##### Form Data Handling

- `$_SERVER["REQUEST_METHOD"] == "POST"` checks if the form was submitted using the POST method.
- Form inputs are retrieved using the `$_POST[]` superglobal array. These inputs include `first_name`, `last_name`, `email`, `username`, `password`, and `password_repeat`.

##### Password Validation

- The script checks if the `password` and `password_repeat` fields match. If they don’t, it stops execution and outputs an error message.

##### Password Hashing

- `password_hash($password, PASSWORD_DEFAULT)` securely hashes the password before storing it in the database. This helps protect user passwords from being exposed even if the database is compromised.

##### SQL Query Preparation

- The SQL query `$sql` is a string that inserts the new user's details into the `users` table. It includes fields for first name, last name, email, username, and the hashed password.

##### Query Execution

- `$conn->query($sql)` executes the SQL query. If the query executes successfully, the user is redirected to `welcome.html`. Otherwise, an error message is displayed, showing the SQL query and error details.

##### Redirection and Exit

- `header("Location: welcome.html")` redirects the user to a welcome page after a successful registration.
- `exit()` ensures that no further code is executed after the redirection.

##### Additional Notes

**Security**

- It's important to use prepared statements (`$conn->prepare()` and `$stmt->bind_param()`) for inserting data into the database to avoid SQL injection vulnerabilities.
- Validating and sanitizing user input can help prevent various security issues.

**Error Handling**

- The script uses basic error handling. For a production environment, consider logging errors to a file and showing user-friendly messages.

### `process_login.php`

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

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` initializes a new session or resumes an existing one. This is required for managing user sessions across different pages.

##### Database Connection Configuration

- `$servername`, `$username`, `$password`, and `$dbname` are variables that store the credentials needed to connect to the MySQL database.
- `new mysqli($servername, $username, $password, $dbname)` creates a new MySQLi object to establish a connection with the database.

##### Connection Check

- `$conn->connect_error` checks if there was an error when trying to connect to the database.
- If a connection error occurs, the script halts execution using `die()` and outputs an error message.

##### Form Data Handling

- `$_SERVER["REQUEST_METHOD"] == "POST"` checks if the form was submitted using the POST method.
- Form inputs are retrieved using the `$_POST[]` superglobal array. In this script, the inputs include `username` and `password`.

##### SQL Query Preparation and Execution

- The SQL query `$sql` is a string that selects the `id`, `username`, and `password` fields from the `users` table where the `username` matches the provided input.
- `$result = $conn->query($sql)` executes the SQL query, and `$result->num_rows` checks if exactly one record was found for the given username.

##### Password Verification

- `password_verify($password, $row["password"])` checks if the entered password matches the hashed password stored in the database.
- If the password is correct, the script sets session variables `$_SESSION["user_id"]` and `$_SESSION["user_username"]`, then redirects the user to `main.php`.
- If the password is incorrect, a JavaScript alert notifies the user, and they are redirected back to `login.html`.

##### Error Handling

- If no user is found with the provided username, a JavaScript alert informs the user, and they are redirected back to `login.html`.

##### Alert Function

- `function function_alert($message)` is a helper function that displays a JavaScript alert with the provided message. This function can be used to show various alerts throughout the script.

### `main.php`

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

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` starts a new session or resumes an existing one. This is essential for accessing and managing session variables throughout the application.

##### User Authentication Check

- `if (isset($_SESSION["user_id"]))` checks if the `user_id` session variable is set, which indicates that the user is logged in.

##### Displaying Logged-In User Information

- If the user is logged in, the script outputs a `div` with the class `logged-in-info`, showing the username and user ID stored in the session variables `$_SESSION["user_username"]` and `$_SESSION["user_id"]`.

##### Handling Unauthorized Access

- If the `user_id` session variable is not set, meaning the user is not logged in, the script:
  - Outputs a `div` with the class `unauthorized-message`, indicating unauthorized access.
  - Uses `header("Refresh: 3; URL=index.html")` to redirect the user to the `index.html` page after a 3-second delay.
  - Calls `exit()` to terminate the script, ensuring no further code is executed after initiating the redirect.

### `process_edit_profile.php`

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

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` starts or resumes a session, enabling the script to access session variables, such as the logged-in user's ID.

##### Database Connection Setup

- Variables `$servername`, `$username`, `$password`, and `$dbname` hold the credentials needed to connect to the MySQL database.
- `new mysqli($servername, $username, $password, $dbname)` creates a new MySQLi object to establish the connection to the database.

##### Connection Error Handling

- The script checks for a connection error using `$conn->connect_error`. If an error is detected, the script stops execution with `die()` and outputs a connection failure message.

##### Retrieving User Input and Session Data

- The script retrieves the logged-in user's ID from the session using `$_SESSION["user_id"]`.
- It also retrieves the form data sent via POST, including `first_name`, `last_name`, `email`, `username`, `password`, and `password_repeat`.

##### Password Hashing

- The script hashes the password using `password_hash($password, PASSWORD_DEFAULT)` before updating it in the database. This ensures that the password is securely stored.

##### SQL Query for Updating User Profile

- The script prepares an SQL `UPDATE` statement to modify the user's profile details in the `users` table. It updates fields like `FirstName`, `LastName`, `Email`, `Username`, and the hashed `Password` where the `id` matches the logged-in user's ID.

##### Executing the Update Query

- The script executes the update query with `$conn->query($update_sql)`. If successful, it displays an alert indicating the profile was updated and redirects the user to `profile.php`. The `exit()` function ensures that no further code is executed after the redirection.

##### Error Handling for Profile Update

- If the update query fails, the script outputs an error message showing the issue.

##### Retrieving Updated User Data

- The script prepares an SQL `SELECT` query to fetch the updated user details from the `users` table using the user ID.
- If the query finds the user, the script retrieves the first name, last name, email, and username from the database and assigns them to respective variables.

##### Handling Errors in Data Retrieval

- If no user data is found, the script outputs an error message indicating that the user was not found and stops execution using `exit()`.

##### Security Considerations

- It’s important to note that this script should be improved by using prepared statements to prevent SQL injection attacks, especially when handling user inputs in the SQL queries.

### `generate_report.php`

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

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` starts or resumes a session, allowing the script to access session variables, such as the user's ID.

##### Database Connection Setup

- Variables `$servername`, `$username`, `$password`, and `$dbname` store the credentials necessary to connect to the MySQL database.
- `new mysqli($servername, $username, $password, $dbname)` creates a new MySQLi object to establish a connection to the database.

##### Connection Error Handling

- The script checks for connection errors using `$conn->connect_error`. If an error occurs, the script stops execution with `die()` and outputs an error message.

##### Retrieving Existing Categories

- The script executes a query to fetch distinct categories from the `expenses` table for the logged-in user:  
  `SELECT DISTINCT category FROM expenses WHERE user_id = {$_SESSION["user_id"]}`.
- It then populates the `$existingCategories` array with the categories retrieved from the database by looping through the results with `$categoriesResult->fetch_assoc()`.

##### Handling GET Request

- The script checks if the request method is `GET` using `$_SERVER["REQUEST_METHOD"] == "GET"`.
- It then verifies if `start-date` and `end-date` parameters are set in the URL query string using `isset()`.

##### Date Processing

- The script converts the `start-date` and `end-date` parameters into `Y-m-d` format using `strtotime()` and `date()` functions.

##### Category Selection and Condition Construction

- The script checks if any categories have been selected via the `category` parameter.
- If categories are selected, it constructs an SQL condition to filter results by the selected categories:  
  `AND category IN ('category1','category2',...)`.

##### SQL Query for Retrieving Expenses

- The script prepares an SQL `SELECT` query to retrieve expenses for the logged-in user within the specified date range, optionally filtered by the selected categories:  
  `SELECT * FROM expenses WHERE user_id = {$_SESSION["user_id"]} AND date BETWEEN '$startDate' AND '$endDate' $categoryCondition`.

##### Displaying Results in an HTML Table

- The script fetches and displays the query results in an HTML table. It includes columns for `Date`, `Amount`, `Category`, and `Description`.
- The script loops through each row returned by the query using `$result->fetch_assoc()`, outputting the corresponding table rows.

##### Error Handling for Missing Data

- If the required `start-date` and `end-date` parameters are not provided, the script displays an error message indicating that data for report generation is missing.

##### Security Considerations

- It’s important to note that this script should be improved by using prepared statements to prevent SQL injection attacks, especially when handling user inputs in the SQL queries.

### `logout.php`

```php
<?php
session_start();
session_unset();
session_destroy();
?>
```

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` starts or resumes a session, enabling the script to manage session data for the user. Even though the purpose here is to end the session, the session must first be started or resumed to perform operations on it.

##### Unsetting Session Variables

- `session_unset()` clears all session variables. It removes all the data stored in the current session but does not destroy the session itself. This step ensures that all user-specific data is removed from the session.

##### Destroying the Session

- `session_destroy()` completely destroys the session. It not only deletes the session data but also invalidates the session ID, effectively logging the user out. This is a crucial step in securely terminating a session, ensuring that the session cannot be resumed or reused.

##### Security Considerations

- By using both `session_unset()` and `session_destroy()`, the script ensures that all session data is removed and the session is fully terminated. This is important for security, as it prevents

### `table.php`

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

#### Detailed Explanation of the PHP Script

##### Session Initialization

- `session_start()` starts or resumes a session, which allows the script to access the session variables such as `$_SESSION["user_id"]`. This is crucial for identifying the logged-in user and retrieving the corresponding data from the database.

##### Database Connection

- The script uses `$servername`, `$username`, `$password`, and `$dbname` to define the credentials and details required to connect to the MySQL database.
- `new mysqli($servername, $username, $password, $dbname)` establishes a connection to the MySQL database using the provided credentials.
- If the connection fails, `$conn->connect_error` checks for any errors, and the script is terminated using `die()` with an appropriate error message.

##### User Identification

- The variable `$user_id` is initialized with the value from the session, `$_SESSION["user_id"]`, which uniquely identifies the logged-in user.

##### Search Parameters

- The script retrieves optional search parameters from the `$_GET` superglobal:
  - `$searchCategory`: The category of the expense to search for.
  - `$searchDate`: The specific date of the expense.
  - `$searchDescription`: A keyword or phrase to search for in the description of the expense.
  - `$searchAmount`: The exact amount of the expense.
- Default values for these parameters are empty strings, ensuring the script can handle cases where no specific search criteria are provided.

##### SQL Query Construction

- The base SQL query, stored in `$sql`, selects relevant fields (`id`, `date`, `category`, `expense_name`, `amount`) from the `expenses` table, filtering by `user_id`.
- Additional filtering criteria are appended to the SQL query using conditional checks:
  - If `$searchCategory` is provided, a `LIKE` clause is added to match categories containing the search term.
  - If `$searchDate` is specified, an exact match on the date is required.
  - If `$searchDescription` is provided, a `LIKE` clause is added to match descriptions containing the search term.
  - If `$searchAmount` is specified, an exact match on the amount is required.

##### Query Execution and Results Display

- The query is executed with `$conn->query($sql)`, and the result is stored in `$result`.
- If there are matching records (`$result->num_rows > 0`), the script iterates through each record using `fetch_assoc()` to retrieve the data and display it in a table row.
  - Each row includes the expense's date, amount, category, description, and action links for editing and deleting the expense.
  - The `onclick` event handlers in the action links trigger JavaScript functions (`openEditPopup()` and `deleteExpense()`) with relevant data passed as parameters.
- If no matching records are found, a message is displayed indicating that no search results were found.

##### Security Considerations

- The script currently builds the SQL query dynamically using user input. In a production environment, it's essential to use prepared statements to prevent SQL injection vulnerabilities.
- Validating and sanitizing user input is also crucial to ensure the script handles data safely and securely.

### `save_expense.php`

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

#### Detailed Explanation of the PHP Script

##### Function Definition: `saveExpense`

- This script defines a reusable function named `saveExpense` that is responsible for saving a new expense record into the database.

##### Function Parameters

- The function accepts the following parameters:
  - `$conn`: The database connection object, which is an instance of the `mysqli` class.
  - `$date`: The date of the expense as a string.
  - `$category`: The category under which the expense is classified.
  - `$expenseName`: A brief description or name of the expense.
  - `$amount`: The monetary value of the expense. This is expected to be a floating-point number.
  - `$userId`: The ID of the user to whom this expense belongs.

##### SQL Query Preparation

- The function uses a prepared statement to securely insert data into the `expenses` table:
  - `$stmt = $conn->prepare("INSERT INTO expenses (date, category, expense_name, amount, user_id) VALUES (?, ?, ?, ?, ?)");`
  - The placeholders `?` in the SQL query represent the values to be inserted into the respective columns (`date`, `category`, `expense_name`, `amount`, and `user_id`).
  - If the preparation of the statement fails, the script terminates and outputs an error message using `die("Error in SQL query: " . $conn->error);`.

##### Parameter Binding

- The prepared statement is parameterized to prevent SQL injection. The parameters are bound to the placeholders using the `bind_param` method:
  - `$stmt->bind_param("ssssd", $date, $category, $expenseName, $amount, $userId);`
  - The string `"ssssd"` specifies the data types of the parameters:
    - `s`: String (for `$date`, `$category`, and `$expenseName`)
    - `d`: Double (for `$amount`, which is treated as a floating-point number)
  - This method securely assigns the values to the placeholders in the prepared statement.

##### Query Execution

- After binding the parameters, the statement is executed using `$stmt->execute();`.
- Once the execution is complete, the statement is closed with `$stmt->close();` to free up resources.

##### Error Handling and Security

- The function uses basic error handling to terminate the script if the prepared statement fails to initialize.
- By using prepared statements with parameter binding, the function effectively mitigates the risk of SQL injection, making it a more secure method for interacting with the database.

### `edit_expense.php`

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

#### Detailed Explanation of the PHP Script

##### Function Definition: `editExpense`

- This script defines a function called `editExpense` that updates an existing expense record in the database for a specific user.

##### Function Parameters

- The function accepts the following parameters:
  - `$conn`: The database connection object, which is an instance of the `mysqli` class.
  - `$editedDate`: The new date of the expense as a string.
  - `$editedCategory`: The updated category of the expense.
  - `$editedExpenseName`: The new description or name of the expense.
  - `$editedAmount`: The updated amount of the expense (expected to be a floating-point number).
  - `$editedExpenseId`: The ID of the expense record that needs to be updated.

##### SQL Query Preparation

- The function prepares an SQL statement to update the expense record in the `expenses` table:
  - `$stmt = $conn->prepare("UPDATE expenses SET date = ?, category = ?, expense_name = ?, amount = ? WHERE id = ? AND user_id = ?");`
  - The placeholders `?` in the query represent the values to be updated in the respective columns (`date`, `category`, `expense_name`, `amount`) and the condition (`id` and `user_id`) to identify the specific record.
  - If the preparation of the statement fails, the script terminates and outputs an error message using `die("Error in SQL query: " . $conn->error);`.

##### Parameter Binding

- The prepared statement is parameterized to prevent SQL injection. The parameters are bound to the placeholders using the `bind_param` method:
  - `$stmt->bind_param("ssssdd", $editedDate, $editedCategory, $editedExpenseName, $editedAmount, $editedExpenseId, $userId);`
  - The string `"ssssdd"` specifies the data types of the parameters:
    - `s`: String (for `$editedDate`, `$editedCategory`, and `$editedExpenseName`)
    - `d`: Double (for `$editedAmount` and `$editedExpenseId`)
  - This method securely assigns the values to the placeholders in the prepared statement.

##### Execution and Finalization

- The function executes the SQL query using `$stmt->execute();` to update the expense record.
- After execution, the prepared statement is closed with `$stmt->close();` to free up resources.

##### Error Handling and Security

- The function includes basic error handling to stop execution and output an error message if the prepared statement fails to initialize.
- By using prepared statements with parameter binding, the function reduces the risk of SQL injection, ensuring secure interaction with the database.

### `delete_expense.php`

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

#### Detailed Explanation of the PHP Script

##### Function Definition: `deleteExpense`

- This script defines a function called `deleteExpense` that deletes an expense record from the database for a specific user.

##### Function Parameters

- The function accepts the following parameters:
  - `$conn`: The database connection object, which is an instance of the `mysqli` class.
  - `$expenseId`: The ID of the expense record that needs to be deleted.

##### SQL Query Preparation

- The function prepares an SQL statement to delete the expense record from the `expenses` table:
  - `$stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND user_id = ?");`
  - The placeholders `?` in the query represent the values for the `id` of the expense to be deleted and the `user_id` to ensure the record belongs to the logged-in user.
  - If the preparation of the statement fails, the script terminates and outputs an error message using `die("Error in SQL query: " . $conn->error);`.

##### Parameter Binding

- The prepared statement is parameterized to prevent SQL injection. The parameters are bound to the placeholders using the `bind_param` method:
  - `$stmt->bind_param("dd", $expenseId, $userId);`
  - The string `"dd"` specifies the data types of the parameters:
    - `d`: Double (for both `$expenseId` and `$userId`)
  - This method securely assigns the values to the placeholders in the prepared statement.

##### Execution and Finalization

- The function executes the SQL query using `$stmt->execute();` to delete the specified expense record.
- After execution, the prepared statement is closed with `$stmt->close();` to free up resources.

##### Error Handling and Security

- The function includes basic error handling to stop execution and output an error message if the prepared statement fails to initialize.
- By using prepared statements with parameter binding, the function reduces the risk of SQL injection, ensuring secure interaction with the database.

# Project Setup Guide

## `Setup Instructions`

### `Database Configuration:`

1. **Create a MySQL Database:**
   - Access your MySQL database server (e.g., using phpMyAdmin, MySQL Workbench, or command line).
   - Create a new database, for example, `ushosteu_wydatki`.

2. **Import Tables:**
   - Import the necessary SQL schema to create required tables such as `users` and `expenses`.
   - Ensure the tables include all necessary columns and data types as expected by your PHP scripts.

3. **Update Database Credentials:**
   - Edit the PHP files to update the database connection credentials:
     - Set `$servername` to your MySQL server address (e.g., `localhost`).
     - Set `$username` to your MySQL username.
     - Set `$password` to your MySQL password.
     - Set `$dbname` to the name of your database (e.g., `ushosteu_wydatki`).
   - Ensure the PHP scripts have access to these credentials for connecting to the database.

### `Deploy the Application:`

1. **Prepare the Web Server:**
   - Place all PHP files into your web server's root directory (e.g., `/var/www/html` for Apache on Linux).
   - Ensure that the server's configuration allows for PHP execution.

2. **Verify PHP Installation:**
   - Make sure your web server supports PHP (e.g., Apache with PHP module or Nginx with PHP-FPM).
   - You can check PHP functionality by creating a `phpinfo.php` file with the following content:
     ```php
     <?php
     phpinfo();
     ?>
     ```
   - Access `http://your-server-address/phpinfo.php` to verify PHP information.

3. **Set File Permissions:**
   - Ensure that the PHP files have the correct permissions to be read and executed by the web server.
   - Typically, setting permissions to `755` for directories and `644` for files is appropriate.

### `Testing:`

1. **Register a New User:**
   - Open `registration.html` in your web browser.
   - Fill out the registration form and submit to create a new user account.

2. **Log In:**
   - Open `login.html` and use the newly created credentials to log in.
   - Verify that the login process works and redirects you appropriately.

3. **Manage Expenses:**
   - Access `main.php` to add, edit, or delete expenses.
   - Generate and view expense reports to ensure functionality.

4. **Profile Management:**
   - Test updating user profile information through the relevant PHP script.

### `Additional Configuration:`

1. **Session Management:**
   - Ensure session settings are correctly configured in `php.ini` for session handling.
   - Verify session storage and security settings.

2. **Error Handling:**
   - Implement and test error handling strategies for production, such as logging errors to a file and displaying user-friendly messages.

3. **Security Considerations:**
   - Use HTTPS to secure data transmitted between the client and server.
   - Regularly update PHP and your web server to mitigate security vulnerabilities.

### `License`

This project is licensed under the MIT License - see the LICENSE file for details.

### `Contributing`

If you would like to contribute to this project:
1. Fork the repository.
2. Create a feature branch.
3. Make your changes.
4. Submit a pull request with a description of the changes.

### `Support`

For support or questions:
- Open an issue on the project's GitHub repository.
- Contact the project maintainer via email (if available).

Feel free to adjust or add additional sections as necessary!
