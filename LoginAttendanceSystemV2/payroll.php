<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $employee_name = $_POST['employee_name'];
    $pay_period_start = $_POST['pay_period_start'];
    $pay_period_end = $_POST['pay_period_end'];

    // Get the employee_id from the database
    $sql_employee = "SELECT id FROM employees WHERE name = '$employee_name'";
    $result_employee = $conn->query($sql_employee);

    if ($result_employee->num_rows > 0) {
        $row_employee = $result_employee->fetch_assoc();
        $employee_id = $row_employee['id'];

        // Calculate total hours worked
        $sql = "SELECT SUM(TIMESTAMPDIFF(SECOND, check_in, check_out)) AS total_seconds
                FROM attendance
                WHERE employee_id = $employee_id
                AND check_in >= '$pay_period_start'
                AND check_out <= '$pay_period_end'";

        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $total_seconds = $row['total_seconds'];
        $total_hours = $total_seconds ? $total_seconds / 3600 : 0;

        // Get hourly rate and employee name
        $sql = "SELECT hourly_rate, name FROM employees WHERE id=$employee_id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $hourly_rate = $row['hourly_rate'];
        $employee_name = $row['name']; // Fetch the employee name

        // Calculate gross pay
        $gross_pay = $total_hours * $hourly_rate;

        // --- Calculate Deductions (Detailed Breakdown) ---

        // Income Tax (Reduced Example - Replace with actual tax calculation)
        $taxable_income = $gross_pay; // You might need to adjust this based on allowances

        // ** Reduced Tax Calculation (Example) **
        $income_tax = calculateIncomeTax($taxable_income);

        // SSS Contribution (Example - Replace with actual SSS table lookup)
        $sss_contribution = calculateSSS($gross_pay); // Replace with your SSS calculation logic

        // PhilHealth Contribution (Example - Replace with actual PhilHealth table lookup)
        $philhealth_contribution = calculatePhilHealth($gross_pay); // Replace with your PhilHealth calculation logic

        // Pag-IBIG Contribution (Example - Fixed amount or based on salary)
        $pagibig_contribution = 100; // Replace with your Pag-IBIG calculation logic

        // Total Deductions
        $total_deductions = $income_tax + $sss_contribution + $philhealth_contribution + $pagibig_contribution;

        // Net Pay
        $net_pay = $gross_pay - $total_deductions;

        // Insert payroll record
        $sql = "INSERT INTO payroll (employee_id, pay_period_start, pay_period_end, gross_pay, tax_deduction, sss_deduction, philhealth_deduction, pagibig_deduction, net_pay)
                VALUES ($employee_id, '$pay_period_start', '$pay_period_end', $gross_pay, $income_tax, $sss_contribution, $philhealth_contribution, $pagibig_contribution, $net_pay)";

        if ($conn->query($sql) === TRUE) {
            $success_message = "Payroll generated successfully for " . $employee_name;
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error_message = "Employee not found.";
    }
}

// Placeholder functions for deduction calculations (REPLACE THESE)
function calculateIncomeTax($taxable_income) {
    // ** REPLACE THIS WITH THE ACTUAL PHILIPPINE INCOME TAX CALCULATION LOGIC **
    // This is a very simplified example with reduced tax rates and not accurate for real tax calculations.
    // You'll likely need a tiered system based on income brackets.

    if ($taxable_income <= 20833) {
        $tax = 0;
    } elseif ($taxable_income <= 33332) {
        $tax = ($taxable_income - 20833) * 0.15; // Reduced from 20%
    } elseif ($taxable_income <= 66666) {
        $tax = 1875 + ($taxable_income - 33333) * 0.20; // Reduced from 25%
    } else {
        // ... (add more brackets for higher income)
        $tax = 8541.67 + ($taxable_income - 66667) * 0.25; // Reduced from 30%
    }
    return $tax;
}

