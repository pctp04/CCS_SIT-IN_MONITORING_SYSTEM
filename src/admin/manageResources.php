<?php
session_start();
include(__DIR__ . '/../../database.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$message = '';
$error = '';

// Handle resource update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $resource_id = $_POST['resource_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    
    $query = "UPDATE resources SET TITLE = ?, DESCRIPTION = ? WHERE RESOURCE_ID = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "ssi", $title, $description, $resource_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Resource updated successfully!";
        } else {
            $error = "Error updating resource: " . mysqli_stmt_error($stmt);
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Error preparing statement: " . mysqli_error($conn);
    }
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $uploaded_by = $_SESSION['user_id'];
    
    // Check if file was uploaded without errors
    if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['resource_file'];
        $file_name = $file['name'];
        $file_tmp = $file['tmp_name'];
        $file_type = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Allowed file types
        $allowed_types = array('pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx');
        
        if (in_array($file_type, $allowed_types)) {
            // Create uploads directory if it doesn't exist
            $upload_dir = __DIR__ . '/../../uploads/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Generate unique filename
            $new_file_name = uniqid() . '_' . $file_name;
            $file_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $file_path)) {
                // Insert into database
                $query = "INSERT INTO resources (TITLE, DESCRIPTION, FILE_NAME, FILE_PATH, FILE_TYPE, UPLOADED_BY) 
                         VALUES (?, ?, ?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $query)) {
                    mysqli_stmt_bind_param($stmt, "sssssi", $title, $description, $file_name, $new_file_name, $file_type, $uploaded_by);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $message = "Resource uploaded successfully!";
                    } else {
                        $error = "Error saving to database: " . mysqli_stmt_error($stmt);
                        // Delete the uploaded file if database insert fails
                        unlink($file_path);
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    $error = "Error preparing statement: " . mysqli_error($conn);
                    unlink($file_path);
                }
            } else {
                $error = "Error uploading file.";
            }
        } else {
            $error = "Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, PPT, PPTX";
        }
    } else {
        $error = "Please select a file to upload.";
    }
}

// Handle resource deletion
if (isset($_GET['delete'])) {
    $resource_id = $_GET['delete'];
    
    // Get file path before deletion
    $query = "SELECT FILE_PATH FROM resources WHERE RESOURCE_ID = ?";
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $resource_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($row = mysqli_fetch_assoc($result)) {
            $file_path = __DIR__ . '/../../uploads/' . $row['FILE_PATH'];
            
            // Delete from database
            $query = "DELETE FROM resources WHERE RESOURCE_ID = ?";
            if ($delete_stmt = mysqli_prepare($conn, $query)) {
                mysqli_stmt_bind_param($delete_stmt, "i", $resource_id);
                
                if (mysqli_stmt_execute($delete_stmt)) {
                    // Delete the file
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                    $message = "Resource deleted successfully!";
                } else {
                    $error = "Error deleting resource.";
                }
                mysqli_stmt_close($delete_stmt);
            }
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all resources
$query = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
          FROM resources r 
          JOIN user u ON r.UPLOADED_BY = u.IDNO 
          ORDER BY r.UPLOAD_DATE DESC";
$resources = mysqli_query($conn, $query);
if (!$resources) {
    $error = "Error fetching resources: " . mysqli_error($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <link rel="stylesheet" href="../static/css/style.css">
    <title>Manage Resources</title>
    <style>
        .resource-form {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
        }
        .resource-list {
            margin-top: 30px;
        }
        .file-icon {
            font-size: 24px;
            margin-right: 10px;
        }
        .resource-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .resource-info {
            flex-grow: 1;
        }
        .resource-actions {
            display: flex;
            gap: 10px;
        }
        .edit-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.4);
        }
        .edit-modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . '/adminHeader.php'); ?>
    
    <div class="content-wrapper">
        <h2 class="w3-center">Manage Resources</h2>
        
        <?php if ($message): ?>
            <div class="w3-panel w3-green w3-display-container">
                <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                <p><?php echo $message; ?></p>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="w3-panel w3-red w3-display-container">
                <span onclick="this.parentElement.style.display='none'" class="w3-button w3-large w3-display-topright">&times;</span>
                <p><?php echo $error; ?></p>
            </div>
        <?php endif; ?>
        
        <!-- Upload Form -->
        <div class="resource-form w3-card">
            <h3>Upload New Resource</h3>
            <form method="post" enctype="multipart/form-data">
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <label>Title:</label>
                        <input type="text" name="title" class="w3-input w3-border" required>
                    </div>
                </div>
                
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <label>Description:</label>
                        <textarea name="description" class="w3-input w3-border" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <label>File:</label>
                        <input type="file" name="resource_file" class="w3-input w3-border" required>
                        <small>Allowed file types: PDF, DOC, DOCX, TXT, PPT, PPTX</small>
                    </div>
                </div>
                
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <button type="submit" name="upload" class="w3-button w3-blue">Upload Resource</button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Resources List -->
        <div class="resource-list">
            <h3>Uploaded Resources</h3>
            <?php if ($resources && mysqli_num_rows($resources) > 0): ?>
                <?php while ($resource = mysqli_fetch_assoc($resources)): ?>
                    <div class="resource-item w3-card">
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
                            <small>
                                Uploaded by: <?php echo htmlspecialchars($resource['FIRSTNAME'] . ' ' . $resource['LASTNAME']); ?> | 
                                Date: <?php echo date('M d, Y', strtotime($resource['UPLOAD_DATE'])); ?>
                            </small>
                        </div>
                        <div class="resource-actions">
                            <button onclick="openEditModal(<?php echo $resource['RESOURCE_ID']; ?>, '<?php echo htmlspecialchars($resource['TITLE']); ?>', '<?php echo htmlspecialchars($resource['DESCRIPTION']); ?>')" 
                                    class="w3-button w3-orange">Edit</button>
                            <a href="../../uploads/<?php echo $resource['FILE_PATH']; ?>" 
                               class="w3-button w3-blue" 
                               target="_blank">View</a>
                            <a href="?delete=<?php echo $resource['RESOURCE_ID']; ?>" 
                               class="w3-button w3-red"
                               onclick="return confirm('Are you sure you want to delete this resource?')">Delete</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="w3-center">No resources uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content w3-card">
            <span onclick="closeEditModal()" class="w3-button w3-display-topright">&times;</span>
            <h3>Edit Resource</h3>
            <form method="post">
                <input type="hidden" id="edit_resource_id" name="resource_id">
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <label>Title:</label>
                        <input type="text" id="edit_title" name="title" class="w3-input w3-border" required>
                    </div>
                </div>
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <label>Description:</label>
                        <textarea id="edit_description" name="description" class="w3-input w3-border" rows="3"></textarea>
                    </div>
                </div>
                <div class="w3-row-padding">
                    <div class="w3-col s12">
                        <button type="submit" name="update" class="w3-button w3-blue">Update Resource</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, title, description) {
            document.getElementById('editModal').style.display = 'block';
            document.getElementById('edit_resource_id').value = id;
            document.getElementById('edit_title').value = title;
            document.getElementById('edit_description').value = description;
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
        }
    </script>
</body>
</html> 