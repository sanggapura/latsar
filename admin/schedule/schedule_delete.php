<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM schedules WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
