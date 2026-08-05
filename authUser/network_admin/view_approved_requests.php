<?php
require_once(__DIR__ . '/guard.php');
require_once __DIR__ . '/../../config/db.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<title>Bootstrap Application Template | Dashboard</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="stylesheet" href="../../assets/vendor/bootstrap/css/bootstrap.min.css" />
	<!-- Custom styles for this template -->
	<link rel="stylesheet" href="../../assets/css/base.css" />
	<link rel="stylesheet" href="../../assets/css/base-responsive.css" />
	<link rel="stylesheet" href="../../assets/css/animate.min.css" />
	<link rel="stylesheet" href="../../assets/css/slicknav.min.css" />
	<link rel="stylesheet" href="../../assets/css/font-awesome.min.css" />
	<link href="../../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

	<script src="../../assets/vendor/charts/Chart.js"></script>
	<script src="../../assets/vendor/charts/moment.min.js"></script>
	<script src="../../assets/vendor/charts/Chart.min.js"></script>
	<script src="../../assets/vendor/charts/utils.js"></script>
	<style>
		body {
			background-color: #fff;
		}

		.b-leftmenu ul {
			list-style: none;
			margin: 0;
			padding: 0;
		}

		.b-leftmenu ul li {
			/* Sub Menu */
		}

		.b-leftmenu ul li a {
			display: block;
			background: #ebebeb;
			padding: 10px 15px;
			color: #333;
			text-decoration: none;
			-webkit-transition: 0.2s linear;
			-moz-transition: 0.2s linear;
			-ms-transition: 0.2s linear;
			-o-transition: 0.2s linear;
			transition: 0.2s linear;
		}

		.b-leftmenu ul li a:hover {
			background: #f8f8f8;
			color: #515151;
		}

		.b-leftmenu ul li a .fa {
			width: 16px;
			text-align: center;
			margin-right: 5px;
			float: right;
		}

		.b-leftmenu ul ul {
			background-color: #ebebeb;
		}

		.b-leftmenu .sub-menu ul li a {
			background: #f8f8f8;
			border-left: 4px solid transparent;
			padding: 10px 25px;
		}

		.b-leftmenu .sub-sub-menu ul li a {
			padding: 10px 20px 10px 40px;
		}

		.b-leftmenu a.b-newpage:hover {
			background: #ebebeb;
			border-left: 4px solid #3498db;
		}
	</style>
</head>


