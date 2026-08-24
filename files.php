<?php
// Enable error reporting to show mistakes on screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$username_logged = $_SESSION['username'];
$user_dept = $_SESSION['department']; // Fetching user department from session

// Base query based on strict 3-Level Security Roles (RBAC Upgrade)
if ($user_role == 'Admin') {
    // 1. Admin can search and view absolutely everything in the database
    $query = "SELECT * FROM documents WHERE is_deleted = 0";
} elseif ($user_role == 'Manager') {
    // 2. Manager can search/view all active files EXCEPT Admin-only private files
    $query = "SELECT * FROM documents WHERE is_deleted = 0 AND sent_to != 'Admin'";
} else {
    // 3. Staff can ONLY search and view files belonging to their specific department/office!
    $query = "SELECT * FROM documents WHERE is_deleted = 0 AND department = '$user_dept'";
}

// Process Search if the button is clicked
if (isset($_GET['search_btn'])) {
    $keyword = mysqli_real_escape_string($conn, $_GET['keyword']);
    $department = mysqli_real_escape_string($conn, $_GET['department']);

    // Searches by: Ref Number, Title, Sender, or Year
    if (!empty($keyword)) {
        $query .= " AND (ref_number LIKE '%$keyword%' OR title LIKE '%$keyword%' OR sender_receiver LIKE '%$keyword%' OR document_date LIKE '%$keyword%')";
    }

    // Filter by department (Only for Admin and Manager, Staff is locked to their own department)
    if ($user_role == 'Admin' || $user_role == 'Manager') {
        if (!empty($department)) {
            $query .= " AND department = '$department'";
        }
    }
}

$query .= " ORDER BY id DESC";

// Execute query and check for errors
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Database Query Failed: " . mysqli_error($conn));
}

// Fetch all active departments dynamically from the database for the search dropdown
$dept_query = "SELECT * FROM departments ORDER BY name ASC";
$dept_result = mysqli_query($conn, $dept_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Office File Management System - Active Archives</title>
    <!-- Local Bootstrap CSS (Offline Compatible) -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background-color: #f1f3f7; font-size: 1.15rem; }
        .navbar-custom { background-color: #1a365d; }
        .navbar-brand { font-size: 1.45rem; }
        .nav-link { font-size: 1.15rem; }
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
<!-- Main Container -->
<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-11">
            
            <!-- NEW SECTION: Search & Filter Card (Upgraded: 5-4-3 grid spacing to fit on ONE line!) -->
            <div class="card shadow-sm border-0 rounded-3 mb-4">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom">
                    <i class="bi bi-search text-primary me-2"></i> Search & Filter Archives
                </div>
                <div class="card-body p-4">
                    <form action="files.php" method="GET">
                        <div class="row g-3">
                            <!-- Keyword Search (col-md-5) -->
                            <div class="col-md-5">
                                <label for="keyword" class="form-label fw-semibold text-secondary">Search Keyword</label>
                                <input type="text" class="form-control py-2" id="keyword" name="keyword" 
                                       value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>"
                                       placeholder="Search by Ref, Title, Sender, or Year...">
                            </div>

                            <!-- Department Dropdown (col-md-4) -->
                            <div class="col-md-4">
                                <label for="department" class="form-label fw-semibold text-secondary">Filter by Department</label>
                                <?php if ($user_role == 'Admin' || $user_role == 'Manager'): ?>
                                    <select class="form-select py-2" id="department" name="department">
                                        <option value="" selected>All Departments / Offices</option>
                                        <?php if (mysqli_num_rows($dept_result) > 0): ?>
                                            <?php while ($dept = mysqli_fetch_assoc($dept_result)): ?>
                                                <option value="<?php echo htmlspecialchars($dept['name']); ?>" <?php echo (isset($_GET['department']) && $_GET['department'] == $dept['name']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($dept['name']); ?>
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                <?php else: ?>
                                    <!-- Locked to Staff's specific department/office -->
                                    <input type="text" class="form-control py-2 bg-light" value="<?php echo htmlspecialchars($user_dept); ?>" readonly>
                                    <input type="hidden" name="department" value="<?php echo htmlspecialchars($user_dept); ?>">
                                <?php endif; ?>
                            </div>

                            <!-- Search & Reset Buttons (col-md-3: Aligns on one line!) -->
                            <div class="col-md-3 d-flex gap-2 align-self-end">
                                <button type="submit" name="search_btn" class="btn btn-primary py-2 fw-bold w-100" style="background-color: #1a365d; border: none;">
                                    <i class="bi bi-search me-1"></i> Search
                                </button>
                                <a href="files.php" class="btn btn-outline-secondary py-2 px-3"><i class="bi bi-arrow-counterclockwise"></i></a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List of Files Card -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white py-3 fw-bold text-dark border-bottom d-flex justify-content-between align-items-center">
                    <span class="mb-0"><i class="bi bi-list-task text-primary me-2"></i> List of Registered Files</span>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#filesTableCollapse" aria-expanded="true" aria-controls="filesTableCollapse">
                            <i class="bi bi-arrows-expand me-1"></i> Toggle List
                        </button>
                        <a href="dashboard.php" class="btn btn-sm btn-outline-secondary fw-bold ms-2">
                            <i class="bi bi-arrow-left-circle me-1"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
                <!-- Collapsible Container Wrapper -->
                <div class="collapse show" id="filesTableCollapse">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ref Number</th>
                                        <th>Title</th>
                                        <th>Sender / Receiver</th>
                                        <th>Category</th>
                                        <th>Doc Date (E.C.)</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (mysqli_num_rows($result) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                            <tr>
                                                <td class="fw-semibold text-primary"><?php echo htmlspecialchars($row['ref_number']); ?></td>
                                                <td><?php echo htmlspecialchars($row['title']); ?></td>
                                                <td><?php echo htmlspecialchars($row['sender_receiver']); ?></td>
                                                <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['department']); ?></span></td>
                                                <td class="text-muted fw-semibold"><?php echo htmlspecialchars($row['document_date']); ?></td>
                                                <td>
                                                    <!-- 1. PREVIEW Button -->
                                                    <a href="uploads/<?php echo $row['file_name']; ?>" target="_blank" class="btn btn-sm btn-outline-primary fw-bold">
                                                        <i class="bi bi-eye-fill me-1"></i> view
                                                    </a>
                                                    <!-- 2. DOWNLOAD Button -->
                                                    <a href="uploads/<?php echo $row['file_name']; ?>" download class="btn btn-sm btn-outline-info fw-bold ms-1">
                                                        <i class="bi bi-download me-1"></i> Download
                                                    </a>
                                                    <!-- 3. TRANSFER Button -->
                                                    <a href="transfer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-success fw-bold ms-1">
                                                        <i class="bi bi-send-fill me-1"></i> Transfer
                                                    </a>
                                                   <!-- 4. DELETE Button (Only visible to Admin and Manager) -->
<?php if ($user_role == 'Admin' || $user_role == 'Manager'): ?>
    <a href="delete_process.php?id=<?php echo $row['id']; ?>&redirect=files" onclick="return confirm('Are you sure you want to move this file to Trash Bin?')" class="btn btn-sm btn-outline-danger fw-bold ms-1">
        <i class="bi bi-trash-fill me-1"></i> Delete
    </a>
<?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-secondary py-4">No registered documents found in your archive department.</td>
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
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

</body>
</html>