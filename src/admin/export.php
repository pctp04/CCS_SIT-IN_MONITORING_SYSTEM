<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get filter parameters
$date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
$filter_lab = isset($_GET['filter_lab']) ? $_GET['filter_lab'] : '';
$filter_purpose = isset($_GET['filter_purpose']) ? $_GET['filter_purpose'] : '';
$export_type = isset($_GET['type']) ? $_GET['type'] : '';

// Prepare the query with filters
$query = "SELECT s.ID, s.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
          s.PURPOSE, s.LABORATORY, 
          TIME_FORMAT(s.LOGIN_TIME, '%h:%i%p') as LOGIN,
          TIME_FORMAT(s.LOGOUT_TIME, '%h:%i%p') as LOGOUT,
          DATE_FORMAT(s.SESSION_DATE, '%Y-%m-%d') as DATE
          FROM `sit-in` s
          JOIN user u ON s.STUDENT_ID = u.IDNO
          WHERE DATE(s.SESSION_DATE) = ?";

$params = array($date_filter);
$types = "s";

if ($filter_lab) {
    $query .= " AND s.LABORATORY = ?";
    $params[] = $filter_lab;
    $types .= "s";
}
if ($filter_purpose) {
    $query .= " AND s.PURPOSE = ?";
    $params[] = $filter_purpose;
    $types .= "s";
}

$query .= " ORDER BY s.ID DESC";

if ($conn) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($export_type === 'csv') {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="sit-in-report-' . $date_filter . '.csv"');
        
        // Create output stream
        $output = fopen('php://output', 'w');
        
        // Add headers
        fputcsv($output, array('University of Cebu-Main Campus'));
        fputcsv($output, array('College of Computer Studies'));
        fputcsv($output, array('Computer Laboratory Sit-in Monitoring System Report'));
        fputcsv($output, array('Date: ' . $date_filter));
        if ($filter_lab) fputcsv($output, array('Laboratory: ' . $filter_lab));
        if ($filter_purpose) fputcsv($output, array('Purpose: ' . $filter_purpose));
        fputcsv($output, array('')); // Empty line
        
        // Add column headers
        fputcsv($output, array('ID Number', 'Name', 'Purpose', 'Laboratory', 'Login Time', 'Logout Time', 'Date'));
        
        // Add data rows
        while ($row = $result->fetch_assoc()) {
            fputcsv($output, array(
                $row['STUDENT_ID'],
                $row['NAME'],
                $row['PURPOSE'],
                $row['LABORATORY'],
                $row['LOGIN'] ?? 'N/A',
                $row['LOGOUT'] ?? 'N/A',
                $row['DATE']
            ));
        }
        
        fclose($output);
        exit();
        
    } elseif ($export_type === 'pdf') {
        require_once __DIR__ . '/../../vendor/tecnickcom/tcpdf/tcpdf.php';
        
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Computer Laboratory');
        $pdf->SetTitle('Sit-in Report');
        
        // Remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        
        // Add a page
        $pdf->AddPage();
        
        // Set font
        $pdf->SetFont('helvetica', 'B', 14);
        
        // Add headers
        $pdf->Cell(0, 10, 'University of Cebu-Main Campus', 0, 1, 'C');
        $pdf->Cell(0, 10, 'College of Computer Studies', 0, 1, 'C');
        $pdf->Cell(0, 10, 'Computer Laboratory Sit-in Monitoring System Report', 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, 'Date: ' . $date_filter, 0, 1, 'C');
        if ($filter_lab) $pdf->Cell(0, 10, 'Laboratory: ' . $filter_lab, 0, 1, 'C');
        if ($filter_purpose) $pdf->Cell(0, 10, 'Purpose: ' . $filter_purpose, 0, 1, 'C');
        $pdf->Ln(10);
        
        // Column headers
        $pdf->SetFont('helvetica', 'B', 10);
        $header = array('ID Number', 'Name', 'Purpose', 'Laboratory', 'Login Time', 'Logout Time', 'Date');
        $w = array(25, 50, 35, 20, 25, 25, 25);
        
        for($i = 0; $i < count($header); $i++) {
            $pdf->Cell($w[$i], 7, $header[$i], 1, 0, 'C');
        }
        $pdf->Ln();
        
        // Data rows
        $pdf->SetFont('helvetica', '', 10);
        while ($row = $result->fetch_assoc()) {
            $pdf->Cell($w[0], 6, $row['STUDENT_ID'], 1);
            $pdf->Cell($w[1], 6, $row['NAME'], 1);
            $pdf->Cell($w[2], 6, $row['PURPOSE'], 1);
            $pdf->Cell($w[3], 6, $row['LABORATORY'], 1);
            $pdf->Cell($w[4], 6, $row['LOGIN'] ?? 'N/A', 1);
            $pdf->Cell($w[5], 6, $row['LOGOUT'] ?? 'N/A', 1);
            $pdf->Cell($w[6], 6, $row['DATE'], 1);
            $pdf->Ln();
        }
        
        // Output PDF
        $pdf->Output('sit-in-report-' . $date_filter . '.pdf', 'D');
        exit();
    }
    
    $stmt->close();
}

// If no valid export type or error occurred, redirect back to reports page
header("Location: reports.php");
exit();
?>