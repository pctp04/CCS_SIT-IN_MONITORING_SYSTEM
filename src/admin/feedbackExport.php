<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}
include(__DIR__ . '/../../database.php');

// Get sort parameters
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'student_id';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'DESC';
$export_type = isset($_GET['type']) ? $_GET['type'] : '';

// Validate sort parameters
$allowed_sort_fields = ['student_id', 'purpose', 'laboratory'];
$sort_by = in_array($sort_by, $allowed_sort_fields) ? $sort_by : 'student_id';
$sort_order = in_array(strtoupper($sort_order), ['ASC', 'DESC']) ? strtoupper($sort_order) : 'DESC';

// Fetch feedback records
if($conn) {
    $query = "SELECT f.FEEDBACK_ID, f.STUDENT_ID, CONCAT(u.LASTNAME, ', ', u.FIRSTNAME) as NAME,
              u.COURSE, u.YEAR, f.LABORATORY, f.FEEDBACK_MSG,
              DATE_FORMAT(f.SESSION_DATE, '%Y-%m-%d') as DATE
              FROM feedback f
              JOIN user u ON f.STUDENT_ID = u.IDNO
              ORDER BY ";

    // Add sorting based on selected field
    switch($sort_by) {
        case 'student_id':
            $query .= "f.STUDENT_ID";
            break;
        case 'purpose':
            $query .= "f.PURPOSE";
            break;
        case 'laboratory':
            $query .= "f.LABORATORY";
            break;
    }

    $query .= " $sort_order";
              
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

// Handle CSV Export
if ($export_type === 'csv') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=feedback_report.csv');
    
    $output = fopen('php://output', 'w');
    
    // Add headers
    fputcsv($output, array('University of Cebu-Main Campus'));
    fputcsv($output, array('College of Computer Studies'));
    fputcsv($output, array('Computer Laboratory Sit-in Monitoring System Feedback Report'));
    fputcsv($output, array('')); // Empty line for spacing
    fputcsv($output, array('Date Generated:', date('Y-m-d')));
    fputcsv($output, array('')); // Empty line for spacing
    fputcsv($output, array('Date', 'Student ID', 'Name', 'Course', 'Year', 'Laboratory', 'Feedback'));
    
    // Add data
    foreach ($records as $row) {
        fputcsv($output, array(
            $row['DATE'],
            $row['STUDENT_ID'],
            $row['NAME'],
            $row['COURSE'],
            $row['YEAR'],
            $row['LABORATORY'],
            $row['FEEDBACK_MSG']
        ));
    }
    
    fclose($output);
    exit();
}

// Handle PDF Export
if ($export_type === 'pdf') {
    require_once('../../vendor/autoload.php');
    require_once('../../vendor/tecnickcom/tcpdf/tcpdf.php');
    
    // Create new PDF document
    $pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
    
    // Set document information
    $pdf->SetCreator('CCS Admin');
    $pdf->SetAuthor('CCS Admin');
    $pdf->SetTitle('Feedback Report');
    
    // Set default header data
    $pdf->SetHeaderData('', 0, 'Feedback Report', 'Date Generated: ' . date('Y-m-d'));
    
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
    $pdf->Cell(0, 10, 'Computer Laboratory Sit-in Monitoring System Feedback Report', 0, 1, 'C');
    $pdf->Ln(5);
    $pdf->Cell(0, 10, 'Date Generated: ' . date('Y-m-d'), 0, 1, 'L');
    $pdf->Ln(5);
    
    // Set font for table
    $pdf->SetFont('helvetica', '', 10);
    
    // Create table header
    $header = array('Date', 'Student ID', 'Name', 'Course', 'Year', 'Laboratory', 'Feedback');
    
    // Create table data
    $data = array();
    foreach ($records as $row) {
        $data[] = array(
            $row['DATE'],
            $row['STUDENT_ID'],
            $row['NAME'],
            $row['COURSE'],
            $row['YEAR'],
            $row['LABORATORY'],
            $row['FEEDBACK_MSG']
        );
    }
    
    // Print table
    $pdf->SetFillColor(255, 255, 255);
    $pdf->SetTextColor(0);
    $pdf->SetDrawColor(0, 0, 0);
    $pdf->SetLineWidth(0.3);
    $pdf->SetFont('', 'B');
    
    // Header
    $w = array(20, 20, 40, 20, 10, 20, 50);
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
    $pdf->Output('feedback_report.pdf', 'D');
    exit();
}

// If no export type is specified, show the export page with the same styling as feedbackReports.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Export Feedback Reports</title>
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="w3-container">
            <div class="w3-card w3-white w3-margin">
                <header class="w3-container w3-blue">
                    <h2>Export Feedback Reports</h2>
                </header>

                <div class="w3-container w3-margin">
                    <div class="w3-row">
                        <div class="w3-col s6">
                            <a href="feedbackExport.php?type=csv&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>" 
                               class="w3-button w3-blue w3-margin-right">
                                <i class="fa fa-file-excel-o"></i> Export CSV
                            </a>
                        </div>
                        <div class="w3-col s6">
                            <a href="feedbackExport.php?type=pdf&sort_by=<?php echo $sort_by; ?>&sort_order=<?php echo $sort_order; ?>" 
                               class="w3-button w3-blue">
                                <i class="fa fa-file-pdf-o"></i> Export PDF
                            </a>
                        </div>
                    </div>
                </div>

                <div class="w3-container w3-margin">
                    <div class="w3-panel w3-pale-blue w3-leftbar w3-border-blue">
                        <p>Select the export format above to download the feedback report. The data will be sorted according to your current sorting preferences.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 