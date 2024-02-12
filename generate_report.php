<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    echo '<div class="unauthorized-message">Nieautoryzowany dostęp, przenoszę na stronę główną...</div>';
    header("Refresh: 3; URL=index.html");
    exit();
}

$servername = "localhost";
$username = "ushosteu_wydatki";
$password = "Szkola123!";
$dbname = "ushosteu_wydatki";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pobierz wszystkie istniejące kategorie
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

        // Zmodyfikowane zapytanie do bazy danych z warunkiem kategorii
        $categoryCondition = "";
        if (!empty($selectedCategories)) {
            $categoryCondition = "AND category IN ('" . implode("','", $selectedCategories) . "')";
        }

        $query = "SELECT * FROM expenses 
                  WHERE user_id = {$_SESSION["user_id"]} 
                  AND date BETWEEN '$startDate' AND '$endDate' 
                  $categoryCondition";
        $result = $conn->query($query);

        // Wyświetlanie wyników zapytania
        echo '<table>';
        echo '<tr><th>Data</th><th>Kwota</th><th>Kategoria</th><th>Opis</th></tr>';
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
        echo '<div class="error-message">Brak wymaganych danych do generowania raportu.</div>';
    }
}

$conn->close();
?>
