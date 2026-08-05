<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';
include 'header.php';

$emp_id = (int)($_SESSION['emp_id'] ?? 0);
$stmt = $conn->prepare("SELECT r.mac_address, r.ip_address_of_employee, r.ip_purpose,
                               r.action_status_reporting_officer, r.query_reporting_officer,
                               r.datetime_of_employee, d.devie_name AS device_name
                        FROM ip_request r
                        LEFT JOIN device_master d ON d.id = r.device_id
                        WHERE r.emp_id = ? ORDER BY r.datetime_of_employee DESC");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<div class="container" style="padding:20px; overflow:auto;">
<h2>Request Status</h2>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Device</th><th>MAC Address</th><th>Requested IP</th><th>Purpose</th>
      <th>Status</th><th>Remarks</th><th>Requested At</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($result->num_rows === 0): ?>
    <tr><td colspan="7" class="text-center">No requests yet.</td></tr>
  <?php endif; ?>
  <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?= htmlspecialchars($row['device_name'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['mac_address']) ?></td>
      <td><?= htmlspecialchars($row['ip_address_of_employee'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['ip_purpose']) ?></td>
      <td><?= status_badge($row['action_status_reporting_officer']) ?></td>
      <td><?= htmlspecialchars($row['query_reporting_officer'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['datetime_of_employee']) ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>

<?php $stmt->close(); include 'footer.php'; ?>
