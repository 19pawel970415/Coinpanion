A project of Coinpanion, an app for personal budgeting being a part of the University of Lodz "Software Engineering" course.

# Coinpanion - personal budgeting system

Authors: Paweł Żurawski, Dawid Kruszyński, Dawid Kudaj, Karol Kraska, Wiktoria Jęch

## User Guide

![416023103_389005050310286_5319686253198355794_n](https://github.com/user-attachments/assets/d53205fc-2b9b-4587-b357-1e81a63584b3)

### 1. Registration:
To start using Coinpanion, the first step is to create an account.
On the homepage (index.html), click the "SIGN UP" button.
You will be redirected to the registration page (registration.html), where you will enter your basic information such as first name, last name, email, username, and password.
After filling out the form, click "REGISTER".
If the form is filled out correctly and you are successfully registered in our database, you will be redirected to the welcome page (welcome.html). You can now return to the homepage using the "HOME" button and proceed to log in.

### 2. Login:
Once you have an account with Coinpanion, you can log in.
On the homepage (index.html), click the "LOGIN" button.
You will be redirected to the login page (login.html), where you will enter your username and password.
After entering your details, click the "LOGIN" button.
If the username and password are correct, you will be redirected to the Menu (menu.php).

### 3. Menu (main.php):
In the menu, you will find tiles that allow you to navigate through the Coinpanion app.
- **Expenses:** Page where you can find all your expenses and add new ones.
- **Profile:** Page where you can edit your account information.
- **Reports:** Page that allows you to generate reports based on your expenses.
- **Logout:** Button that will log you out.
On each page, you will find a return button – a black arrow icon that will take you back to the Menu.

### 4. Expenses (table.php):
On this page, you can add new expenses, edit and delete existing ones, and search for transactions by: category, date, description, amount.

#### 4.a Adding an Expense
To add a new transaction, click the "NEW EXPENSE" button. A small form will appear with fields for date, category, description, and amount. Fill out the fields and then click the "Add" button.
The expense has been added to your list.

#### 4.b Editing an Expense
If you want to make changes to a transaction, click the pen icon in the row of that expense. You can now update the information in the form. To confirm changes, click "Save Changes."

#### 4.c Deleting an Expense
To remove a transaction from your list, click the trash can icon in the row of the expense. A message will pop up asking if you are sure you want to delete it. Click "OK" if you want to delete the transaction; otherwise, click "Cancel."

#### 4.d Searching for an Expense
If you want to find a specific transaction, you can use our search function. Fill out one of the fields (category, date, description, amount) and click the "Search" button.

### 5. Profile (profile.php)
On this page, you can edit your account details. Fill out the fields and then click "EDIT" to confirm the changes. If you do not want to make any changes, use the return button.

### 6. Reports (report.php)
On this page, you have the option to generate reports based on your expenses. To do this, select the period for which the report should be generated. From the list of categories, check the ones you are interested in and click "Add to Report." If everything is correct, click "Generate Report." You will be redirected to the completed report.



## Technical Overview

This project is a simple expenses management system that includes user registration, login, profile management, and expenses tracking. Users can register, log in, update their profiles, generate expenses reports, and manage their expenses.

## Files Descriptions

## .php files

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

## .html files

### index.html

```html
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dołącz do Coinpanion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1 class="h1-welcome">Witaj w Coinpanion!</h1>
    <p class="p-bio">
        Coinpanion to innowacyjna aplikacja stworzona z myślą o tych, którzy pragną lepiej kontrolować swoje finanse. Niezależnie od tego, czy jesteś doświadczonym inwestorem, czy dopiero zaczynasz swoją podróż ku oszczędzaniu. Coinpanion dostarcza narzędzi, które sprawią, że zarządzanie budżetem stanie się łatwiejsze i bardziej efektywne.
    </p>
    <a href="login.html" class="button-primary go-to-login" style="  display: inline-block; width: 150px;margin: 0 10px; text-align: center;margin-bottom: 20px;">LOGIN</a>
    <a href="registration.html" class="button-secondary go-to-registration" style="  display: inline-block;width: 150px;margin: 0 10px; text-align: center;margin-bottom: 20px;">SIGN UP</a>
    <footer>Coinpanion©</footer>
    
</body>
</html>
```

#### Detailed Explanation of the PHP/HTML Script

##### PHP Session Initialization

- `<?php session_start(); ?>` initiates a new session or resumes an existing session. This is used to manage user sessions across multiple pages.

##### HTML Structure

- The document is an HTML5 document with a `<!DOCTYPE html>` declaration, specifying that the content is written in HTML5.

##### Document Metadata

- `<meta charset="UTF-8">` specifies the character encoding for the document, ensuring proper display of characters.
- `<meta name="viewport" content="width=device-width, initial-scale=1.0">` ensures the webpage is responsive and displays correctly on various devices by setting the viewport width to match the device's width.

##### Page Title

- `<title>Dołącz do Coinpanion</title>` sets the title of the page, which appears in the browser's title bar or tab.

##### Stylesheet Linking

- `<link rel="stylesheet" href="styles.css">` links to an external CSS file (`styles.css`) that contains styling rules for the page.

##### Body Content

- `<h1 class="h1-welcome">Witaj w Coinpanion!</h1>` displays a welcoming heading with a class `h1-welcome` for styling.
- `<p class="p-bio">` contains a paragraph introducing Coinpanion, describing its purpose and benefits, with a class `p-bio` for styling.

##### Navigation Links

- `<a href="login.html" class="button-primary go-to-login" style="display: inline-block; width: 150px; margin: 0 10px; text-align: center; margin-bottom: 20px;">LOGIN</a>` creates a styled login button that directs users to `login.html`.
- `<a href="registration.html" class="button-secondary go-to-registration" style="display: inline-block; width: 150px; margin: 0 10px; text-align: center; margin-bottom: 20px;">SIGN UP</a>` creates a styled registration button that directs users to `registration.html`.

##### Footer

- `<footer>Coinpanion©</footer>` adds a footer to the page, containing a copyright notice for Coinpanion.

##### Additional Notes

**Styling**

- The page uses inline styles and classes from an external stylesheet (`styles.css`) to control the appearance of various elements, including buttons and text.

**Responsive Design**

- The use of the viewport meta tag helps ensure the page is responsive and looks good on mobile devices as well as desktops.

**Session Management**

- The PHP `session_start()` function is included to enable session management, allowing user information to be preserved across different pages within the same session.

**Navigation**

- The page provides clear navigation options for users to log in or sign up, guiding them to the appropriate pages for these actions.

### registration.html

```html

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In Coinpanion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form class="form-registration" method="post" action="process_registration.php">
        <label class="form-login-label">IMIĘ</label><br>
        <input type="text" class="reg-name login-input" name="imie" required><br>
        <label class="form-login-label">NAZWISKO</label><br>
        <input type="text" class="reg-surname login-input" name="nazwisko" required><br>
        <label class="form-login-label">EMAIL</label><br>
        <input type="email" class="reg-email login-input" name="email" required><br>
        <label class="form-login-label">LOGIN</label><br>
        <input type="text" class="profile-email login-input" name="login" required><br>
        <label class="form-login-label">HASŁO</label><br>
        <input type="password" class="reg-password login-input" name="haslo" required><br>
        <label class="form-login-label">POWTÓRZ HASŁO</label><br>
        <input type="password" class="reg-password2 login-input" name="haslo_powtorz" required><br>
        <input type="submit" class="button-primary button-registration" value="ZAREJESTRUJ">
    </form>
    <footer>Coinpanion©</footer>
</body>
</html>
```

#### Detailed Explanation of the HTML Registration Form

##### Document Metadata

- `<!DOCTYPE html>` declares the document type and version of HTML, ensuring it is rendered in standards mode.
- `<html lang="en">` specifies the language of the document as English.

##### Metadata and Styles

- `<meta charset="UTF-8">` sets the character encoding to UTF-8, which supports a wide range of characters.
- `<meta name="viewport" content="width=device-width, initial-scale=1.0">` makes the page responsive by setting the viewport width to match the device width and scaling the page for optimal viewing.
- `<title>Sign In Coinpanion</title>` sets the title of the page, which appears in the browser’s title bar or tab.
- `<link rel="stylesheet" href="styles.css">` links to an external CSS file (`styles.css`) for styling the form and other elements.

##### Form Structure

- `<form class="form-registration" method="post" action="process_registration.php">` creates a form for user registration with the `POST` method, which sends form data securely to `process_registration.php`.

##### Form Inputs

- **Name Input**
  - `<label class="form-login-label">IMIĘ</label><br>` labels the input field for the user's first name.
  - `<input type="text" class="reg-name login-input" name="imie" required><br>` creates a text input field for the user's first name with a `required` attribute, meaning the field must be filled out.

- **Surname Input**
  - `<label class="form-login-label">NAZWISKO</label><br>` labels the input field for the user's surname.
  - `<input type="text" class="reg-surname login-input" name="nazwisko" required><br>` creates a text input field for the user's surname with a `required` attribute.

- **Email Input**
  - `<label class="form-login-label">EMAIL</label><br>` labels the input field for the user's email address.
  - `<input type="email" class="reg-email login-input" name="email" required><br>` creates an email input field, which validates the format of the email address, and is required.

- **Login Input**
  - `<label class="form-login-label">LOGIN</label><br>` labels the input field for the user's login username.
  - `<input type="text" class="profile-email login-input" name="login" required><br>` creates a text input field for the user's login username with a `required` attribute.

- **Password Input**
  - `<label class="form-login-label">HASŁO</label><br>` labels the input field for the user's password.
  - `<input type="password" class="reg-password login-input" name="haslo" required><br>` creates a password input field where the text is obscured and is required.

- **Repeat Password Input**
  - `<label class="form-login-label">POWTÓRZ HASŁO</label><br>` labels the input field for the user to confirm their password.
  - `<input type="password" class="reg-password2 login-input" name="haslo_powtorz" required><br>` creates a second password input field for confirming the password, also with a `required` attribute.

- **Submit Button**
  - `<input type="submit" class="button-primary button-registration" value="ZAREJESTRUJ">` creates a submit button labeled "ZAREJESTRUJ" (Register). When clicked, it submits the form data to `process_registration.php`.

##### Footer

- `<footer>Coinpanion©</footer>` adds a footer with a copyright notice for Coinpanion.

##### Additional Notes

**Styling**

- The page relies on an external stylesheet (`styles.css`) to style the form elements and layout.

**Form Handling**

- The form data is sent via `POST` to `process_registration.php`, which is expected to handle user registration by processing the input data.

**Validation**

- The `required` attribute is used on all form fields to ensure that users cannot submit the form with missing information.

**Responsive Design**

- The inclusion of the viewport meta tag ensures that the form is displayed correctly on various devices, adapting to different screen sizes.

**Security Considerations**

- Ensure that `process_registration.php` properly handles and sanitizes the form data to prevent security issues such as SQL injection and XSS.

### welcome.html

```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dziękujemy za dołączenie do nas.</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h3 class="h3-successful">Rejestracja przebiegła pomyślnie!</h3>
    <p class="p-successful">
        Dziękujemy za dołączenie do społeczności Coinpanion - Twojego nowego partnera w zarządzaniu finansami! Jesteśmy podekscytowani, że jesteś z nami.<br>
        Teraz możesz śledzić swoje wydatki, planować cele oszczędnościowe i zyskać pełną kontrolę nad swoim budżetem. Zacznij już teraz, dodając swoje konta i ciesz się funkcjonalnościami, które ułatwią Ci życie finansowe.
        Jeśli masz jakiekolwiek pytania lub potrzebujesz pomocy, nie wahaj się skontaktować z nami. Życzymy udanej podróży ku finansowej niezależności!<br>
        Pozdrawiamy, <BR>Zespół Coinpanion
    </p>
    <a href="index.html" class="button-primary go-to-main">STRONA GŁÓWNA</a>
    <footer>Coinpanion©</footer>
</body>
</html>
```

#### Detailed Explanation of the HTML Success Page

##### Document Metadata

- `<!DOCTYPE html>` declares the document type and version of HTML, ensuring the page is rendered in standards mode.
- `<html lang="en">` specifies that the document is in English.

##### Metadata and Styles

- `<meta charset="UTF-8">` sets the character encoding to UTF-8, supporting a wide range of characters and ensuring proper text display.
- `<meta name="viewport" content="width=device-width, initial-scale=1.0">` ensures the page is responsive, adjusting its layout to the device's width and scaling it for optimal viewing.
- `<title>Dziękujemy za dołączenie do nas.</title>` sets the page title to "Thank You for Joining Us," which appears in the browser's title bar or tab.
- `<link rel="stylesheet" href="styles.css">` links to an external CSS file (`styles.css`) for styling the page elements.

##### Body Content

- `<h3 class="h3-successful">Rejestracja przebiegła pomyślnie!</h3>` displays a heading indicating a successful registration with a class `h3-successful` for styling.

- `<p class="p-successful">` contains a paragraph with a class `p-successful`, providing a detailed success message to the user:
  - **Greeting and Welcome Message**: Thanks the user for joining Coinpanion and expresses excitement about their participation.
  - **Usage Instructions**: Encourages users to start tracking their expenses, planning savings goals, and adding accounts to make the most of the financial management tools provided.
  - **Support Offer**: Invites users to contact support if they have questions or need help.
  - **Closing**: Ends with best wishes for financial independence and a sign-off from the Coinpanion team.

- `<a href="index.html" class="button-primary go-to-main">STRONA GŁÓWNA</a>` creates a button labeled "STRONA GŁÓWNA" (Homepage) that redirects users to the homepage (`index.html`). The button uses the class `button-primary` for styling.

##### Footer

- `<footer>Coinpanion©</footer>` includes a footer with a copyright notice for Coinpanion.

##### Additional Notes

**Styling**

- The page uses an external stylesheet (`styles.css`) to manage the look and feel of the success message and button.

**Responsiveness**

- The viewport meta tag ensures the page is displayed correctly on various devices, adapting to different screen sizes.

**User Experience**

- The page provides a clear and welcoming confirmation message following a successful registration, enhancing the user's experience and guiding them to the next steps.

**Navigation**

- The button provided offers an easy way for users to return to the homepage, ensuring smooth navigation after completing the registration process.

### login.html

```html
<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Coinpanion</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form class="form-login" method="post" action="process_login.php">
        <label class="form-login-label">LOGIN</label><br>
        <input type="text" class="login-user login-input" name="login" required><br>
        <label class="form-login-label">HASŁO</label><br>
        <input type="password" class="login-password login-input" name="haslo" required><br>
        <input type="submit" class="button-primary button-login" value="ZALOGUJ">
        <input type="hidden" name="action" value="login">
    </form>
    <footer>Coinpanion©</footer>
</body>
</html>
```

#### Detailed Explanation of the PHP/HTML Login Form

##### PHP Session Initialization

- `<?php session_start(); ?>` initiates a new session or resumes an existing session, which allows for session management across multiple pages.

##### Document Metadata

- `<!DOCTYPE html>` specifies that the document is an HTML5 document, ensuring it is rendered in standards mode.
- `<html lang="en">` declares the language of the document as English.

##### Metadata and Styles

- `<meta charset="UTF-8">` sets the character encoding to UTF-8, which supports a wide range of characters and ensures proper text display.
- `<meta name="viewport" content="width=device-width, initial-scale=1.0">` makes the page responsive by setting the viewport width to match the device width and scaling the content for optimal viewing.
- `<title>Login Coinpanion</title>` sets the title of the page, which appears in the browser’s title bar or tab.
- `<link rel="stylesheet" href="styles.css">` links to an external CSS file (`styles.css`) that provides styling for the page elements.

##### Body Content

- `<form class="form-login" method="post" action="process_login.php">` creates a form for user login, using the `POST` method to send data securely to `process_login.php`.

##### Form Inputs

- **Login Input**
  - `<label class="form-login-label">LOGIN</label><br>` labels the input field for the user’s login username.
  - `<input type="text" class="login-user login-input" name="login" required><br>` creates a text input field for the username, marked as `required` to ensure it must be filled out.

- **Password Input**
  - `<label class="form-login-label">HASŁO</label><br>` labels the input field for the user’s password.
  - `<input type="password" class="login-password login-input" name="haslo" required><br>` creates a password input field where the text is obscured and is required for form submission.

- **Submit Button**
  - `<input type="submit" class="button-primary button-login" value="ZALOGUJ">` creates a submit button labeled "ZALOGUJ" (Log In). When clicked, it submits the form data to `process_login.php`.

- **Hidden Input**
  - `<input type="hidden" name="action" value="login">` includes a hidden field with the name `action` and value `login`. This can be used to differentiate between different form submissions or actions in `process_login.php`.

##### Footer

- `<footer>Coinpanion©</footer>` adds a footer to the page with a copyright notice for Coinpanion.

##### Additional Notes

**Styling**

- The page utilizes an external stylesheet (`styles.css`) to apply styles to the form and its elements, ensuring a consistent appearance.

**Form Handling**

- The form data is submitted via `POST` to `process_login.php`, which is expected to handle the authentication process.

**Validation**

- The `required` attribute on the input fields ensures that the user cannot submit the form without providing both a username and password.

**Session Management**

- The PHP `session_start()` function is used to manage user sessions, allowing for tracking and maintaining user information across different pages.

**Security Considerations**

- Ensure that `process_login.php` securely handles login credentials and properly manages user authentication to protect against security vulnerabilities.

## .css files

### styles.css

```css
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* Body */
body {
  font-family: Arial, sans-serif;
  background-color: #f0f0f0;
  color: #333;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  min-height: 100vh;
  text-align: center;
  padding: 20px;
}

body#spending-page {
  font-family: Arial, sans-serif;
  background-color: #f5f5f5;
  color: #333;
  padding: 0;
  display: block;
  margin: 0 auto;
  width: 80%;
  text-align: left;
}



/* Header */
header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
  margin-top: 10px;
}

/* Main */
main {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

/* Nagłówki */
h1, h2, h3, h4, h5, h6 {
  color: #333;
  margin-bottom: 10px;
}

.h1-welcome {
  color: #007bff;
  margin-bottom: 20px;
}

.h3-successful {
  color: #007bff;
  margin-bottom: 20px;
}

/* Paragrafy */
p {
  line-height: 1.6;
  margin-bottom: 15px;
}

.p-bio {
  margin: 0 auto 20px;
  max-width: 600px;
  font-size: 18px;
  line-height: 1.6;
}

.p-successful {
  margin-bottom: 20px;
  font-size: 16px;
  line-height: 1.6;
}

/* Linki */
a {
  color: #007bff;
  text-decoration: none;
}

a:hover {
  text-decoration: underline;
}

/* Nagłówek witamy */
.h1-welcome {
  color: #007bff;
}

.button-primary,
.button-secondary {
  display: inline-block;
  width: 150px;
  margin: 0 10px;
  text-align: center;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}


.button-primary {
  background-color: #007bff;
  color: #fff;
}

.button-secondary {
  background-color: #28a745;
  color: #fff;
}

.button-primary:hover,
.button-secondary:hover {
  background-color: #0056b3;
}

/* Formularze */
.form-login, .form-registration, .form-profile {
  max-width: 300px;
  margin: 0 auto;
}

.login-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.form-login {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.form-profile {
  max-width: 400px;
  margin: 20px auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-login-label {
  font-weight: bold;
  display: block;
  margin-bottom: 5px;
  text-align: left;
}

.form-registration {
  background-color: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  text-align: center;
  width: 300px;
}

.login-input {
  width: 100%;
  padding: 8px;
  margin-bottom: 10px;
  border-radius: 5px;
  border: 1px solid #ccc;
}

.button-primary {
  display: inline-block;
  padding: 10px 20px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  background-color: #007bff;
  color: #fff;
}

.button-primary:hover {
  background-color: #0056b3;
}

/* Div Spending 1 */
.div-spending1 {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-bottom: 20px;
}

.filtr-input {
  margin-left: 20px;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 200px;
}

.button-spending {
  margin-right: 20px;
  padding: 10px 20px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.button-spending:hover {
  background-color: #0056b3;
}

/* Div Spending 2 */
.div-spending2 {
  width: 80%;
  margin-bottom: 20px;
}

.table-spending {
  width: 70%;
  border-collapse: collapse;
  margin-right: 20px;
}

.table-spending th,
.table-spending td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}

.table-spending th {
  background-color: #f0f0f0;
}

/* Aside */
aside {
  width: 25%;
}

/* Div Spending 3 */
.div-spending3 {
  width: 80%;
}

.table-categories {
  width: 100%;
  border-collapse: collapse;
}

.table-categories th,
.table-categories td {
  border: 1px solid #ccc;
  padding: 8px;
  text-align: center;
}

.table-categories th {
  background-color: #f0f0f0;
}

.div-raport1 {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 40px;
  border-bottom: 1px solid #ccc;
  background-color: #f9f9f9;
}

.form-raport {
  display: flex;
  align-items: center;
  gap: 20px; 
}

.raport-date-start,
.raport-date-end {
  padding: 12px;
  margin-right: 20px; 
  border: 1px solid #ccc;
  border-radius: 6px;
}

input[type="submit"] {
  padding: 12px 24px;
  border: none;
  border-radius: 6px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
}

input[type="date"],
.category-select,
input[type="submit"] {
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
}

select {
  padding: 12px;
  border: 1px solid #ccc;
  border-radius: 6px;
}

.button-generate {
  position: absolute;
  top: 30px;
  right: 30px;
  padding: 14px 28px;
  border: none;
  border-radius: 6px;
  background-color: #007bff;
  color: white;
  cursor: pointer;
}

/* Stopka */
footer {
  text-align: center;
  padding: 10px 0;
  background-color: #007bff;
  color: #fff;
  position: fixed;
  bottom: 0;
  width: 100%;
}

/* Menu główne */
.main-menu {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  gap: 20px;
  margin-bottom: 40px;
}
.menu-element {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-color: #fff;
  padding: 40px; 
  border-radius: 20px; 
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease; 
}
.menu-element img {
  width: 100px; 
  height: 100px;
}
.menu-element:hover {
  transform: scale(1.05);
}

.menu-element div {
  margin-bottom: 15px;
}

.menu-element span {
  font-size: 18px; 
  font-weight: bold;
}

/* Elementy wydatków */
.div-spending1, .div-spending2, .div-spending3 {
  margin-bottom: 20px;
  padding: 10px;
}

.table-spending {
  width: 100%;
}

.table-categories {
  width: 100%;
}

/* Elementy raportów */
.div-raport1 {
  padding: 10px;
}
```

#### Detailed Explanation of the CSS File

##### Global Styles

- `* { margin: 0; padding: 0; box-sizing: border-box; }` 
  - Resets margins and paddings to zero and sets `box-sizing` to `border-box` for all elements, ensuring consistent box model behavior across the site.

##### Body

- `body { font-family: Arial, sans-serif; background-color: #f0f0f0; color: #333; display: flex; flex-direction: column; justify-content: center; align-items: center; min-height: 100vh; text-align: center; padding: 20px; }`
  - Sets the global font to Arial, with a light grey background and dark text color.
  - Uses Flexbox to center content vertically and horizontally.
  - Ensures the body takes at least the full height of the viewport.

- `body#spending-page { font-family: Arial, sans-serif; background-color: #f5f5f5; color: #333; padding: 0; display: block; margin: 0 auto; width: 80%; text-align: left; }`
  - Specific styles for the `spending-page` ID.
  - Changes background to a slightly different grey, adjusts width, and aligns text to the left.

##### Header

- `header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; margin-top: 10px; }`
  - Uses Flexbox to arrange header items with space between them.
  - Adds vertical margin.

##### Main

- `main { display: flex; justify-content: space-between; align-items: flex-start; }`
  - Uses Flexbox to layout main content with space between items and aligns items to the start.

##### Headings

- `h1, h2, h3, h4, h5, h6 { color: #333; margin-bottom: 10px; }`
  - Sets the color of all headings to dark grey and adds margin below.

- `.h1-welcome { color: #007bff; margin-bottom: 20px; }`
  - Specific styling for welcome headings (`h1`), setting color to a blue shade and increasing bottom margin.

- `.h3-successful { color: #007bff; margin-bottom: 20px; }`
  - Specific styling for successful operation messages (`h3`), similar to welcome headings.

##### Paragraphs

- `p { line-height: 1.6; margin-bottom: 15px; }`
  - Sets line height for paragraphs for better readability and adds margin below.

- `.p-bio { margin: 0 auto 20px; max-width: 600px; font-size: 18px; line-height: 1.6; }`
  - Specific styling for bio paragraphs, including maximum width and font size.

- `.p-successful { margin-bottom: 20px; font-size: 16px; line-height: 1.6; }`
  - Specific styling for success messages, with adjusted font size.

##### Links

- `a { color: #007bff; text-decoration: none; }`
  - Sets link color to blue and removes underline.

- `a:hover { text-decoration: underline; }`
  - Underlines links on hover.

##### Buttons

- `.button-primary, .button-secondary { display: inline-block; width: 150px; margin: 0 10px; text-align: center; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; }`
  - Defines common button styles including dimensions, padding, and cursor type.

- `.button-primary { background-color: #007bff; color: #fff; }`
  - Primary button styling with blue background and white text.

- `.button-secondary { background-color: #28a745; color: #fff; }`
  - Secondary button styling with green background and white text.

- `.button-primary:hover, .button-secondary:hover { background-color: #0056b3; }`
  - Darkens button background on hover.

##### Forms

- `.form-login, .form-registration, .form-profile { max-width: 300px; margin: 0 auto; }`
  - Sets a maximum width for form containers and centers them.

- `.login-input { width: 100%; padding: 8px; margin-bottom: 10px; border-radius: 5px; border: 1px solid #ccc; }`
  - Styles form input fields with padding, border, and rounded corners.

- `.form-login { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; }`
  - Styles login forms with background color, padding, border radius, and shadow.

- `.form-profile { max-width: 400px; margin: 20px auto; padding: 20px; background-color: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); }`
  - Styles profile forms with larger maximum width and subtle shadow.

- `.form-login-label { font-weight: bold; display: block; margin-bottom: 5px; text-align: left; }`
  - Styles form labels with bold text and left alignment.

- `.form-registration { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); text-align: center; width: 300px; }`
  - Styles registration forms similarly to login forms but with a specific width.

##### Spending Sections

- `.div-spending1 { display: flex; flex-direction: column; align-items: center; margin-bottom: 20px; }`
  - Centers content and adds margin below for the spending section.

- `.filtr-input { margin-left: 20px; padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 200px; }`
  - Styles filter input fields with padding and border radius.

- `.button-spending { margin-right: 20px; padding: 10px 20px; background-color: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; transition: background-color 0.3s ease; }`
  - Styles spending buttons with blue background, white text, and hover effect.

- `.div-spending2 { width: 80%; margin-bottom: 20px; }`
  - Sets width for spending section 2 and adds margin below.

- `.table-spending { width: 70%; border-collapse: collapse; margin-right: 20px; }`
  - Styles tables in the spending section with borders and spacing.

- `.table-spending th, .table-spending td { border: 1px solid #ccc; padding: 8px; text-align: center; }`
  - Styles table cells with borders and padding.

- `.table-spending th { background-color: #f0f0f0; }`
  - Sets background color for table headers.

##### Aside

- `aside { width: 25%; }`
  - Sets width for aside elements.

##### Spending Section 3

- `.div-spending3 { width: 80%; }`
  - Sets width for the third spending section.

- `.table-categories { width: 100%; border-collapse: collapse; }`
  - Styles tables in the categories section with full width.

- `.table-categories th, .table-categories td { border: 1px solid #ccc; padding: 8px; text-align: center; }`
  - Styles table cells in the categories section similarly to spending tables.

- `.div-raport1 { display: flex; justify-content: space-between; align-items: center; padding: 40px; border-bottom: 1px solid #ccc; background-color: #f9f9f9; }`
  - Styles report section with Flexbox, padding, border, and background color.

- `.form-raport { display: flex; align-items: center; gap: 20px; }`
  - Styles report forms with Flexbox and spacing between elements.

- `.raport-date-start, .raport-date-end { padding: 12px; margin-right: 20px; border: 1px solid #ccc; border-radius: 6px; }`
  - Styles date input fields with padding, margin, border, and border radius.

- `input[type="submit"] { padding: 12px 24px; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer; }`
  - Styles submit buttons with padding, border radius, and blue background.

- `input[type="date"], .category-select, input[type="submit"] { padding: 10px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; }`
  - Styles date inputs and select elements with padding, border, and font size.

- `select { padding: 12px; border: 1px solid #ccc; border-radius: 6px; }`
  - Styles dropdown select elements similarly to other inputs.

- `.button-generate { position: absolute; top: 30px; right: 30px; padding: 14px 28px; border: none; border-radius: 6px; background-color: #007bff; color: white; cursor: pointer; }`
  - Styles a generate button with absolute positioning and blue background.

##### Footer

- `footer { text-align: center; padding: 10px 0; background-color: #007bff; color: #fff; position: fixed; bottom: 0; width: 100%; }`
  - Styles the footer with centered text, padding, blue background, and fixed positioning at the bottom.

##### Main Menu

- `.main-menu { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 40px; }`
  - Uses a grid layout for the main menu with two columns and a gap between items.

- `.menu-element { display: flex; flex-direction: column; justify-content: center; align-items: center; background-color: #fff; padding: 40px; border-radius: 20px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); transition: transform 0.3s ease; }`
  - Styles individual menu elements with Flexbox, background color, padding, rounded corners, shadow, and a transition effect.

- `.menu-element img { width: 100px; height: 100px; }`
  - Sets fixed dimensions for menu images.

- `.menu-element:hover { transform: scale(1.05); }`
  - Adds a scaling effect on hover for menu elements.

- `.menu-element div { margin-bottom: 15px; }`
  - Adds bottom margin to div elements within menu elements.

- `.menu-element span { font-size: 18px; font-weight: bold; }`
  - Styles text within menu elements with larger font size and bold weight.

##### Spending Elements

- `.div-spending1, .div-spending2, .div-spending3 { margin-bottom: 20px; padding: 10px; }`
  - Adds margin and padding to various spending sections.

- `.table-spending, .table-categories { width: 100%; }`
  - Ensures tables in spending and categories sections span the full width of their containers.

##### Report Elements

- `.div-raport1 { padding: 10px; }`
  - Adds padding to the report section.

This CSS file provides a comprehensive set of styles for a web application, covering layout, typography, form elements, buttons, and more. It ensures a consistent look and feel across different components and pages, improving user experience and accessibility.


# Project Setup Guide

## `Setup Instructions`

### `Database Configuration:`

![412402769_281393051585275_6459831302802139921_n](https://github.com/user-attachments/assets/0db6f988-3830-45c2-a1bc-3e2bc5b5b8bf)

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
- Contact the project maintainer via email (19pawel970415@gmail.com).

Feel free to adjust or add additional sections as necessary!
