<?php
$conn = new mysqli("localhost", "root", "", "latsar_db");
$id    = $_POST['id'];
$start = $_POST['start'];
$end   = $_POST['end'];
$stmt = $conn->prepare("UPDATE schedules SET start=?, end=? WHERE id=?");
$stmt->bind_param("ssi", $start, $end, $id);
$stmt->execute();
