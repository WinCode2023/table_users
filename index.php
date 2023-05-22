<!DOCTYPE html>
<html>
<head>
    <title>Список взломанных аккаунтов</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600&display=swap" rel="stylesheet">
    <style>
    body {
      font-family: Montserrat, sans-serif;
    }

    .loading-container {
      display: none;
      text-align: center;
      margin-top: 20px;
    }

    .loading-animation {
      display: inline-block;
      width: 50px;
      height: 50px;
      border: 3px solid #ccc;
      border-radius: 50%;
      border-top-color: #333;
      animation: spin 1s infinite ease-in-out;
    }

    @keyframes spin {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }
    </style>
</head>
<body>
    <div class="loading-container">
        <div class="loading-animation"></div>
        <p>Загрузка данных...</p>
    </div>

    <div id="mainContainer">
        <div class="container">
            <h2>Список взломанных аккаунтов</h2>

            <form method="GET" action="index.php" class="form-inline" id="searchForm">
                <div class="form-group">
                    <label for="search">Поиск:</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo $searchTerm; ?>">
                </div>
                <button type="submit" class="btn btn-primary">Найти</button>
            </form>

            <?php
            // Параметры подключения к базе данных
            $host = "localhost";
            $username = "root";
            $password = "pass";
            $dbname = "base";

            // Подключение к базе данных
            $conn = new mysqli($host, $username, $password, $dbname);

            // Проверка соединения
            if ($conn->connect_error) {
                die("Ошибка подключения к базе данных: " . $conn->connect_error);
            }

            $conn->set_charset("utf8");

            // Поиск
            $searchTerm = "";
            if (isset($_GET['search'])) {
                $searchTerm = $_GET['search'];
            }

            // Пагинация
            $resultsPerPage = 20;
            $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
            $offset = ($currentPage - 1) * $resultsPerPage;

            // Получение общего количества строк
            $sqlCount = "SELECT COUNT(*) AS total FROM users WHERE email LIKE '%$searchTerm%' OR password LIKE '%$searchTerm%'";
            $resultCount = $conn->query($sqlCount);
            $rowCount = $resultCount->fetch_assoc()['total'];

            // Получение строк для текущей страницы
            $sql = "SELECT * FROM users WHERE email LIKE '%$searchTerm%' OR password LIKE '%$searchTerm%' LIMIT $offset, $resultsPerPage";
            $result = $conn->query($sql);

            // Вывод списка взломанных аккаунтов
            if ($result->num_rows > 0) {
                echo "<div class='table-responsive'>";
                echo "<table class='table table-bordered'>";
                echo "<thead><tr><th>Email</th><th>Password</th></tr></thead>";
                echo "<tbody>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr><td>" . $row['email'] . "</td><td>" . $row['password'] . "</td></tr>";
                }
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
            } else {
                echo "<p>Нет результатов для отображения.</p>";
            }

            // Пагинация - вывод страниц
            $totalPages = ceil($rowCount / $resultsPerPage);
            $maxPagesToShow = 10;
            $startPage = max(1, $currentPage - floor($maxPagesToShow / 2));
            $endPage = min($totalPages, $startPage + $maxPagesToShow - 1);
            $startPage = max(1, $endPage - $maxPagesToShow + 1);

            if ($totalPages > 1) {
                echo "<ul class='pagination'>";
                if ($currentPage > 1) {
                    echo "<li><a href='index.php?page=1&search=$searchTerm'>&laquo;</a></li>";
                }
                for ($i = $startPage; $i <= $endPage; $i++) {
                    echo "<li" . ($i == $currentPage ? " class='active'" : "") . "><a href='index.php?page=$i&search=$searchTerm'>$i</a></li>";
                }
                if ($currentPage < $totalPages) {
                    echo "<li><a href='index.php?page=$totalPages&search=$searchTerm'>&raquo;</a></li>";
                }
                echo "</ul>";
            }

            // Закрытие соединения с базой данных
            $conn->close();
            ?>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        $('#searchForm').submit(function() {
            showLoading();
        });
    });

    function showLoading() {
        $('.loading-container').show();
        $('#mainContainer').hide();
    }
    </script>
</body>
</html>
