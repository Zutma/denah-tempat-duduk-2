<?php
require __DIR__ . '/../includes/db.php';

$result = $conn->query("SELECT * FROM faculties");

while ($row = $result->fetch_assoc()) {
    echo $row['name'] . "<br>";
}