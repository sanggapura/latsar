<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
$title = $_POST['title'];
$start = $_POST['start'];
$end   = $_POST['end'];
$stmt = $conn->prepare("INSERT INTO schedules (title, start, end) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $title, $start, $end);
$stmt->execute();
