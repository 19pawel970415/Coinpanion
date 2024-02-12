<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Strona główna</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
        session_start();
        if (isset($_SESSION["user_id"])) {
            echo '<div class="logged-in-info">Zalogowany jako: ' . $_SESSION["user_login"] . ' (ID użyt.: ' . $_SESSION["user_id"] . ')</div>';
        } 
        else {
            echo '<div class="unauthorized-message">Nieautoryzowany dostęp, przenoszę na stronę główną...</div>';
            header("Refresh: 3; URL=index.html");
            exit();
        }
    ?>

    <main class="main-menu">
        <a href="table.php" class="menu-element">
            <div><img src="images/wallet.png" alt="Wydatki"></div>
            <span>WYDATKI</span>
        </a>
        <a href="profile.php" class="menu-element">
            <div><img src="images/profileedit.png" alt="Profil"></div>
            <span>PROFIL</span>
        </a>
        <a href="raport.php" class="menu-element">
            <div><img src="images/raport.png" alt="Raporty"></div>
            <span>RAPORTY</span>
        </a>
        <form method="post" action="logout.php">
            <button type="submit" class="menu-element" style="background: none; border: none; cursor: pointer;">
                <div><img src="images/logout.png" alt="Wyloguj"></div>
                <span>WYLOGUJ</span>
            </button>
        </form>
    </main>
    <footer>Coinpanion©</footer>
</body>
</html> 