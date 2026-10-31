-- Run this once against an existing ip_management database.
-- Brings it up to the current schema in ip_management.sql.
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

-- Set when the network admin assigns an IP for a request, so assigned requests
-- leave the assign list. For existing data, mark the request each transaction
-- points to, plus the earlier approved requests for that same employee and MAC.
ALTER TABLE ip_request
  ADD COLUMN assigned_date_time DATETIME NULL;

UPDATE ip_request r
  JOIN ip_transaction t ON t.emp_id = r.emp_id
  JOIN ip_request cur ON cur.id = t.ip_request_id AND cur.mac_address = r.mac_address
   SET r.assigned_date_time = t.date_time
 WHERE r.id <= cur.id
   AND r.action_status_reporting_officer = 1;

-- Passwords are now stored with password_hash(), which needs more room.
-- Existing SHA-256 hashes keep working and are replaced at each user's next login.
ALTER TABLE reporting_officer MODIFY password VARCHAR(255) NOT NULL;
ALTER TABLE network_admin MODIFY password VARCHAR(255) NOT NULL;
ALTER TABLE employee MODIFY password VARCHAR(255) NOT NULL;

-- Purposes used to be saved HTML-escaped (R&D was stored as R&amp;D).
-- New requests are saved as typed, so turn the old ones back.
-- &amp; goes last so text that was typed as "&lt;" stays "&lt;".
UPDATE ip_request
   SET ip_purpose = REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(ip_purpose,
       '&lt;', '<'), '&gt;', '>'), '&quot;', '"'), '&#039;', ''''), '&amp;', '&');

-- Foreign keys. If one fails, a row points at something that doesn't exist or the
-- two columns have different types: fix that, then run the failed statement again.
ALTER TABLE employee
  ADD CONSTRAINT fk_employee_officer FOREIGN KEY (reporting_officer_id) REFERENCES reporting_officer (id);
ALTER TABLE ip_request
  ADD CONSTRAINT fk_request_employee FOREIGN KEY (emp_id) REFERENCES employee (id),
  ADD CONSTRAINT fk_request_officer FOREIGN KEY (reporting_officer_id) REFERENCES reporting_officer (id),
  ADD CONSTRAINT fk_request_device FOREIGN KEY (device_id) REFERENCES device_master (id);
ALTER TABLE ip_transaction
  ADD CONSTRAINT fk_transaction_request FOREIGN KEY (ip_request_id) REFERENCES ip_request (id),
  ADD CONSTRAINT fk_transaction_employee FOREIGN KEY (emp_id) REFERENCES employee (id);
