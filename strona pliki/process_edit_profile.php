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
    $user_id = $_SESSION["user_id"];
    $imie = $_POST["imie"];
    $nazwisko = $_POST["nazwisko"];
    $email = $_POST["email"];
    $login = $_POST["login"];
    $haslo = $_POST["haslo"];
    $haslo_powtorz = $_POST["haslo_powtorz"];

    if ($haslo != $haslo_powtorz) {
        echo '<script>alert("Passwords do not match. Please try again."); window.location.href = "profile.php";</script>';
        exit();
    }

    $hashed_password = password_hash($haslo, PASSWORD_DEFAULT);

    $update_sql = "UPDATE uzytkownik SET Imie='$imie', Nazwisko='$nazwisko', Email='$email', Login='$login', Haslo='$hashed_password' WHERE id='$user_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo '<script>alert("Profile updated successfully."); window.location.href = "profile.php";</script>';
        exit();
    } else {
        echo "Error updating profile: " . $conn->error;
    }
}

$conn->close();
?>