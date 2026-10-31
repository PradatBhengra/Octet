<?php
require_once __DIR__ . '/../../config/db.php';
require_once('guard.php');

$reporting_officer_id = (int)$_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM ip_request
                        WHERE reporting_officer_id = ?
                          AND action_status_reporting_officer = " . STATUS_PENDING);
$stmt->bind_param("i", $reporting_officer_id);
$stmt->execute();
$result = $stmt->get_result();
$randomtoken = issue_token();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View IP Requests</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- BOOTSTRAP CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/view_request.css">

</head>
<body>
<header class="container-fluid shadow">
    <div class="container">
        <div class="row align-items-center">
            <figure class="col-lg-6 mb-0">
                <img src="../../assets/images/NIC.png" alt="NIC Logo" style="height: 50px;">
            </figure>
            <!-- <div class="col-lg-6 d-flex justify-content-end gap-3">
                <button class="btn"><i class="fa-solid fa-user"></i></button>
                <button class="btn"><i class="fa-solid fa-bell"></i></button>
            </div> -->
        </div>
    </div>
</header>

<div class="d-flex" id="wrapper">
    <div id="sidebar-wrapper">
        <div class="sidebar-heading text-center">
            <i class="fas fa-tachometer-alt"></i> REPORTING OFFICER
        </div>
        <ul>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="view_request.php">View Requests</a></li>
            <li><a href="review_requests.php">Review Requests</a></li>
        </ul>
    </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light border-bottom mb-4">
        <div class="container-fluid">
            <button class="btn" id="menu-toggle">
            <i class="fas fa-bars"></i>
            </button>
                    <div class="ms-auto d-flex gap-2 align-items-center">

  <!-- User Dropdown -->
  <div class="dropdown">
    <button class="btn btn-outline-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="fa-solid fa-user"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
      <!-- <li><a class="dropdown-item" href="#">Profile</a></li>
      <li><a class="dropdown-item" href="#">Settings</a></li> -->
      <li><hr class="dropdown-divider"></li>
      <li><a class="dropdown-item" href="../../public/logout.php">Logout</a></li>
    </ul>
  </div>

  <!-- Bell Icon (Notifications) -->
  <button class="btn btn-outline-light"><i class="fa-solid fa-bell"></i></button>
</div>
            <!-- <div class="ms-auto d-flex gap-2">
            <button class="btn btn-outline-light"><i class="fa-solid fa-user"></i></button>
            <button class="btn btn-outline-light"><i class="fa-solid fa-bell"></i></button>
            </div> -->
        </div>
        </nav>

        <div class="container-fluid">
            <h4 class="mb-4">IP Requests</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Request_ID</th>
                            <th>Emp ID</th>
                            <th>Device ID</th>
                            <th>MAC</th>
                            <th>IP</th>
                            <th>Datetime</th>
                            <th>Purpose</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)) {
                            $rowClass = '';
                            if ((int)$row['action_status_reporting_officer'] === STATUS_APPROVED) {
                                $rowClass = 'approved';
                            } elseif ((int)$row['action_status_reporting_officer'] === STATUS_REJECTED) {
                                $rowClass = 'rejected';
                            }
                        ?>
                        <tr class="<?= $rowClass ?>">
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['emp_id']) ?></td>
                            <td><?= htmlspecialchars($row['device_id']) ?></td>
                            <td><?= htmlspecialchars($row['mac_address']) ?></td>
                            <td><?= htmlspecialchars($row['ip_address_of_employee']) ?></td>
                            <td><?= htmlspecialchars($row['datetime_of_employee']) ?></td>
                            <td><?= htmlspecialchars($row['ip_purpose']) ?></td>
                            <td>
                            <!-- Approve Form -->
                            <form method="POST" action="submit_action.php" class="d-inline">
                                <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                <input type="hidden" name="status" value="1"> <!-- 1 for Approved -->
                                <input type="hidden" name="token" value="<?= $randomtoken ?>">
                                <button type="submit" class="btn btn-success btn-sm">Approve</button>
                            </form>

                            <!-- Reject Form with Toggle Textarea -->
                            <button type="button" class="btn btn-danger btn-sm" onclick="toggleRejectForm(<?= $row['id'] ?>)">Reject</button>

                            <!-- Hidden reject form -->
                            <div id="reject-form-<?= $row['id'] ?>" style="display:none; margin-top:5px;">
                                <form method="POST" action="submit_action.php">
                                    <input type="hidden" name="request_id" value="<?= $row['id'] ?>">
                                    <input type="hidden" name="status" value="0"> <!-- 0 for Rejected -->
                                    <input type="hidden" name="token" value="<?= $randomtoken ?>">
                                    <textarea name="query_reporting_officer" class="form-control mb-2" rows="2" maxlength="200" placeholder="Enter rejection remarks..." required></textarea>
                                    <button type="submit" class="btn btn-warning btn-sm">Submit Rejection</button>
                                </form>
                            </div>
                            </td>
                        </tr>
                        <?php } ?>
                        <?php if (mysqli_num_rows($result) === 0): ?>
                            <tr><td colspan="8">No requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {
        $('#menu-toggle').click(function (e) {
            e.preventDefault();
            $('#wrapper').toggleClass('toggled');
        });

        $('.action-form').each(function() {
            const form = this;
            const select = form.querySelector('.action-select');
            const remarkInput = form.querySelector('.remark-input');

            select.addEventListener('change', function () {
                if (this.value === 'Rejected') {
                    remarkInput.style.display = 'block';
                    remarkInput.required = true;
                } else {
                    remarkInput.style.display = 'none';
                    remarkInput.required = false;
                    remarkInput.value = '';
                }
            });

            form.addEventListener('submit', function(e) {
                const action = select.value;
                const query = remarkInput.value;

                if (!action) {
                    alert("Please select an action.");
                    e.preventDefault();
                    return;
                }

                if (action === 'Rejected' && query.trim() === '') {
                    alert("Please provide a remark for rejection.");
                    e.preventDefault();
                    return;
                }

                if (!confirm(`Are you sure you want to ${action} this request?`)) {
                    e.preventDefault();
                    return;
                }
            });
        });
    });
</script>
<script>
function toggleRejectForm(id) {
    const form = document.getElementById(`reject-form-${id}`);
    form.style.display = (form.style.display === 'none') ? 'block' : 'none';
}
</script>
<!-- <script>
    $(document).ready(function () {
        $('#menu-toggle').click(function (e) {
            e.preventDefault();
            $('#wrapper').toggleClass('toggled');
        });
    });
</script> -->

</body>
</html>
