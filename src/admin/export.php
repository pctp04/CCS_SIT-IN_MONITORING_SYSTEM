<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get the date filter and export type
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$export_type = isset($_GET['type']) ? $_GET['type'] : '';

// Get sort parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';

// Validate sort parameters
$allowed_sort_fields = ['id', 'student_id', 'name', 'purpose', 'laboratory', 'login', 'logout', 'date'];
$sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'id';
$sort_order = in_array(strtoupper($sort_order), ['ASC', 'DESC']) ? strtoupper($sort_order) : 'DESC';

// Fetch sit-in records with user information
if($conn) {
    $query = "SELECT s.ID, s.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
              s.PURPOSE, s.LABORATORY, 
              TIME_FORMAT(s.LOGIN_TIME, '%h:%i%p') as LOGIN,
              TIME_FORMAT(s.LOGOUT_TIME, '%h:%i%p') as LOGOUT,
              DATE_FORMAT(s.SESSION_DATE, '%Y-%m-%d') as DATE
              FROM `sit-in` s
              JOIN user u ON s.STUDENT_ID = u.IDNO
              WHERE DATE(s.SESSION_DATE) = ?
              ORDER BY ";

    // Add sorting based on selected field
    switch($sort_by) {
        case 'id':
            $query .= "s.ID";
            break;
        case 'student_id':
            $query .= "s.STUDENT_ID";
            break;
        case 'name':
            $query .= "u.LASTNAME, u.FIRSTNAME";
            break;
        case 'purpose':
            $query .= "s.PURPOSE";
            break;
        case 'laboratory':
            $query .= "s.LABORATORY";
            break;
        case 'login':
            $query .= "s.LOGIN_TIME";
            break;
        case 'logout':
            $query .= "s.LOGOUT_TIME";
            break;
        case 'date':
            $query .= "s.SESSION_DATE";
            break;
    }

    $query .= " $sort_order";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $date_filter);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle CSV Export
if ($export_type === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=sit-in_report_' . $date_filter . '.csv');
    
    $output = fopen('php://output', 'w');
    
    // Add headers
    fputcsv($output, array('University of Cebu-Main Campus'));
    fputcsv($output, array('College of Computer Studies'));
    fputcsv($output, array('Computer Laboratory Sit-in Monitoring System Report'));
    fputcsv($output, array('')); // Empty line for spacing
    fputcsv($output, array('Date:', $date_filter));
    fputcsv($output, array('')); // Empty line for spacing
    fputcsv($output, array('ID', 'Student ID', 'Name', 'Purpose', 'Laboratory', 'Login Time', 'Logout Time', 'Date'));
    
    // Add data
    foreach ($records as $row) {
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit();
}

// Handle PDF Export
if ($export_type === 'pdf') {
    require_once('../../vendor/autoload.php');
    
    // Include TCPDF
    require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');
    
    // Create new PDF document
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('CCS Admin');
    $pdf->SetAuthor('CCS Admin');
    $pdf->SetTitle('Sit-in Report - ' . $date_filter);
    
    // Set default header data
    $pdf->SetHeaderData('', 0, 'Sit-in Report', 'Date: ' . $date_filter);
    
    // Set header and footer fonts
    $pdf->setHeaderFont(Array('helvetica', '', 10));
    $pdf->setFooterFont(Array('helvetica', '', 8));
    
    // Set default monospaced font
    $pdf->SetDefaultMonospacedFont('courier');
    
    // Set margins
    $pdf->SetMargins(15, 15, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);
    
    // Set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, 15);
    
    // Add a page
    $pdf->AddPage();
    
    // Set font
    $pdf->SetFont('helvetica', 'B', 12);
    
    // Add report headers
    $pdf->Cell(0, 10, 'University of Cebu-Main Campus', 0, 1, 'C');
    $pdf->Cell(0, 10, 'College of Computer Studies', 0, 1, 'C');
    $pdf->Cell(0, 10, 'Computer Laboratory Sit-in Monitoring System Report', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Date: ' . $date_filter, 0, 1, 'L');
    $pdf->Ln(5);
    
    // Set font for table
    $pdf->SetFont('helvetica', '', 10);
    
    // Create table header
    $header = array('ID', 'Student ID', 'Name', 'Purpose', 'Laboratory', 'Login', 'Logout', 'Date');
    
    // Create table data
    $data = array();
    foreach ($records as $row) {
        $data[] = array(
            $row['ID'],
            $row['STUDENT_ID'],
            $row['NAME'],
            $row['PURPOSE'],
            $row['LABORATORY'],
            $row['LOGIN'],
            $row['LOGOUT'],
            $row['DATE']
        );
    }
    
    // Print table
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');
    
    // Header
    $w = array(10, 20, 40, 30, 20, 20, 20, 20);
    for($i = 0; $i < count($header); $i++) {
        $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C', true);
    }
    $pdf->Ln();
    
    // Data
    $pdf->SetFont('');
    foreach($data as $row) {
        for($i = 0; $i < count($row); $i++) {
            $pdf->Cell($w[$i], 6, $row[$i], 'LR', 0, 'L');
        }
        $pdf->Ln();
    }
    
    // Closing line
    $pdf->Cell(array_sum($w), 0, '', 'T');
    
    // Output PDF
    $pdf->Output('sit-in_report_' . $date_filter . '.pdf', 'D');
    exit();
}
?> 