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
    $imie = $_POST["imie"];
    $nazwisko = $_POST["nazwisko"];
    $email = $_POST["email"];
    $login = $_POST["login"];
    $haslo = $_POST["haslo"];
    $haslo_powtorz = $_POST["haslo_powtorz"];

    if ($haslo != $haslo_powtorz) {
        die("Passwords do not match");
    }

    $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);

    $sql = "INSERT INTO uzytkownik (Imie, Nazwisko, Email, Login, Haslo) VALUES ('$imie', '$nazwisko', '$email', '$login', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
        header("Location: welcome.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>