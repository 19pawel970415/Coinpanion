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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["start-date"]) && isset($_GET["end-date"]) && isset($_GET["category"])) {
        $startDate = $_GET["start-date"];
        $endDate = $_GET["end-date"];
        $category = $_GET["category"];
        
        // Użyj przesłanych danych do generowania raportu
        // Możesz wykonać zapytanie do bazy danych i wyświetlić wyniki na stronie

        // Poniżej znajduje się przykładowe zapytanie do bazy danych
        $query = "SELECT * FROM expenses 
                  WHERE user_id = {$_SESSION["user_id"]} 
                  AND date BETWEEN '$startDate' AND '$endDate' 
                  AND category = '$category'";
        $result = $conn->query($query);

        // Tutaj możesz wyświetlić wyniki zapytania, np. w tabeli
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