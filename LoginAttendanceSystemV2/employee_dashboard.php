<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'employee') {
    header("Location: login.php");
    exit();
}

date_default_timezone_set('Asia/Manila');
$employee_id = $_SESSION['user_id'];

// Handle Check In/Out Actions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    
    // Get current UTC time from MySQL server
    $result = $conn->query("SELECT UTC_TIMESTAMP() AS utc_now");
    $row = $result->fetch_assoc();
    $now_utc = new DateTime($row['utc_now'], new DateTimeZone('UTC'));
    
    // Convert to Manila time for display
    $now_ph = clone $now_utc;
    $now_ph->setTimezone(new DateTimeZone('Asia/Manila'));

    // Calculate UTC date boundaries for current PH date
    $ph_today_start = new DateTime('today', new DateTimeZone('Asia/Manila'));
    $utc_today_start = clone $ph_today_start;
    $utc_today_start->setTimezone(new DateTimeZone('UTC'));

    $ph_today_end = new DateTime('tomorrow', new DateTimeZone('Asia/Manila'));
    $utc_today_end = clone $ph_today_end;
    $utc_today_end->setTimezone(new DateTimeZone('UTC'));

    if ($action == 'check_in') {
        // Check for existing check-in today
        $check_sql = "SELECT id FROM attendance 
                     WHERE employee_id = ?
                     AND check_in BETWEEN ? AND ?
                     LIMIT 1";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("iss", $employee_id, 
            $utc_today_start->format('Y-m-d H:i:s'),
            $utc_today_end->format('Y-m-d H:i:s')
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 0) {
            $insert_sql = "INSERT INTO attendance (employee_id, check_in) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("is", $employee_id, $now_utc->format('Y-m-d H:i:s'));
            if ($stmt->execute()) {
                $_SESSION['message'] = "Checked in at " . $now_ph->format('g:i A');
            } else {
                $_SESSION['error'] = "Check-in failed: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "You already checked in today!";
        }
    } 
    elseif ($action == 'check_out') {
        $check_sql = "SELECT id FROM attendance 
                     WHERE employee_id = ?
                     AND check_in BETWEEN ? AND ?
                     AND check_out IS NULL
                     LIMIT 1";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("iss", $employee_id, 
            $utc_today_start->format('Y-m-d H:i:s'),
            $utc_today_end->format('Y-m-d H:i:s')
        );
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $update_sql = "UPDATE attendance
                          SET check_out = ?
                          WHERE employee_id = ?
                          AND check_in BETWEEN ? AND ?
                          LIMIT 1";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("siss", 
                $now_utc->format('Y-m-d H:i:s'),
                $employee_id,
                $utc_today_start->format('Y-m-d H:i:s'),
                $utc_today_end->format('Y-m-d H:i:s')
            );
            
            if ($stmt->execute()) {
                $_SESSION['message'] = "Checked out at " . $now_ph->format('g:i A');
            } else {
                $_SESSION['error'] = "Check-out failed: " . $conn->error;
            }
        } else {
            $_SESSION['error'] = "No check-in found for today or already checked out!";
        }
    }
    
    header("Location: employee_dashboard.php");
    exit();
}

// Calculate Statistics
$attendance_streak = 0;
$total_hours = 0;

// Get attendance records
$records_sql = "SELECT check_in, check_out FROM attendance
               WHERE employee_id = ?
               ORDER BY check_in DESC";
$stmt = $conn->prepare($records_sql);
$stmt->bind_param("i", $employee_id);
$stmt->execute();
$records_result = $stmt->get_result();

$all_records = [];
$streak_dates = [];

while ($row = $records_result->fetch_assoc()) {
    // Convert UTC times to Manila timezone
    $check_in = new DateTime($row['check_in'], new DateTimeZone('UTC'));
    $check_in->setTimezone(new DateTimeZone('Asia/Manila'));
    
    $check_out = null;
    if (!empty($row['check_out'])) {
        $check_out = new DateTime($row['check_out'], new DateTimeZone('UTC'));
        $check_out->setTimezone(new DateTimeZone('Asia/Manila'));
    }

    $all_records[] = [
        'check_in' => $check_in,
        'check_out' => $check_out
    ];
    $streak_dates[] = $check_in->format('Y-m-d');
}

// Calculate attendance streak
$current_streak = 0;
$previous_date = null;

foreach ($streak_dates as $date) {
    $current_date = new DateTime($date);
    
    if ($previous_date === null) {
        $current_streak = 1;
        $previous_date = $current_date;
        continue;
    }

    $previous_date->modify('-1 day');
    if ($current_date->format('Y-m-d') === $previous_date->format('Y-m-d')) {
        $current_streak++;
    } else {
        break;
    }
    $previous_date = new DateTime($date);
}

$attendance_streak = $current_streak;

// Calculate total hours
foreach ($all_records as $record) {
    if ($record['check_out']) {
        $diff = $record['check_out']->diff($record['check_in']);
        $total_hours += $diff->h + ($diff->i / 60);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        .card { transition: transform 0.2s; }
        .card:hover { transform: translateY(-3px); }
        .streak-active { color: #dc3545; animation: pulse 1.5s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.5; } }
    </style>
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-primary mb-4">
        <div class="container">
            <span class="navbar-brand">Philippine TimeTrack</span>
            <div class="d-flex align-items-center">
                <span class="text-white me-3"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show mt-2" role="alert">
            <?= $_SESSION['message'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message']); endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show mt-2" role="alert">
            <?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-3">Total Hours</h5>
                        <h2 class="display-4"><?= number_format($total_hours, 1) ?></h2>
                        <i class="fas fa-clock fa-3x text-primary mt-3"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center">
                        <h5 class="text-muted mb-3">Attendance Streak</h5>
                        <h2 class="display-4 <?= $attendance_streak >= 3 ? 'streak-active' : '' ?>">
                            <?= $attendance_streak ?>
                        </h2>
                        <i class="fas fa-fire fa-3x text-danger mt-3"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card shadow h-100">
                    <div class="card-body text-center d-flex flex-column">
                        <h5 class="text-muted mb-3">Today's Action</h5>
                        <form method="post" class="mt-auto">
                            <div class="btn-group w-100">
                                <button name="action" value="check_in"
                                    class="btn btn-success btn-lg py-3">
                                    <i class="fas fa-sign-in-alt"></i> CHECK IN
                                </button>
                                <button name="action" value="check_out"
                                    class="btn btn-danger btn-lg py-3">
                                    <i class="fas fa-sign-out-alt"></i> CHECK OUT
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-history me-2"></i>Attendance Records (PH Time)</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Duration</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($all_records as $record): ?>
                            <tr>
                                <td><?= $record['check_in']->format('M j, Y') ?></td>
                                <td><?= $record['check_in']->format('g:i A') ?></td>
                                <td>
                                    <?php if ($record['check_out']): ?>
                                        <?= $record['check_out']->format('g:i A') ?>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($record['check_out']): 
                                        $diff = $record['check_out']->diff($record['check_in']);
                                        echo $diff->h . 'h ' . $diff->i . 'm';
                                    else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>