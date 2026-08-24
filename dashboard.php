<?php
session_start();
include 'db.php';

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username = $_SESSION['username'];
$user_dept = isset($_SESSION['department']) ? $_SESSION['department'] : 'General Admin'; 

// 1. COUNT REAL ACTIVE DOCUMENTS (Symmetric count based on role and department)
if ($user_role == 'Admin' || $user_role == 'Manager') {
    $active_query = "SELECT COUNT(*) AS total FROM documents WHERE is_deleted = 0";
} else {
    $active_query = "SELECT COUNT(*) AS total FROM documents WHERE is_deleted = 0 AND (sent_to = '$user_dept' OR sent_to = 'Staff' OR sent_to = 'All')";
}
$active_result = mysqli_query($conn, $active_query);
$active_count = mysqli_fetch_assoc($active_result)['total'];

// 2. COUNT REAL SOFT DELETED DOCUMENTS (In Trash Bin)
$trash_query = "SELECT COUNT(*) AS total FROM documents WHERE is_deleted = 1";
$trash_result = mysqli_query($conn, $trash_query);
$trash_count = mysqli_fetch_assoc($trash_result)['total'];

// 3. COUNT REAL REGISTERED USERS
$users_query = "SELECT COUNT(*) AS total FROM users";
$users_result = mysqli_query($conn, $users_query);
$users_count = mysqli_fetch_assoc($users_result)['total'];

