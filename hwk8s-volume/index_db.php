<?php
echo '<h1>DB</h1>';
echo '<hr>'; 

$dbconn = pg_connect("host=hwk8s-db-s port=5432 dbname=test user=postgres password=qwerty")
or die('Не удалось соединиться: ' . pg_last_error());

// Выполнение SQL-запроса
$query = 'SELECT * FROM k8s_table';
$result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

// Вывод результатов в HTML
echo "<table border='1'>";
while ($line = pg_fetch_array($result, null, PGSQL_ASSOC)) {
    echo "<tr>";

    foreach ($line as $col_value) {
        echo '<td>' . $col_value . '</td>';
    }

    echo "</tr>";
}
echo "</table>";

// Очистка результата
pg_free_result($result);

// Закрытие соединения
pg_close($dbconn);