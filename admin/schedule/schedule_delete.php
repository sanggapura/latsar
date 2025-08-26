<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
$id = $_GET['id'];
$stmt = $conn->prepare("DELETE FROM schedules WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
