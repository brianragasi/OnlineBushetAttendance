-- Create the employees table
CREATE TABLE IF NOT EXISTS employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    hourly_rate DECIMAL(10, 2) NOT NULL,
    is_admin INT NOT NULL DEFAULT 0
);

-- Create the attendance table
CREATE TABLE IF NOT EXISTS attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    check_in DATETIME NOT NULL,
    check_out DATETIME
    -- No foreign key constraint here
);

-- Create the payroll table
CREATE TABLE IF NOT EXISTS payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    pay_period_start DATE NOT NULL,
    pay_period_end DATE NOT NULL,
    gross_pay DECIMAL(10, 2) NOT NULL,
    tax_deduction DECIMAL(10, 2) NOT NULL,
    net_pay DECIMAL(10, 2) NOT NULL
    -- No foreign key constraint here
);

-- Add foreign keys to both attendance and payroll table
ALTER TABLE attendance
ADD CONSTRAINT fk_attendance_employee
FOREIGN KEY (employee_id) REFERENCES employees(id);

ALTER TABLE payroll
ADD CONSTRAINT fk_payroll_employee
FOREIGN KEY (employee_id) REFERENCES employees(id);
