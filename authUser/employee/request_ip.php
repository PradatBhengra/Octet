<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';
$randomtoken = issue_token();

include 'header.php';

?>
<h2>IP Request Form</h2>
<?php
if (isset($_SESSION['form_errors'])) {
    echo '<div style="color:red;">' . $_SESSION['form_errors'] . '</div>';
    unset($_SESSION['form_errors']); // clear after showing
}
?>

<form action="request_ip_exec.php" method="POST" class="site-form">
  <label>Device:
    <select name="device_id" required>
      <option value="">-- select a device --</option>
      <?php
      $devices = $conn->query("SELECT id, devie_name FROM device_master WHERE status = 1 ORDER BY devie_name");
      while ($d = $devices->fetch_assoc()) {
          echo '<option value="' . (int)$d['id'] . '">' . htmlspecialchars($d['devie_name']) . '</option>';
      }
      ?>
    </select>
  </label>
  <label>MAC Address: <input type="text" name="mac_address" required></label>
  <label>Requested IP (optional): <input type="text" name="ip_address_of_employee"></label>
  <label>Purpose: <textarea name="ip_purpose" maxlength="200" required></textarea></label>
  <!--<button name="submit_request">Submit Request</button>-->
  <input type="submit" name="submit_request" value="Submit Request">

  <?php  $_SESSION['token'] = $randomtoken; ?>
					<input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" required>
                        
                    
</form>

<?php include 'footer.php'; ?>
