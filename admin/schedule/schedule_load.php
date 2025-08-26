<?php
$conn = new mysqli("localhost", "root", "", "jejaring_db");
$res = $conn->query("SELECT * FROM schedules");
$events = [];
while($row = $res->fetch_assoc()){
    $events[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'start' => $row['start'],
        'end'   => $row['end']
    ];
}
echo json_encode($events);