// 4. FETCH ONLY RECEIVED DOCUMENTS (Inbox - Strictly filtered by role/department)
if ($user_role == 'Admin' || $user_role == 'Manager') {
    // Admins and Managers receive files sent to them, or to their departments, or to 'All'
    $inbox_query = "SELECT * FROM documents WHERE is_deleted = 0 AND (sent_to = '$user_role' OR sent_to = '$user_dept' OR sent_to = 'All') ORDER BY id DESC";
} else {
    // Staff only receive files sent to their specific department, or general 'Staff', or to 'All'
    $inbox_query = "SELECT * FROM documents WHERE is_deleted = 0 AND (sent_to = '$user_dept' OR sent_to = 'Staff' OR sent_to = 'All') ORDER BY id DESC";
}
$inbox_result = mysqli_query($conn, $inbox_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { 
            background-color: #f1f3f7; 
            font-size: 1.15rem; 
        }
        .navbar-custom { 
            background-color: #1a365d; 
        }
        .navbar-brand {
            font-size: 1.45rem; 
        }
        .nav-link {
            font-size: 1.15rem; 
        }
        .card-custom { 
            border-left: 5px solid #1a365d; 
            transition: transform 0.2s; 
        }
        .card-custom:hover { 
            transform: translateY(-3px); 
        }
        .card-trash { border-left: 5px solid #ffc107; }
        .card-users { border-left: 5px solid #198754; }
        .badge { font-size: 0.95rem; }
        .btn-sm { font-size: 0.95rem; }
    </style>
</head>
<body>
    <!-- New Clean Universal Navigation Bar (Without search.php) -->
<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm py-3">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">
            <i class="bi bi-folder-fill me-2"></i>Office File Management
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                </li>
                
                <!-- DROPDOWN 1: File Management (Cleaned up!) -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="docDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-file-earmark-text-fill me-1"></i> File Management
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="docDropdown">
                        <li><a class="dropdown-item py-2 fw-semibold" href="upload.php"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i> Upload File</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="files.php"><i class="bi bi-file-earmark-text-fill text-primary me-2"></i> List of Files</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="transfer.php"><i class="bi bi-send-fill text-info me-2"></i> Transfer File</a></li>
                    </ul>
                </li>
                
                <!-- DROPDOWN 2: Administration -->
                <?php if ($_SESSION['role'] == 'Admin' || $_SESSION['role'] == 'Manager'): ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-gear-fill me-1"></i> Administration
                    </a>
                    <ul class="dropdown-menu shadow-sm" aria-labelledby="adminDropdown">
                        <li><a class="dropdown-item py-2 fw-semibold" href="trash.php"><i class="bi bi-trash-fill text-warning me-2"></i> Trash Bin</a></li>
                        
                        <!-- Only Manager can see Track Transfers -->
                        <?php if ($_SESSION['role'] == 'Manager'): ?>
                        <li><a class="dropdown-item py-2 fw-semibold" href="track_transfers.php"><i class="bi bi-send-check-fill text-success me-2"></i> Track Transfers</a></li>
                        <?php endif; ?>
                        
                        <!-- Only Admin can see Activity Log, User Registration, and Manage Departments -->
                        <?php if ($_SESSION['role'] == 'Admin'): ?>
                        <li><a class="dropdown-item py-2 fw-semibold" href="logs.php"><i class="bi bi-journal-text text-primary me-2"></i> Activity Log</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="register.php"><i class="bi bi-person-plus-fill text-primary me-2"></i> Register User</a></li>
                        <li><a class="dropdown-item py-2 fw-semibold" href="add_department.php"><i class="bi bi-building-plus text-success me-2"></i> Manage Departments</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
            <span class="navbar-text text-white me-3 fw-semibold">
                <i class="bi bi-person-circle me-1"></i> Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm fw-bold">Logout</a>
        </div>
    </div>
</nav>
<!-- Main Content Area -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-md-12 mb-4">
            <h1 class="fw-bold text-dark display-6">System Overview Dashboard</h1>
            <p class="text-secondary fs-5">Quick overview of the office documents and user activities.</p>
        </div>
    </div>

    <!-- Clickable Statistics Cards -->
    <div class="row">
        <!-- Card 1: Active Documents (Clickable: Links to files.php) -->
        <div class="col-md-4 mb-4">
            <a href="files.php" class="text-decoration-none">
                <div class="card card-custom shadow-sm h-100 py-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1 fw-bold">Active Documents</div>
                            <div class="h2 mb-0 font-weight-bold text-primary fw-bold"><?php echo $active_count; ?> Files</div>
                        </div>
                        <div><i class="bi bi-file-earmark-text-fill text-primary opacity-50" style="font-size: 3rem;"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Card 2: Soft Deleted Documents (Clickable: Links to trash.php for Admin/Manager) -->
        <div class="col-md-4 mb-4">
            <?php if ($user_role == 'Admin' || $user_role == 'Manager'): ?>
            <a href="trash.php" class="text-decoration-none">
            <?php endif; ?>
                <div class="card card-custom card-trash shadow-sm h-100 py-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1 fw-bold">In Trash Bin</div>
                            <div class="h2 mb-0 font-weight-bold text-warning fw-bold"><?php echo $trash_count; ?> Files</div>
                        </div>
                        <div><i class="bi bi-trash-fill text-warning opacity-50" style="font-size: 3rem;"></i></div>
                    </div>
                </div>
            <?php if ($user_role == 'Admin' || $user_role == 'Manager'): ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Card 3: System Users (Clickable: Links to register.php for Admin) -->
        <div class="col-md-4 mb-4">
            <?php if ($user_role == 'Admin'): ?>
            <a href="register.php" class="text-decoration-none">
            <?php endif; ?>
                <div class="card card-custom card-users shadow-sm h-100 py-3">
                    <div class="card-body d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1 fw-bold">System Users</div>
                            <div class="h2 mb-0 font-weight-bold text-success fw-bold"><?php echo $users_count; ?> Users</div>
                        </div>
                        <div><i class="bi bi-people-fill text-success opacity-50" style="font-size: 3rem;"></i></div>
                    </div>
                </div>
            <?php if ($user_role == 'Admin'): ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Inbox - Received Documents (Collapsible) -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-3">
                <!-- Card Header with Collapse Toggle Button -->
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center fs-5">
                    <span class="mb-0"><i class="bi bi-inbox-fill text-primary me-2"></i> Inbox - Received Documents</span>
                    <button class="btn btn-sm btn-outline-secondary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#inboxTableCollapse" aria-expanded="true" aria-controls="inboxTableCollapse">
                        <i class="bi bi-arrows-expand me-1"></i> Toggle Inbox
                    </button>
                </div>
                <!-- Collapsible Container Wrapper -->
                <div class="collapse show" id="inboxTableCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Title </th>
                                        <th>Sender / Receiver</th>
                                        <th>Category</th>
                                        <th>Received At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($inbox_result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($inbox_result)): ?>
                                            <tr>
                                                <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['ref_number']); ?></td>
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['sender_receiver']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['department']); ?></span></td>
                                                <td class="text-muted"><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
                                                <td>
                                                    <!-- View / Download button -->
                                                    <a href="uploads/<?php echo $row['file_name']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                                        <i class="bi bi-eye-fill me-1"></i> Preview
                                                    </a>
                                                    <a href="uploads/<?php echo $row['file_name']; ?>" download class="btn btn-sm btn-outline-info fw-bold ms-1">
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                    <a href="transfer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success fw-bold ms-1">
                                                        <i class="bi bi-send-fill me-1"></i> Transfer
                                                    </a>
                                                    <?php if ($user_role == 'Admin' || $user_role == 'Manager'): ?>
                                                        <a href="delete_process.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to move this file to Trash Bin?')" class="btn btn-sm btn-outline-danger fw-bold ms-1">
                                                            <i class="bi bi-trash-fill me-1"></i> Delete
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-secondary py-4">No received documents in your inbox.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom fs-5">Quick Actions</div>
                <div class="card-body d-flex gap-3">
                    <a href="upload.php" class="btn btn-primary py-2 px-4 fw-bold" style="background-color: #1a365d; border: none; font-size: 1.1rem;">
                        <i class="bi bi-file-earmark-plus-fill me-1"></i> Upload New Letter
                    </a>
                    <a href="search.php" class="btn btn-secondary py-2 px-4 fw-bold" style="font-size: 1.1rem;">
                        <i class="bi bi-search me-1"></i> Search Archives
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>