<body>

	<!-- <nav class="navbar navbar-expand-sm bg-dark navbar-dark sticky-top">
		<a class="navbar-brand" href="#">Logo</a>
		<ul class="navbar-nav">
			<li class="nav-item">
				<a class="nav-link" href="#">Link</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" href="#">Link</a>
			</li>
		</ul>
	</nav> -->



	<div class="d-flex" id="wrapper">

		<!-- Sidebar -->
		<div class="dashboard-bgcolor border-right" id="sidebar-wrapper">
			<div class="sidebar-heading text-center text-white" style="font-size: 24px">
				<span class="fas fa-tachometer-alt"></span> &nbsp;<span class="text-uppercase">Admin</span>
			</div>
			<div class="list-group list-group-flush b-leftmenu">

				<ul>
					<li><a href='dashboard.php'
							class="dashboard-bgcolor text-white border-bottom b-newpage">Dashboard</a></li>
					<li><a href='assign_ip.php' class="dashboard-bgcolor text-white border-bottom b-newpage">Assign IP &
							MAC</a></li>
					<li><a href='view_approved_requests.php'
							class="dashboard-bgcolor text-white border-bottom b-newpage">Approved Requests</a></li>
				</ul>
			</div>
		</div>
		<!-- /#sidebar-wrapper -->

		<!-- Page Content -->
		<div id="page-content-wrapper">

			<nav class="navbar navbar-expand-lg navbar-light dashboard-bgcolor border-bottom">
				<button class="btn text-white" id="menu-toggle">
					<span style="display:none;">Menu</span>
					<span class="fas fa-bars" style="font-size: 1.4rem"></span>
				</button>

				<!-- Online users -->







				<button class="navbar-toggler b-dropmenubtn" type="button" data-toggle="collapse"
					data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
					aria-label="Toggle navigation">
					<span class="far fa-caret-square-down" style="font-size: 30px; color: #FFF"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav ml-auto mt-2 mt-lg-0">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button"
								data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								Profile
							</a>
							<div class="dropdown-menu dropdown-menu-right text-center b-dropmenu-db"
								aria-labelledby="navbarDropdown">
								<a class="dropdown-item" href="../../public/logout.php" data-toggle="modal"
									data-target="#signout-modal">Sign Out</a>

							</div>
						</li>
					</ul>
				</div>
			</nav>
			<!-- /#container -->
			<div class="container mt-5">
				<h3 class="text-center mb-4">Approved Requests</h3>
				<table class="table table-bordered">
					<thead class="thead-dark">
						<tr>
							<th>Request ID</th>
							<th>Emp ID</th>
							<th>IP Address</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>
					<tbody>
						<?php
						require_once __DIR__ . '/../../config/db.php';
						$requests = "
                SELECT 
					a.ip_request_id,
                    a.emp_id,
                    a.ip_address_assigned AS ip_address,
					b.device_id,
                    CASE 
                        WHEN used_unsed = 1 THEN 'In Use'
                        WHEN used_unsed = 0 THEN 'Not In Use'
                        ELSE 'Pending'
                    END AS status
                FROM 
                    ip_transaction AS a JOIN ip_request AS b ON a.ip_request_id = b.id
            ";

						$result = $conn->query($requests);
						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {
								$badgeClass = '';
								if ($row['status'] === 'In Use') {
									$badgeClass = 'badge-success';
								} elseif ($row['status'] === 'Not In Use') {
									$badgeClass = 'badge-danger';
								} else {
									$badgeClass = 'badge-warning';
								}

								$collapseId = "collapseDetails" . $row['ip_request_id'];

								
									echo "<tr>
											<td>{$row['ip_request_id']}</td>
											<td>{$row['emp_id']}</td>
											<td>{$row['ip_address']}</td>
											<td><span class='badge {$badgeClass}'>{$row['status']}</span></td>
											<td><button class='btn btn-sm btn-primary' data-toggle='collapse' data-target='#{$collapseId}'>Details</button></td>
										</tr>";

									echo "<tr class='collapse' id='{$collapseId}'>
											<td colspan='5' class='p-3'>
												<strong>More Info:</strong><br>
												IP: {$row['ip_address']}<br>
												Status: {$row['status']}<br>
												Request ID: {$row['ip_request_id']}<br>
												Device ID: {$row['device_id']}
											</td>
										</tr>";




								

							}
						} else {
							echo "<tr><td colspan='5' class='text-center'>No requests found.</td></tr>";
						}

						$conn->close();
						?>
					</tbody>
				</table>
			</div>


			<!-- /#page-content-wrapper -->

		</div>
		<!-- /#wrapper -->



		<!-- Bootstrap core JavaScript -->
		<script src="../../assets/vendor/jquery/jquery.min.js"></script>
		<script src="../../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
		<script src="../../assets/js/jquery.slicknav.min.js"></script>
		<script src="../../assets/js/dashboard.js"></script>

		<!-- Menu Toggle Script -->
		<script>
			$("#menu-toggle").click(function (e) {
				e.preventDefault();
				$("#wrapper").toggleClass("toggled");
			});
		</script>


		<script>
			$(document).ready(function () {
				$('#backtotop').click(function () {
					$("html, body").animate({ scrollTop: 0 }, 600);
					return false;
				});
			});
		</script>


		<script>
			$('.sub-menu ul').hide();
			$('.sub-sub-menu ul').hide();
			$(".sub-menu a").click(function () {
				$(this).parent(".sub-menu").children("ul").slideToggle("100");
				$(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
			});

			$(".sub-sub-menu a").click(function () {
				$(this).parent(".sub-sub-menu").children("ul").slideToggle("100");
				$(this).find(".right").toggleClass("fa-caret-up fa-caret-down");
			});
		</script>



		<!-- Signup Modal -->
		<div class="modal fade" id="signout-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">

					<!-- Modal Header -->
					<div class="modal-header text-center d-block p-5 border-bottom-0">
						<h3 class="modal-title">Sign Out?</h3>
						<button type="button" class="close position-absolute" style="right: 15px; top: 8px;"
							data-dismiss="modal">&times;</button>
					</div>

					<!-- Modal body -->
					<div class="modal-body">
						<p class="text-center">Are you sure you want to Sign Out?</p>
						<div class="text-center py-4">
							<form action="../../public/logout.php">
								<button type="submit" class="btn btn-primary b-btn mx-3">Sign Out</button>
								<button class="btn btn-secondary mx-3" data-dismiss="modal">Cancel</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>






</body>

</html>