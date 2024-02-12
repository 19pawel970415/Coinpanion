<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Użytkownika</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
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

        $select_sql = "SELECT Imie, Nazwisko, Email, Login FROM uzytkownik WHERE id = '$user_id'";
        $result = $conn->query($select_sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $imie = $row["Imie"];
            $nazwisko = $row["Nazwisko"];
            $email = $row["Email"];
            $login = $row["Login"];
        } else {
            echo "Error fetching user data: User not found";
            exit();
        }
    ?>
    <a href="main.php"><img src="images/backtomenu.png" alt="Powrót do menu"></a>
    <form class="form-profile" method="post" action="process_edit_profile.php">

        <label class="form-login-label">IMIĘ</label><br>
        <input type="text" class="profile-name login-input" name="imie" value="<?php echo $imie; ?>" required><br>

        <label class="form-login-label">NAZWISKO</label><br>
        <input type="text" class="profile-surname login-input" name="nazwisko" value="<?php echo $nazwisko; ?>" required><br>

        <label class="form-login-label">EMAIL</label><br>
        <input type="email" class="profile-email login-input" name="email" value="<?php echo $email; ?>" required><br>

        <label class="form-login-label">LOGIN</label><br>
        <input type="login" class="profile-login login-input" name="login" value="<?php echo $login; ?>" required><br>

        <label class="form-login-label">HASŁO</label><br>
        <input type="password" class="profile-password login-input" name="haslo" required><br>

        <label class="form-login-label">POWTÓRZ HASŁO</label><br>
        <input type="password" class="profile-password2 login-input" name="haslo_powtorz" required><br>

        <input type="submit" class="button-primary button-profile" value="EDYTUJ">
    </form>
    <footer>Coinpanion©</footer>
</body>
</html>