function calculateSSS($gross_pay) {
    // ** REPLACE THIS WITH THE ACTUAL SSS CONTRIBUTION TABLE LOOKUP **
    // This is a placeholder. You'll need to use the SSS contribution table
    // to determine the correct deduction based on the salary range.
    if ($gross_pay < 3250) {
        $sss = 135;
    } else if ($gross_pay >= 3250 && $gross_pay < 3750) {
        $sss = 157.5;
    } else if ($gross_pay >= 3750 && $gross_pay < 4250) {
        $sss = 180;
    } else if ($gross_pay >= 4250 && $gross_pay < 4750) {
        $sss = 202.5;
    } else if ($gross_pay >= 4750 && $gross_pay < 5250) {
        $sss = 225;
    } else if ($gross_pay >= 5250 && $gross_pay < 5750) {
        $sss = 247.5;
    } else if ($gross_pay >= 5750 && $gross_pay < 6250) {
        $sss = 270;
    } else if ($gross_pay >= 6250 && $gross_pay < 6750) {
        $sss = 292.5;
    } else if ($gross_pay >= 6750 && $gross_pay < 7250) {
        $sss = 315;
    } else if ($gross_pay >= 7250 && $gross_pay < 7750) {
        $sss = 337.5;
    } else if ($gross_pay >= 7750 && $gross_pay < 8250) {
        $sss = 360;
    } else if ($gross_pay >= 8250 && $gross_pay < 8750) {
        $sss = 382.5;
    } else if ($gross_pay >= 8750 && $gross_pay < 9250) {
        $sss = 405;
    } else if ($gross_pay >= 9250 && $gross_pay < 9750) {
        $sss = 427.5;
    } else if ($gross_pay >= 9750 && $gross_pay < 10250) {
        $sss = 450;
    } else if ($gross_pay >= 10250 && $gross_pay < 10750) {
        $sss = 472.5;
    } else if ($gross_pay >= 10750 && $gross_pay < 11250) {
        $sss = 495;
    } else if ($gross_pay >= 11250 && $gross_pay < 11750) {
        $sss = 517.5;
    } else if ($gross_pay >= 11750 && $gross_pay < 12250) {
        $sss = 540;
    } else if ($gross_pay >= 12250 && $gross_pay < 12750) {
        $sss = 562.5;
    } else if ($gross_pay >= 12750 && $gross_pay < 13250) {
        $sss = 585;
    } else if ($gross_pay >= 13250 && $gross_pay < 13750) {
        $sss = 607.5;
    } else if ($gross_pay >= 13750 && $gross_pay < 14250) {
        $sss = 630;
    } else if ($gross_pay >= 14250 && $gross_pay < 14750) {
        $sss = 652.5;
    } else if ($gross_pay >= 14750 && $gross_pay < 15250) {
        $sss = 675;
    } else if ($gross_pay >= 15250 && $gross_pay < 15750) {
        $sss = 697.5;
    } else if ($gross_pay >= 15750 && $gross_pay < 16250) {
        $sss = 720;
    } else if ($gross_pay >= 16250 && $gross_pay < 16750) {
        $sss = 742.5;
    } else if ($gross_pay >= 16750 && $gross_pay < 17250) {
        $sss = 765;
    } else if ($gross_pay >= 17250 && $gross_pay < 17750) {
        $sss = 787.5;
    } else if ($gross_pay >= 17750 && $gross_pay < 18250) {
        $sss = 810;
    } else if ($gross_pay >= 18250 && $gross_pay < 18750) {
        $sss = 832.5;
    } else if ($gross_pay >= 18750 && $gross_pay < 19250) {
        $sss = 855;
    } else if ($gross_pay >= 19250 && $gross_pay < 19750) {
        $sss = 877.5;
    } else if ($gross_pay >= 19750 && $gross_pay < 20250) {
        $sss = 900;
    } else if ($gross_pay >= 20250 && $gross_pay < 20750) {
        $sss = 922.5;
    } else if ($gross_pay >= 20750 && $gross_pay < 21250) {
        $sss = 945;
    } else if ($gross_pay >= 21250 && $gross_pay < 21750) {
        $sss = 967.5;
    } else if ($gross_pay >= 21750 && $gross_pay < 22250) {
        $sss = 990;
    } else if ($gross_pay >= 22250 && $gross_pay < 22750) {
        $sss = 1012.5;
    } else if ($gross_pay >= 22750 && $gross_pay < 23250) {
        $sss = 1035;
    } else if ($gross_pay >= 23250 && $gross_pay < 23750) {
        $sss = 1057.5;
    } else if ($gross_pay >= 23750 && $gross_pay < 24250) {
        $sss = 1080;
    } else if ($gross_pay >= 24250 && $gross_pay < 24750) {
        $sss = 1102.5;
    } else {
        $sss = 1125;
    }
    return $sss;
}

