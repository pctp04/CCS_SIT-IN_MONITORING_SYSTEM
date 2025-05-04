<?php
session_start();
require_once '../config/database.php';
require_once 'adminHeader.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit();
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['resource_file'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['resource_file'];
    
    // Create uploads directory if it doesn't exist
    $upload_dir = '../uploads/resources/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    // Generate unique filename
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $file_path = $upload_dir . $new_filename;
    
    // Allowed file types
    $allowed_types = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx'];
    
    if (in_array(strtolower($file_extension), $allowed_types)) {
        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            $stmt = $conn->prepare("INSERT INTO resources (TITLE, DESCRIPTION, FILE_NAME, FILE_PATH, FILE_TYPE, UPLOADED_BY) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssi", $title, $description, $file['name'], $file_path, $file_extension, $_SESSION['user_id']);
            
            if ($stmt->execute()) {
                $success_message = "Resource uploaded successfully!";
            } else {
                $error_message = "Error uploading resource to database.";
            }
        } else {
            $error_message = "Error uploading file.";
        }
    } else {
        $error_message = "Invalid file type. Allowed types: PDF, DOC, DOCX, TXT, PPT, PPTX";
    }
}

// Handle resource deletion
if (isset($_POST['delete_resource'])) {
    $resource_id = $_POST['resource_id'];
    
    // Get file path before deleting
    $stmt = $conn->prepare("SELECT FILE_PATH FROM resources WHERE RESOURCE_ID = ?");
    $stmt->bind_param("i", $resource_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $resource = $result->fetch_assoc();
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM resources WHERE RESOURCE_ID = ?");
    $stmt->bind_param("i", $resource_id);
    
    if ($stmt->execute()) {
        // Delete file if exists
        if (file_exists($resource['FILE_PATH'])) {
            unlink($resource['FILE_PATH']);
        }
        $success_message = "Resource deleted successfully!";
    } else {
        $error_message = "Error deleting resource.";
    }
}

// Fetch all resources
$resources_query = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
                   FROM resources r 
                   JOIN user u ON r.UPLOADED_BY = u.IDNO 
                   ORDER BY r.UPLOAD_DATE DESC";
$resources_result = $conn->query($resources_query);
?>

<div class="container mt-4">
    <h2>Manage Resources</h2>
    
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h4>Upload New Resource</h4>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="resource_file" class="form-label">File</label>
                    <input type="file" class="form-control" id="resource_file" name="resource_file" required>
                    <small class="text-muted">Allowed file types: PDF, DOC, DOCX, TXT, PPT, PPTX</small>
                </div>
                <button type="submit" class="btn btn-primary">Upload Resource</button>
            </form>
        </div>
    </div>
    
    <!-- Resources List -->
    <div class="card">
        <div class="card-header">
            <h4>Uploaded Resources</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Description</th>
                            <th>File Name</th>
                            <th>Type</th>
                            <th>Uploaded By</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($resource = $resources_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($resource['TITLE']); ?></td>
                                <td><?php echo htmlspecialchars($resource['DESCRIPTION']); ?></td>
                                <td><?php echo htmlspecialchars($resource['FILE_NAME']); ?></td>
                                <td><?php echo strtoupper($resource['FILE_TYPE']); ?></td>
                                <td><?php echo htmlspecialchars($resource['FIRSTNAME'] . ' ' . $resource['LASTNAME']); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($resource['UPLOAD_DATE'])); ?></td>
                                <td>
                                    <a href="<?php echo $resource['FILE_PATH']; ?>" class="btn btn-sm btn-primary" target="_blank">View</a>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="resource_id" value="<?php echo $resource['RESOURCE_ID']; ?>">
                                        <button type="submit" name="delete_resource" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this resource?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 