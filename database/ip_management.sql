-- IP Management System - full schema
-- Import through phpMyAdmin, or: mysql -u root < ip_management.sql
-- Demo password for all three sample accounts: Password@123

CREATE DATABASE IF NOT EXISTS ip_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ip_management;

-- ---------- master data ----------

CREATE TABLE state_master (
    id bigint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    state_name varchar(100) NOT NULL,
    status int(2) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE district_master (
    id bigint(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    district_name varchar(100) NOT NULL,
    state_id bigint(11) NOT NULL,
    status int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE location_master (
    id bigint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    office_name varchar(150) NOT NULL,
    office_address varchar(200) NOT NULL,
    office_contact bigint(15) NOT NULL,
    district_id bigint(3) NOT NULL,
    state_id bigint(2) NOT NULL,
    status int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE device_master (
    id bigint(3) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    devie_name varchar(200) NOT NULL,
    status int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ---------- users, one table per role ----------

CREATE TABLE reporting_officer (
    id bigint(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    emp_id bigint(4) NOT NULL,
    name varchar(100) NOT NULL,
    designation varchar(100) NOT NULL,
    location_id bigint(3) NOT NULL,
    mobile_no bigint(10) NOT NULL,
    email varchar(60) NOT NULL,
    password varchar(100) NOT NULL COMMENT 'sha256 hash',
    doj date NOT NULL,
    dor date NOT NULL,
    dob date NOT NULL,
    created_date_time datetime NOT NULL,
    ip_address varchar(15) NOT NULL,
    status int(1) NOT NULL DEFAULT 1,
    logged_on datetime NULL
) ENGINE=InnoDB;

CREATE TABLE network_admin (
    id bigint(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    emp_id bigint(4) NOT NULL,
    name varchar(100) NOT NULL,
    designation varchar(100) NOT NULL,
    location_id bigint(3) NOT NULL,
    mobile_no bigint(10) NOT NULL,
    email varchar(60) NOT NULL,
    password varchar(100) NOT NULL,
    doj date NOT NULL,
    dor date NOT NULL,
    dob date NOT NULL,
    created_date_time datetime NOT NULL,
    ip_address varchar(15) NOT NULL,
    status int(1) NOT NULL DEFAULT 1,
    logged_on datetime NULL
) ENGINE=InnoDB;

CREATE TABLE employee (
    id bigint(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    emp_id bigint(4) NOT NULL,
    name varchar(100) NOT NULL,
    designation varchar(100) NOT NULL,
    location_id bigint(3) NOT NULL,
    reporting_officer_id bigint(3) NOT NULL,
    mobile_no bigint(10) NOT NULL,
    email varchar(60) NOT NULL,
    password varchar(100) NOT NULL,
    doj date NOT NULL,
    dor date NOT NULL,
    dob date NOT NULL,
    created_date_time datetime NOT NULL,
    ip_address varchar(15) NOT NULL,
    status int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

-- ---------- workflow ----------

-- action_status_reporting_officer: 0 rejected, 1 approved, 2 pending
CREATE TABLE ip_request (
    id bigint(5) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    emp_id bigint(5) NOT NULL,
    reporting_officer_id bigint(5) NOT NULL,
    device_id bigint(3) NOT NULL,
    mac_address varchar(30) NOT NULL,
    ip_address_of_employee varchar(15) NOT NULL,
    datetime_of_employee datetime NOT NULL,
    action_status_reporting_officer int(1) NOT NULL DEFAULT 2,
    query_reporting_officer varchar(200) NOT NULL DEFAULT '',
    ip_purpose varchar(200) NOT NULL,
    reporting_officer_date_time datetime NULL,
    reporting_officer_ip_address varchar(45) NULL
) ENGINE=InnoDB;

CREATE TABLE ip_transaction (
    id bigint(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    ip_request_id bigint(10) NOT NULL,
    emp_id bigint(5) NOT NULL,
    ip_address varchar(15) NOT NULL,
    ip_address_assigned varchar(15) NOT NULL,
    subnet_mask varchar(15) NOT NULL,
    gateway varchar(15) NOT NULL,
    dns1 varchar(15) NOT NULL,
    dns2 varchar(15) NOT NULL,
    date_time datetime NOT NULL,
    given_by_email varchar(60) NOT NULL,
    used_unsed int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB;

CREATE TABLE login_log (
    id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_email varchar(150) NOT NULL,
    user_role varchar(50) NOT NULL,
    login_date_time datetime NOT NULL,
    logout_date_time datetime NULL
) ENGINE=InnoDB;

-- ---------- seed data ----------

INSERT INTO state_master (id, state_name, status) VALUES (1, 'Jharkhand', 1);
INSERT INTO district_master (id, district_name, state_id, status) VALUES (1, 'Ranchi', 1, 1);
INSERT INTO location_master (id, office_name, office_address, office_contact, district_id, state_id, status)
VALUES (1, 'NIC Jharkhand State Centre', 'Nepal House, Doranda, Ranchi', 6512490000, 1, 1, 1);

INSERT INTO device_master (devie_name, status) VALUES
('Desktop', 1), ('Laptop', 1), ('Printer', 1), ('Server', 1), ('Router', 1), ('Switch', 1);

-- Demo accounts, password Password@123
INSERT INTO reporting_officer (id, emp_id, name, designation, location_id, mobile_no, email, password, doj, dor, dob, created_date_time, ip_address, status)
VALUES (1, 200, 'Demo Officer', 'Director', 1, 9400000001, 'officer@nic.in', 'ff7bd97b1a7789ddd2775122fd6817f3173672da9f802ceec57f284325bf589f', '2023-01-02', '2035-01-01', '1980-05-10', NOW(), '127.0.0.1', 1);

INSERT INTO network_admin (id, emp_id, name, designation, location_id, mobile_no, email, password, doj, dor, dob, created_date_time, ip_address, status)
VALUES (1, 300, 'Demo Admin', 'Network Administrator', 1, 9400000002, 'admin@nic.in', 'ff7bd97b1a7789ddd2775122fd6817f3173672da9f802ceec57f284325bf589f', '2023-01-02', '2035-01-01', '1985-08-20', NOW(), '127.0.0.1', 1);

INSERT INTO employee (id, emp_id, name, designation, location_id, reporting_officer_id, mobile_no, email, password, doj, dor, dob, created_date_time, ip_address, status)
VALUES (1, 100, 'Demo Employee', 'Scientist B', 1, 1, 9400000003, 'employee@nic.in', 'ff7bd97b1a7789ddd2775122fd6817f3173672da9f802ceec57f284325bf589f', '2023-01-02', '2035-01-01', '1995-03-15', NOW(), '127.0.0.1', 1);
