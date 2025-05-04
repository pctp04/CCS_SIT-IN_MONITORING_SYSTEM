<?php
session_start();
include(__DIR__ . '/../../database.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

// Fetch all resources
$query = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
          FROM resources r 
          JOIN user u ON r.UPLOADED_BY = u.IDNO 
          ORDER BY r.UPLOAD_DATE DESC";
$resources = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <title>Resources</title>
    <style>
        .resources-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .resource-card {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .file-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .resource-info {
            margin-left: 10px;
        }
        .resource-meta {
            color: #666;
            font-size: 0.9em;
            margin-top: 5px;
        }
        .download-btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/studentHeader.php'); ?>
    
    <div class="content-wrapper">
        <div class="resources-container">
            <h2 class="w3-center">Available Resources</h2>
            
            <?php if ($resources && $resources->num_rows > 0): ?>
                <div class="w3-row-padding">
                    <?php while ($resource = $resources->fetch_assoc()): ?>
                        <div class="w3-col s12 m6 l4">
                            <div class="resource-card w3-card">
                                <div class="w3-container">
                                    <div class="file-icon">
                                        <?php
                                        $icon = '📄';
                                        switch ($resource['FILE_TYPE']) {
                                            case 'pdf':
                                                $icon = '📕';
                                                break;
                                            case 'doc':
                                            case 'docx':
                                                $icon = '📘';
                                                break;
                                            case 'ppt':
                                            case 'pptx':
                                                $icon = '📗';
                                                break;
                                            case 'txt':
                                                $icon = '📄';
                                                break;
                                        }
                                        echo $icon;
                                        ?>
                                    </div>
                                    <div class="resource-info">
                                        <h4><?php echo htmlspecialchars($resource['TITLE']); ?></h4>
                                        <p><?php echo htmlspecialchars($resource['DESCRIPTION']); ?></p>
                                        <div class="resource-meta">
                                            <p>Uploaded by: <?php echo htmlspecialchars($resource['FIRSTNAME'] . ' ' . $resource['LASTNAME']); ?></p>
                                            <p>Date: <?php echo date('M d, Y', strtotime($resource['UPLOAD_DATE'])); ?></p>
                                        </div>
                                        <a href="../../uploads/<?php echo $resource['FILE_PATH']; ?>" 
                                           class="w3-button w3-blue download-btn" 
                                           target="_blank">Download</a>
                                        <?php
                                            $file_url = '../../uploads/' . $resource['FILE_PATH'];
                                            $file_type = strtolower($resource['FILE_TYPE']);
                                            $view_url = $file_url;
                                            if (in_array($file_type, ['pdf', 'txt'])) {
                                                // Direct view
                                                $view_url = $file_url;
                                            } else if (in_array($file_type, ['doc', 'docx', 'ppt', 'pptx'])) {
                                                // Use Google Docs Viewer
                                                $absolute_url = (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/' . $file_url;
                                                $view_url = 'https://docs.google.com/gview?url=' . urlencode($absolute_url) . '&embedded=true';
                                            }
                                        ?>
                                        <a href="<?php echo $view_url; ?>" 
                                           class="w3-button w3-green download-btn" 
                                           target="_blank">View</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="w3-panel w3-pale-yellow w3-border">
                    <p class="w3-center">No resources available at the moment.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 