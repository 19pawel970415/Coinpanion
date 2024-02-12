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
    $login = $_POST["login"];
    $haslo = $_POST["haslo"];

    $sql = "SELECT id, login, haslo FROM uzytkownik WHERE login = '$login'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($haslo, $row["haslo"])) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["user_login"] = $row["login"];
            header("Location: main.php");
            exit();
        } else {
            echo '<script>alert("Nieprawidłowe hasło. Spróbuj jeszcze raz."); window.location.href = "login.html";</script>';
            exit();
        }
    } else {
        echo '<script>alert("Użytkownik o takiej nazwie nie isnieje. Spróbuj jeszcze raz."); window.location.href = "login.html";</script>';
        exit();
    }
}

function function_alert($message) { 
    echo "<script>alert('$message');</script>"; 
} 

$conn->close();
?>