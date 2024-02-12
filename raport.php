<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raporty</title>
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

        $servername = "localhost";
        $username = "ushosteu_wydatki";
        $password = "Szkola123!";
        $dbname = "ushosteu_wydatki";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Pobierz unikalne kategorie z bazy danych
        $categoriesQuery = "SELECT DISTINCT category FROM expenses WHERE user_id = {$_SESSION["user_id"]}";
        $categoriesResult = $conn->query($categoriesQuery);

        $categories = array();
        while ($row = $categoriesResult->fetch_assoc()) {
            $categories[] = $row['category'];
        }
    ?>
    <main>
        <div class="div-raport1">
            <a href="main.php"><img src="images/backtomenu.png" alt="Powrót do menu" style="padding: 20px;"></a>
            <form class="form-raport" method="get" action="generate_report.php">
                <div class="input-group">
                    <label for="start-date">Data początkowa</label>
                    <input id="start-date" type="date" class="login-input raport-date-start" name="start-date" required>
                    <label for="end-date">Data końcowa</label>
                    <input id="end-date" type="date" class="login-input raport-date-end" name="end-date" required>
                </div>
                <label for="category">Kategorie:</label>
                <select id="categorySelect" name="category[]" multiple>
                    <?php
                        foreach ($categories as $category) {
                            echo "<option value=\"$category\">$category</option>";
                        }
                    ?>
                </select>
                <button type="button" onclick="addSelectedCategories()">Dodaj do raportu</button>
                <div id="selectedCategoriesContainer"></div>
                <input type="submit" value="Generuj raport">
            </form>
        </div>
    </main>
    <footer>Coinpanion©</footer>

    <script>
        function addSelectedCategories() {
            var select = document.getElementById("categorySelect");
            var selectedCategoriesContainer = document.getElementById("selectedCategoriesContainer");

            // Pobierz wybrane kategorie
            var selectedCategories = [];
            for (var i = 0; i < select.options.length; i++) {
                if (select.options[i].selected) {
                    selectedCategories.push(select.options[i].text);
                }
            }

            // Wyświetl wybrane kategorie na stronie
            selectedCategoriesContainer.innerHTML = "Wybrane kategorie: " + selectedCategories.join(", ");
        }
    </script>
</body>
</html>
