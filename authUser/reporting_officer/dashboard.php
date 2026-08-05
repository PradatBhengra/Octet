<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';

// Total employees
$employee_query = "SELECT COUNT(*) AS total_employees FROM employee";
$employee_result = $conn->query($employee_query);
$employee_data = $employee_result->fetch_assoc();
$total_employees = $employee_data['total_employees'];

// Total assigned IPs
$assigned_query = "SELECT COUNT(*) AS total_assigned FROM ip_request";
$assigned_result = $conn->query($assigned_query);
$assigned_data = $assigned_result->fetch_assoc();
$total_assigned = $assigned_data['total_assigned'];

// Total available IPs
$available_query = "SELECT COUNT(*) AS total_available FROM ip_transaction";
$available_result = $conn->query($available_query);
$available_data = $available_result->fetch_assoc();
$total_available = $available_data['total_available'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reporting Officer Dashboard</title>

  <!-- Bootstrap & Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter&family=Nunito&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../../assets/css/dashboard.css">

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
<!-- wrapper class -->
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
        </div>
        </nav>

    <!-- Report Section -->
	 <div class="container mt-4">
    <h4 class="mb-4">Dashboard</h4>
  <div class="row g-4">

    <!-- Total Employees -->
    <div class="col-md-4">
      <div class="card text-white bg-primary h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa-solid fa-users"></i> Total Employees</h5>
          <p class="card-text"><?= $total_employees ?></p>
        </div>
      </div>
    </div>

    <!-- IPs Assigned -->
    <div class="col-md-4">
      <div class="card text-white bg-success h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa-solid fa-network-wired"></i> IP Requests</h5>
          <p class="card-text"><?= $total_assigned ?></p>
        </div>
      </div>
    </div>

    <!-- IPs Available -->
    <div class="col-md-4">
      <div class="card text-white bg-warning h-100">
        <div class="card-body">
          <h5 class="card-title"><i class="fa-solid fa-globe"></i> IPs Assigned</h5>
          <p class="card-text"><?= $total_available ?></p>
        </div>
      </div>
    </div>

  </div>
</div>
 
   </div> <!-- end #page-content-wrapper -->

</div> <!-- end #wrapper -->

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  $(document).ready(function () {
    $("#menu-toggle").click(function (e) {
      e.preventDefault();
      $("#wrapper").toggleClass("toggled");
    });
  });
</script>

</body>
</html>
