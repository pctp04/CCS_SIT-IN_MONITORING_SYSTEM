<?php
session_start();
require_once '../config/database.php';
require_once 'studentHeader.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch all resources
$resources_query = "SELECT r.*, u.FIRSTNAME, u.LASTNAME 
                   FROM resources r 
                   JOIN user u ON r.UPLOADED_BY = u.IDNO 
                   ORDER BY r.UPLOAD_DATE DESC";
$resources_result = $conn->query($resources_query);
?>

<div class="container mt-4">
    <h2>Learning Resources</h2>
    
    <div class="row">
        <?php while ($resource = $resources_result->fetch_assoc()): ?>
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($resource['TITLE']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($resource['DESCRIPTION']); ?></p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    Uploaded by: <?php echo htmlspecialchars($resource['FIRSTNAME'] . ' ' . $resource['LASTNAME']); ?><br>
                                    Date: <?php echo date('M d, Y', strtotime($resource['UPLOAD_DATE'])); ?>
                                </small>
                            </div>
                            <div>
                                <span class="badge bg-secondary me-2"><?php echo strtoupper($resource['FILE_TYPE']); ?></span>
                                <a href="<?php echo $resource['FILE_PATH']; ?>" class="btn btn-primary" target="_blank">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    
    <?php if ($resources_result->num_rows === 0): ?>
        <div class="alert alert-info">
            No resources available at the moment.
        </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 