# IP Management System

A centralized system for requesting, approving and assigning IP addresses inside an organization. Built during my internship to replace the manual process of tracking IP and MAC allocations.

**Stack:** PHP, MySQL, Apache (XAMPP), Bootstrap, jQuery

## How it works

Three roles, one workflow:

1. **Employee** logs in, fills in a device ID, MAC address and purpose, and submits an IP request. They can track the status of their requests and see the IPs assigned to their devices.
2. **Reporting Officer** reviews pending requests from their employees and approves or rejects them, with remarks on rejection.
3. **Network Admin** sees approved requests and assigns the actual network details: IP address, subnet mask, gateway and DNS. Every assignment is recorded as a transaction with who assigned it and when. Reassigning a device (same employee and MAC address) updates its existing transaction instead of duplicating it.

Login uses a role selector and a captcha. Passwords are stored with PHP's `password_hash()` and checked with `password_verify()`, so a copy of the database doesn't give away anyone's password; serve the site over HTTPS so the password is also encrypted on its way to the server. Sessions carry a token that is checked on every form submission, and all sign ins/outs are recorded in a login log.

## Project structure

```
ip-management-system/
├── index.php                  # redirects to public/
├── .htaccess                  # blocks web access to config/ and database/
├── database/
│   └── ip_management.sql      # full schema + sample users
├── config/
│   ├── config.php             # DB credentials (env vars or edit here)
│   └── db.php                 # mysqli connection + session + helpers
├── public/                    # login page and auth endpoints
│   ├── index.php              # landing + login modal
│   ├── login-exec.php
│   ├── captcha.php
│   └── logout.php
├── authUser/
│   ├── employee/              # dashboard, request_ip, view status, view assigned
│   ├── reporting_officer/     # dashboard, review requests, approve/reject
│   └── network_admin/         # dashboard, assign IP, view approved
└── assets/                    # one shared copy of css/js/vendor/images/fonts
```

The original build had a full copy of Bootstrap, Font Awesome and every image inside each role folder (four copies of everything). Those are now deduplicated into a single `assets/` directory that all pages reference.

## Running locally (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL** from the control panel.
2. Copy this folder into `C:\xampp\htdocs\` so you have `C:\xampp\htdocs\ip-management-system\`.
3. Open http://localhost/phpmyadmin, go to Import, and import `database/ip_management.sql`. That creates the `ip_management` database, all six tables, and one demo user per role.
4. Open http://localhost/ip-management-system/

Demo logins (password for all three: `Password@123`):

| Role | Email |
|---|---|
| Employee | employee@example.com |
| Reporting Officer | officer@example.com |
| Network Admin | admin@example.com |

Passwords must satisfy the policy the login page enforces: at least 8 characters with an uppercase letter, a lowercase letter, a digit and one of `!@#$&_`.

Default XAMPP MySQL credentials (root, no password) are already set in `config/config.php`. Change them there if yours differ.

### Already have the original database?

If you are running against the database from the internship rather than a fresh import, run `database/migration_fix_column_types.sql` once. It widens three columns that could not hold the values the app writes (an integer column that stored a datetime, another that stored an IP string, and the subnet mask), creates the `login_log` table, adds `ip_request.assigned_date_time` (filled in for requests that already have an IP), widens the password columns for `password_hash()`, turns purposes that were saved HTML-escaped (`R&amp;D`) back into plain text, and adds the foreign keys. Existing SHA-256 passwords keep working and are upgraded at each user's next login.

## Deploying to a live server

Any host with Apache, PHP 8+ and MySQL works. The general steps:

1. Create a MySQL database on the host and import `database/ip_management.sql`.
2. Upload the project files to the web root (or a subdirectory).
3. Set the DB credentials, either as environment variables (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`) or by editing `config/config.php`.
4. Make sure `.htaccess` is respected (`AllowOverride All` in the Apache vhost) so `config/` and `database/` stay unreachable from the browser.
5. Delete the demo users and create real ones with your own emails. Passwords are stored with `password_hash()`; generate one with `php -r "echo password_hash('yourpassword', PASSWORD_DEFAULT);"`.
6. Serve the site over HTTPS, since the login form sends the password to the server.

For a quick free option, an always-free PHP host or a small VPS with a LAMP stack both work; the app has no dependencies beyond PHP's mysqli and GD (for the captcha), both of which ship with standard PHP installs.

## Database

Six tables: one per role (`employee`, `reporting_officer`, `network_admin`), `ip_request` for the request workflow, `ip_transaction` for actual assignments, and `login_log` for the sign in audit trail. Foreign keys link employees to their reporting officer, every request to its employee, officer and device, and every transaction back to its request and employee.

## Cleanup changelog

Things fixed while preparing this version, compared to the internship build:

- Removed a stray Python virtualenv, four duplicate zip archives, temp code runner files and a Jupyter notebook that had ended up inside the project.
- Deduplicated all frontend assets (Bootstrap, Font Awesome, jQuery, Chart.js, images, fonts) from four copies down to one shared `assets/` folder, and removed the unused template demo pages (chart samples, typography, forms and so on).
- Moved DB credentials out of the connection file into `config/config.php` with environment variable support.
- Fixed the logout page, which referenced a PDO handle that never existed, and standardized the filename casing (`Logout.php` vs `logout.php` breaks on Linux servers).
- Fixed the employee session: `emp_id` and `reporting_officer_id` were read on every employee page but never set at login, so requests could not actually be submitted.
- Converted the approve/reject handler from string interpolated SQL to prepared statements, and rewrote the two employee view pages with prepared statements and escaped output.
- "View assigned IPs" now actually shows assignments from `ip_transaction` instead of repeating the request list.
- Added the missing database schema file, demo users, `.htaccess` protection and this README.

Bugs found by running the whole workflow end to end against a copy of the real database:

- `config/db.php` was being included twice through different relative paths, so `require_once` did not deduplicate and PHP fatalled on a redeclared function. Every include is now `require_once __DIR__ . '/...'`, and the file carries its own include guard.
- Requests were inserting the string `Pending` into an integer status column, which silently became 0, the value meaning Rejected. Status is now a shared set of constants (0 rejected, 1 approved, 2 pending) used by every page, so pending requests actually reach the reporting officer.
- `reporting_officer_date_time` was an integer column holding a datetime, and `reporting_officer_ip_address` an integer holding an IP string. Both are corrected in the schema and in the migration.
- The subnet mask column was an integer, so `255.255.255.0` was stored as `255`. It is now a varchar, and the mismatched `bind_param` type strings in the assignment handler (which also shifted every parameter after it) are corrected.
- Device is a foreign key into `device_master`, but the request form asked the employee to type a raw ID. It is now a dropdown of device names, and the listing pages join to show the name instead of the number.
