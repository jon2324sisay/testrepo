<?php
// Enable error reporting to show mistakes on screen
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// 1. Clean old data from users table
if (!mysqli_query($conn, "TRUNCATE TABLE users")) {
    die("Truncate users failed: " . mysqli_error($conn));
}

// 2. Clean old data from departments table
if (!mysqli_query($conn, "TRUNCATE TABLE departments")) {
    die("Truncate departments failed: " . mysqli_error($conn));
}

// 3. Insert your actual IT Internship Offices into departments table
$depts = array(
    "Network Administration Office",
    "System Development & Database Office",
    "IT Support & Maintenance Office",
    "Secretary Office"
);

foreach ($depts as $dept) {
    mysqli_query($conn, "INSERT IGNORE INTO departments (name) VALUES ('$dept')");
}

// 4. Generate secure passwords for default users
$admin_hash = password_hash('admin123', PASSWORD_DEFAULT);
$manager_hash = password_hash('manager123', PASSWORD_DEFAULT);
$staff_hash = password_hash('staff123', PASSWORD_DEFAULT);

// 5. Insert Default Users with their FULL NAMES and Departments/Offices (Upgraded!)
// Admin (System Administrator) belongs to "Secretary Office"
mysqli_query($conn, "INSERT INTO users (full_name, username, password, role, department) 
VALUES ('System Administrator', 'admin', '$admin_hash', 'Admin', 'Secretary Office')");

// Manager (Abebech Sisay) belongs to "System Development & Database Office"
mysqli_query($conn, "INSERT INTO users (full_name, username, password, role, department) 
VALUES ('Hamrawit Amare', 'manager', '$manager_hash', 'Manager', 'System Development & Database Office')");

// Staff (Abreham Hayle) belongs to "Network Administration Office"
mysqli_query($conn, "INSERT INTO users (full_name, username, password, role, department) 
VALUES ('Abreham Hayle', 'staff', '$staff_hash', 'Staff', 'Network Administration Office')");

echo "All IT Internship Departments, Full Names, and Users registered successfully!";
?>