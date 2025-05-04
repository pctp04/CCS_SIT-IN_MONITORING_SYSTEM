<?php
include(__DIR__ . '/../../database.php');

header('Content-Type: application/json');

$lab = $_GET['lab'] ?? '';

if (!$lab || !$conn) {
    echo json_encode([]);
    exit;
}

$query = "SELECT COMPUTER_NUMBER FROM computer_status WHERE LABORATORY = ? AND STATUS = 'Available' ORDER BY COMPUTER_NUMBER";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $lab);
$stmt->execute();
$result = $stmt->get_result();

$pcs = [];
while ($row = $result->fetch_assoc()) {
    $pcs[] = $row['COMPUTER_NUMBER'];
}

$stmt->close();
echo json_encode($pcs); 