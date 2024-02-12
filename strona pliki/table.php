<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION["user_id"])) {
    echo '<div class="unauthorized-message">Nieautoryzowany dostęp, przenoszę na stronę główną...</div>';
    header("Refresh: 3; URL=index.html");
    exit();
} 

$user_id = $_SESSION["user_id"];

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function saveExpense($conn, $date, $category, $expenseName, $amount, $userId) {
    $stmt = $conn->prepare("INSERT INTO expenses (date, category, expense_name, amount, user_id) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error in SQL query: " . $conn->error);
    }
    $stmt->bind_param("ssssd", $date, $category, $expenseName, $amount, $userId);
    $stmt->execute();
    $stmt->close();
}

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

// Obsługa wyszukiwania
$searchCategory = isset($_POST["search_category"]) ? $_POST["search_category"] : "";
$searchDate = isset($_POST["search_date"]) ? $_POST["search_date"] : "";
$searchDescription = isset($_POST["search_description"]) ? $_POST["search_description"] : "";
$searchAmount = isset($_POST["search_amount"]) ? $_POST["search_amount"] : "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save_expense"])) {
        $date = $_POST["expense_date"];
        $category = $_POST["expense_category"];
        $expenseName = $_POST["expense_name"];
        $amount = $_POST["expense_amount"];

        saveExpense($conn, $date, $category, $expenseName, $amount, $user_id);
    } elseif (isset($_POST["save_edit_expense"])) {
        $editedExpenseId = $_POST["edit_expense_id"];
        $editedDate = $_POST["edit_expense_date"];
        $editedCategory = $_POST["edit_expense_category"];
        $editedExpenseName = $_POST["edit_expense_name"];
        $editedAmount = $_POST["edit_expense_amount"];

        editExpense($conn, $editedDate, $editedCategory, $editedExpenseName, $editedAmount, $editedExpenseId);
    } elseif (isset($_POST["delete_expense"])) {
        $expenseIdToDelete = $_POST["delete_expense"];
        deleteExpense($conn, $expenseIdToDelete);
    }
}

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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Twoje Wydatki</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            z-index: 999;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998;
        }
    </style>
</head>
<body id="spending-page">
    <header>
        <a href="main.php"><img src="images/backtomenu.png" alt="Powrót do menu"></a>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" class="filtr-input" name="search_category" placeholder="Wyszukaj wg kategorii" value="<?php echo $searchCategory; ?>">
            <input type="date" name="search_date" value="<?php echo $searchDate; ?>">
            <input type="text" name="search_description" placeholder="Wyszukaj wg opisu" value="<?php echo $searchDescription; ?>">
            <input type="number" name="search_amount" placeholder="Wyszukaj wg kwoty" value="<?php echo $searchAmount; ?>">
            <button type="submit" name="search_button">Szukaj</button>
        </form>
        <!-- Przycisk otwierający popup -->
        <button class="button-primary button-spending" onclick="openPopup()">NOWY WYDATEK</button>
    </header>
    <main id="main-content">
        <table class="table-spending">
            <thead>
                <tr>
                    <th style="width: 15%">Data</th>
                    <th style="width: 20%">Kwota</th>
                    <th style="width: 20%">Kategoria</th>
                    <th style="width: 30%">Opis</th>
                    <th style="width: 15%">Edytuj/Usuń</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["date"] . "</td>";
                        echo "<td>" . $row["amount"] . "</td>";
                        echo "<td>" . $row["category"] . "</td>";
                        echo "<td>" . $row["expense_name"] . "</td>";
                        echo "<td><a href='#' onclick='openEditPopup(" . $row["id"] . ", \"" . $row["date"] . "\", \"" . $row["category"] . "\", \"" . $row["expense_name"] . "\", " . $row["amount"] . ")'><img src='images/edit.png' alt='Edytuj'></a> ";
                        echo "<a href='#' onclick='deleteExpense(" . $row["id"] . ")'><img src='images/delete.png' alt='Usuń'></a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>Brak wyników wyszukiwania.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </main>
    <footer>Coinpanion©</footer>

    <!-- Popup do dodawania nowego wydatku -->
    <div id="expensePopup" class="popup">
        <h2>Nowy Wydatek</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <label for="expense_date">Data:</label>
            <input type="date" name="expense_date" required>

            <label for="expense_category">Kategoria:</label>
            <input type="text" name="expense_category" required>

            <label for="expense_name">Opis:</label>
            <input type="text" name="expense_name" required>

            <label for="expense_amount">Kwota:</label>
            <input type="number" name="expense_amount" required>

            <button type="submit" name="save_expense">Dodaj</button>
        </form>
    </div>

    <!-- Popup do edycji istniejącego wydatku -->
    <div id="editExpensePopup" class="popup">
        <h2>Edytuj Wydatek</h2>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="hidden" name="edit_expense_id" id="edit_expense_id">
            <label for="edit_expense_date">Data:</label>
            <input type="date" name="edit_expense_date" id="edit_expense_date" required>

            <label for="edit_expense_category">Kategoria:</label>
            <input type="text" name="edit_expense_category" id="edit_expense_category" required>

            <label for="edit_expense_name">Opis:</label>
            <input type="text" name="edit_expense_name" id="edit_expense_name" required>

            <label for="edit_expense_amount">Kwota:</label>
            <input type="number" name="edit_expense_amount" id="edit_expense_amount" required>

            <button type="submit" name="save_edit_expense">Zapisz zmiany</button>
        </form>
    </div>

    <!-- Overlay do zakrywania tła -->
    <div id="overlay" class="overlay" onclick="closePopup()"></div>

    <script>
        function openPopup() {
            document.getElementById("expensePopup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function openEditPopup(id, date, category, name, amount) {
            document.getElementById("edit_expense_id").value = id;
            document.getElementById("edit_expense_date").value = date;
            document.getElementById("edit_expense_category").value = category;
            document.getElementById("edit_expense_name").value = name;
            document.getElementById("edit_expense_amount").value = amount;

            document.getElementById("editExpensePopup").style.display = "block";
            document.getElementById("overlay").style.display = "block";
        }

        function deleteExpense(id) {
            if (confirm("Czy na pewno chcesz usunąć ten wydatek?")) {
                document.getElementById("delete_expense_id").value = id;
                document.getElementById("deleteExpenseForm").submit();
            }
        }

        function closePopup() {
            document.getElementById("expensePopup").style.display = "none";
            document.getElementById("editExpensePopup").style.display = "none";
            document.getElementById("overlay").style.display = "none";
        }
    </script>
    
    <!-- Formularz do usuwania wydatku -->
    <form id="deleteExpenseForm" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="delete_expense" id="delete_expense_id">
    </form>
</body>
</html>