function calculatePhilHealth($gross_pay) {
    // ** REPLACE THIS WITH THE ACTUAL PHILHEALTH CONTRIBUTION TABLE LOOKUP **
    // This is a made-up table for demonstration purposes only.
    // Use the real PhilHealth table from the official source.

    if ($gross_pay <= 10000.00) {
        $philhealth = 175.00;
    } else if ($gross_pay >= 10000.01 && $gross_pay <= 10999.99) {
        $philhealth = 175.00;
    } else if ($gross_pay >= 11000.00 && $gross_pay <= 11999.99) {
        $philhealth = 192.50;
    } else if ($gross_pay >= 12000.00 && $gross_pay <= 12999.99) {
        $philhealth = 210.00;
    } else if ($gross_pay >= 13000.00 && $gross_pay <= 13999.99) {
        $philhealth = 227.50;
    } else if ($gross_pay >= 14000.00 && $gross_pay <= 14999.99) {
        $philhealth = 245.00;
    } else if ($gross_pay >= 15000.00 && $gross_pay <= 15999.99) {
        $philhealth = 262.50;
    } else if ($gross_pay >= 16000.00 && $gross_pay <= 16999.99) {
        $philhealth = 280.00;
    } else if ($gross_pay >= 17000.00 && $gross_pay <= 17999.99) {
        $philhealth = 297.50;
    } else if ($gross_pay >= 18000.00 && $gross_pay <= 18999.99) {
        $philhealth = 315.00;
    } else if ($gross_pay >= 19000.00 && $gross_pay <= 19999.99) {
        $philhealth = 332.50;
    } else if ($gross_pay >= 20000.00 && $gross_pay <= 20999.99) {
        $philhealth = 350.00;
    } else if ($gross_pay >= 21000.00 && $gross_pay <= 21999.99) {
        $philhealth = 367.50;
    } else if ($gross_pay >= 22000.00 && $gross_pay <= 22999.99) {
        $philhealth = 385.00;
    } else if ($gross_pay >= 23000.00 && $gross_pay <= 23999.99) {
        $philhealth = 402.50;
    } else if ($gross_pay >= 24000.00 && $gross_pay <= 24999.99) {
        $philhealth = 420.00;
    } else if ($gross_pay >= 25000.00 && $gross_pay <= 25999.99) {
        $philhealth = 437.50;
    } else if ($gross_pay >= 26000.00 && $gross_pay <= 26999.99) {
        $philhealth = 455.00;
    } else if ($gross_pay >= 27000.00 && $gross_pay <= 27999.99) {
        $philhealth = 472.50;
    } else if ($gross_pay >= 28000.00 && $gross_pay <= 28999.99) {
        $philhealth = 490.00;
    } else if ($gross_pay >= 29000.00 && $gross_pay <= 29999.99) {
        $philhealth = 507.50;
    } else if ($gross_pay >= 30000.00 && $gross_pay <= 30999.99) {
        $philhealth = 525.00;
    } else if ($gross_pay >= 31000.00 && $gross_pay <= 31999.99) {
        $philhealth = 542.50;
    } else if ($gross_pay >= 32000.00 && $gross_pay <= 32999.99) {
        $philhealth = 560.00;
    } else if ($gross_pay >= 33000.00 && $gross_pay <= 33999.99) {
        $philhealth = 577.50;
    } else if ($gross_pay >= 34000.00 && $gross_pay <= 34999.99) {
        $philhealth = 595.00;
    } else if ($gross_pay >= 35000.00 && $gross_pay <= 35999.99) {
        $philhealth = 612.50;
    } else if ($gross_pay >= 36000.00 && $gross_pay <= 36999.99) {
        $philhealth = 630.00;
    } else if ($gross_pay >= 37000.00 && $gross_pay <= 37999.99) {
        $philhealth = 647.50;
    } else if ($gross_pay >= 38000.00 && $gross_pay <= 38999.99) {
        $philhealth = 665.00;
    } else if ($gross_pay >= 39000.00 && $gross_pay <= 39999.99) {
        $philhealth = 682.50;
    } else if ($gross_pay >= 40000.00 && $gross_pay <= 40999.99) {
        $philhealth = 700.00;
    } else if ($gross_pay >= 41000.00 && $gross_pay <= 41999.99) {
        $philhealth = 717.50;
    } else if ($gross_pay >= 42000.00 && $gross_pay <= 42999.99) {
        $philhealth = 735.00;
    } else if ($gross_pay >= 43000.00 && $gross_pay <= 43999.99) {
        $philhealth = 752.50;
    } else if ($gross_pay >= 44000.00 && $gross_pay <= 44999.99) {
        $philhealth = 770.00;
    } else if ($gross_pay >= 45000.00 && $gross_pay <= 45999.99) {
        $philhealth = 787.50;
    } else if ($gross_pay >= 46000.00 && $gross_pay <= 46999.99) {
        $philhealth = 805.00;
    } else if ($gross_pay >= 47000.00 && $gross_pay <= 47999.99) {
        $philhealth = 822.50;
    } else if ($gross_pay >= 48000.00 && $gross_pay <= 48999.99) {
        $philhealth = 840.00;
    } else if ($gross_pay >= 49000.00 && $gross_pay <= 49999.99) {
        $philhealth = 857.50;
    } else if ($gross_pay >= 50000.00 && $gross_pay <= 50999.99) {
        $philhealth = 875.00;
    } else if ($gross_pay >= 51000.00 && $gross_pay <= 51999.99) {
        $philhealth = 892.50;
    } else if ($gross_pay >= 52000.00 && $gross_pay <= 52999.99) {
        $philhealth = 910.00;
    } else if ($gross_pay >= 53000.00 && $gross_pay <= 53999.99) {
        $philhealth = 927.50;
    } else if ($gross_pay >= 54000.00 && $gross_pay <= 54999.99) {
        $philhealth = 945.00;
    } else if ($gross_pay >= 55000.00 && $gross_pay <= 55999.99) {
        $philhealth = 962.50;
    } else if ($gross_pay >= 56000.00 && $gross_pay <= 56999.99) {
        $philhealth = 980.00;
    } else if ($gross_pay >= 57000.00 && $gross_pay <= 57999.99) {
        $philhealth = 997.50;
    } else if ($gross_pay >= 58000.00 && $gross_pay <= 58999.99) {
        $philhealth = 1015.00;
    } else if ($gross_pay >= 59000.00 && $gross_pay <= 59999.99) {
        $philhealth = 1032.50;
    } else if ($gross_pay >= 60000.00 && $gross_pay <= 60999.99) {
        $philhealth = 1050.00;
    } else if ($gross_pay >= 61000.00 && $gross_pay <= 61999.99) {
        $philhealth = 1067.50;
    } else if ($gross_pay >= 62000.00 && $gross_pay <= 62999.99) {
        $philhealth = 1085.00;
    } else if ($gross_pay >= 63000.00 && $gross_pay <= 63999.99) {
        $philhealth = 1102.50;
    } else if ($gross_pay >= 64000.00 && $gross_pay <= 64999.99) {
        $philhealth = 1120.00;
    } else if ($gross_pay >= 65000.00 && $gross_pay <= 65999.99) {
        $philhealth = 1137.50;
    } else if ($gross_pay >= 66000.00 && $gross_pay <= 66999.99) {
        $philhealth = 1155.00;
    } else if ($gross_pay >= 67000.00 && $gross_pay <= 67999.99) {
        $philhealth = 1172.50;
    } else if ($gross_pay >= 68000.00 && $gross_pay <= 68999.99) {
        $philhealth = 1190.00;
    } else if ($gross_pay >= 69000.00 && $gross_pay <= 69999.99) {
        $philhealth = 1207.50;
    } else if ($gross_pay >= 70000.00 && $gross_pay <= 70999.99) {
        $philhealth = 1225.00;
    } else if ($gross_pay >= 71000.00 && $gross_pay <= 71999.99) {
        $philhealth = 1242.50;
    } else if ($gross_pay >= 72000.00 && $gross_pay <= 72999.99) {
        $philhealth = 1260.00;
    } else if ($gross_pay >= 73000.00 && $gross_pay <= 73999.99) {
        $philhealth = 1277.50;
    } else if ($gross_pay >= 74000.00 && $gross_pay <= 74999.99) {
        $philhealth = 1295.00;
    } else if ($gross_pay >= 75000.00 && $gross_pay <= 75999.99) {
        $philhealth = 1312.50;
    } else if ($gross_pay >= 76000.00 && $gross_pay <= 76999.99) {
        $philhealth = 1330.00;
    } else if ($gross_pay >= 77000.00 && $gross_pay <= 77999.99) {
        $philhealth = 1347.50;
    } else if ($gross_pay >= 78000.00 && $gross_pay <= 78999.99) {
        $philhealth = 1365.00;
    } else if ($gross_pay >= 79000.00 && $gross_pay <= 79999.99) {
        $philhealth = 1382.50;
    } else if ($gross_pay >= 80000.00) {
        $philhealth = 1400.00;
    }
    return $philhealth;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Calculation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .card { margin-top: 20px; }
        .card-header { background-color: #007bff; color: #fff; }
        .table { margin-top: 20px; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2 class="text-center">Payroll Calculation</h2>
            </div>
            <div class="card-body">
                <?php if (isset($success_message)): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $success_message; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($error_message)): ?>
                    <h3 class="card-title">Payroll Details for <?php echo $employee_name; ?></h3>
                    <table class="table table-striped">
                        <tbody>
                            <tr>
                                <th scope="row">Pay Period Start:</th>
                                <td><?php echo date('F j, Y', strtotime($pay_period_start)); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Pay Period End:</th>
                                <td><?php echo date('F j, Y', strtotime($pay_period_end)); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Total Hours Worked:</th>
                                <td><?php echo number_format($total_hours, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Hourly Rate:</th>
                                <!-- Display the hourly rate in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($hourly_rate, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Gross Pay:</th>
                                <!-- Display the gross pay in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($gross_pay, 2); ?></td>
                            </tr>
                            <tr>
                                <td colspan="2" class="table-secondary"><strong>Deductions</strong></td>
                            </tr>
                            <tr>
                                <th scope="row">Income Tax:</th>
                                <!-- Display the income tax in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($income_tax, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">SSS Contribution:</th>
                                <!-- Display the SSS contribution in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($sss_contribution, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">PhilHealth Contribution:</th>
                                <!-- Display the PhilHealth contribution in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($philhealth_contribution, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Pag-IBIG Contribution:</th>
                                <!-- Display the Pag-IBIG contribution in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($pagibig_contribution, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Total Deductions:</th>
                                <!-- Display the total deductions in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($total_deductions, 2); ?></td>
                            </tr>
                            <tr>
                                <th scope="row">Net Pay:</th>
                                <!-- Display the net pay in Philippine Peso (PHP) -->
                                <td>₱<?php echo number_format($net_pay, 2); ?></td>
                            </tr>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>