<?php
require_once(__DIR__ . '/guard.php');
include 'header.php';
?>

<style>
.dashboard {
    background-color: #f5f8fc;
    padding: 40px 20px;
    min-height: 80vh;
}

.dashboard h2 {
    color: #003366;
    text-align: center;
    margin-bottom: 40px;
    font-weight: 700;
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 30px;
    max-width: 1000px;
    margin: 0 auto;
}

.card {
    background: #ffffff;
    border-left: 6px solid #ff9933;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
    text-align: center;
    transition: all 0.3s ease;
    text-decoration: none;
    color: #003366;
    font-weight: 600;
}

.card:hover {
    background-color: #e6f0ff;
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15);
}

.card-icon {
    font-size: 40px;
    margin-bottom: 15px;
    color: #0066cc;
}

.card-text {
    font-size: 18px;
}

.card.logout {
    border-left-color: #cc0000;
}

.card.logout .card-icon {
    color: #cc0000;
}
</style>

<section class="dashboard">
  <h2>👨‍💼 Welcome, <?= $_SESSION['employee_name'] ?? 'Employee'; ?></h2>
  <?php
if (isset($_SESSION['form_errors'])) {
    echo '<div style="color:red;">' . $_SESSION['form_errors'] . '</div>';
    unset($_SESSION['form_errors']); // clear after showing
}
?>

  <div class="dashboard-grid">
    <a href="request_ip.php" class="card">
      <div class="card-icon">📝</div>
      <div class="card-text">Request New IP</div>
    </a>

    <a href="view_request_status.php" class="card">
      <div class="card-icon">📄</div>
      <div class="card-text">Check Request Status</div>
    </a>

    <a href="view_assigned_ip.php" class="card">
      <div class="card-icon">💻</div>
      <div class="card-text">View Assigned IP</div>
    </a>

    <a href="../../public/logout.php" class="card logout">
      <div class="card-icon">🚪</div>
      <div class="card-text">Logout</div>
    </a>
  </div>
</section>

<?php include 'footer.php'; ?>
