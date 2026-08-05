-- Run this once against an existing ip_management database.
-- Fixes three columns whose types could not hold the values the app writes.
USE ip_management;

-- Stored a full date and time, but the column was an integer
ALTER TABLE ip_request
  MODIFY reporting_officer_date_time DATETIME NULL;

-- Stored an IPv4 string, but the column was an integer
ALTER TABLE ip_request
  MODIFY reporting_officer_ip_address VARCHAR(45) NULL;

-- Stored a dotted mask such as 255.255.255.0, but the column was an integer
ALTER TABLE ip_transaction
  MODIFY subnet_mask VARCHAR(15) NOT NULL;

-- Requests that were never actioned were stored as 0, which now means Rejected.
-- 2 is the pending value. Only run this line if you want old untouched
-- requests (those with no officer timestamp) treated as pending again.
UPDATE ip_request
   SET action_status_reporting_officer = 2
 WHERE action_status_reporting_officer = 0
   AND reporting_officer_date_time IS NULL;

-- Table added for the sign in / sign out audit trail
CREATE TABLE IF NOT EXISTS login_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_email VARCHAR(150) NOT NULL,
    user_role VARCHAR(50) NOT NULL,
    login_date_time DATETIME NOT NULL,
    logout_date_time DATETIME NULL
);
