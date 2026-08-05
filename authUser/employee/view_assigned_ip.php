<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';
include 'header.php';

$emp_id = (int)($_SESSION['emp_id'] ?? 0);
$stmt = $conn->prepare("SELECT t.ip_address_assigned, t.subnet_mask, t.gateway, t.dns1, t.dns2,
                               t.date_time, t.given_by_email, d.devie_name AS device_name, r.mac_address
                        FROM ip_transaction t
                        JOIN ip_request r ON t.ip_request_id = r.id
                        LEFT JOIN device_master d ON d.id = r.device_id
                        WHERE t.emp_id = ?
                        ORDER BY t.date_time DESC");
$stmt->bind_param("i", $emp_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container" style="padding:20px; overflow:auto;">
<h2>My Assigned IPs</h2>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Device</th><th>MAC Address</th><th>Assigned IP</th><th>Subnet Mask</th>
      <th>Gateway</th><th>DNS 1</th><th>DNS 2</th><th>Assigned By</th><th>Assigned At</th>
    </tr>
  </thead>
  <tbody>
  <?php if ($result->num_rows === 0): ?>
    <tr><td colspan="9" class="text-center">No IPs assigned yet.</td></tr>
  <?php endif; ?>
  <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
      <td><?= htmlspecialchars($row['device_name'] ?? '-') ?></td>
      <td><?= htmlspecialchars($row['mac_address']) ?></td>
      <td><?= htmlspecialchars($row['ip_address_assigned']) ?></td>
      <td><?= htmlspecialchars($row['subnet_mask']) ?></td>
      <td><?= htmlspecialchars($row['gateway']) ?></td>
      <td><?= htmlspecialchars($row['dns1']) ?></td>
      <td><?= htmlspecialchars($row['dns2']) ?></td>
      <td><?= htmlspecialchars($row['given_by_email']) ?></td>
      <td><?= htmlspecialchars($row['date_time']) ?></td>
    </tr>
  <?php } ?>
  </tbody>
</table>
</div>

<?php $stmt->close(); include 'footer.php'; ?>
