<?php
$randomtoken = md5(uniqid(rand(),true));
require_once __DIR__ . '/../config/db.php';
$randomtoken = issue_token();
$csrf=rand(999,99999);
$_SESSION['pkskey']=$csrf;
?>


<!DOCTYPE html>
<html lang="en">
<head>
	<title>Web Application Portal | Homepage</title>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom styles for this template -->
    <link rel="stylesheet" href="../assets/css/base.css" />
    <link rel="stylesheet" href="../assets/css/base-responsive.css" />
    <link rel="stylesheet" href="../assets/css/animate.min.css" />
    <link rel="stylesheet" href="../assets/css/slicknav.min.css" />
    <link rel="stylesheet" href="../assets/css/font-awesome.min.css" />
    <link href="../assets/vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

	<script src="../assets/vendor/jquery/jquery.min.js"></script>
	<script src="../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
	<script src="../assets/js/jquery.slicknav.min.js"></script>
	<script src="../assets/js/sha256.js"></script>
	<script src="../assets/js/popper.min.js"></script>
	<script>
	function encryptpwd(){
		var pwdpolicy = /^(?=.*\d)(?=.*[a-z])(?=.*[!@#$&_])(?=.*[A-Z]).{8,20}$/;
		var pass= jQuery("#pwd").val();
		if(pass.match(pwdpolicy)){
			var x = sha256(pass);
			var y= <?php echo $csrf; ?>;
			var z= x+y;
			var z1= sha256(z);
			jQuery("#pwd").val(z1);
			jQuery(".msg").text('');
		} else {
			var msg = "Password does not meet the policy requirements.";
			jQuery(".msg").text(msg);
			return false;
		}
	}
	</script>
</head>


<body>
	<div class="sr-only">
		<h1>Web Application Portal</h1>
	</div>
	
	<!-- Accessibility -->
	<div class="container d-flex clearfix py-2" id="b-accessibility">
		<div class="b-ministryname">
			<div class="text-right d-inline-block font-weight-bold b-acc-goi my-sm-1 pr-sm-2">
				<span>भारत सरकार</span> <br> <span>GOVERNMENT OF INDIA</span>
			</div>

			<div class="font-weight-bold d-inline-block b-acc-ministry my-sm-1 pl-sm-1">
				<span>मंत्रालय / विभाग नाम</span> <br> <span>MINISTRY / DEPT. NAME</span>
			</div>
		</div>
		
		<div class="ml-auto d-flex b-acc-icons">
			<div class="align-self-center">

				<div class="d-inline-block h-100 px-2">
					<a href="#b-homedb" class="align-self-center b-skiptomain" title="Skip to main content">
						<img src="../assets/images/icons/ico-skip.png" alt="skip to main content icon">
					</a>
				</div>

				<div class="d-inline-block h-100 px-2">
					<img src="../assets/images/icons/ico-accessibility.png" alt="accessibility icon" title="Accessibility Dropdown" class="dropdown-toggle" data-toggle="dropdown" style="cursor: pointer;">

					<div class="dropdown-menu b-accessibility-dropdown" style="min-width: 50px; width: 50px">
				        <a href="javascript:void(0);" class="dropdown-item" title="Increase font size"> <span class="font-weight-bold"> A<sup>+</sup> </span> </a>
				        <a href="javascript:void(0)" class="dropdown-item" title="Reset font size"> <span class="font-weight-bold"> A </span> </a>
				        <a href="javascript:void(0);" class="dropdown-item" title="Decrease font size"> <span class="font-weight-bold"> A<sup>-</sup> </span> </a>
				        <a href="javascript:void(0)" class="dropdown-item bg-dark" title="High contrast"> <span class="font-weight-bold text-white"> A </span> </a>
				    </div>
				</div>

				<div class="d-inline-block h-100 px-2">
					<a href="index.php" title="Sitemap">
						<img src="../assets/images/icons/ico-sitemap.png" alt="sitemap icon">
					</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Header -->
	<div class="container clearfix py-3" id="b-header">
		<div class="float-left d-flex h-100 align-items-center">
			<img src="../assets/images/emblem-dark.png" class="b-emblem-image" title="National Emblem of India" alt="emblem of india logo">
            <div class="pl-3">
                <strong class="d-block" style="font-size: 1.2rem; line-height: 1.2;">वेब एप्लिकेशन पोर्टल</strong>
                <span class="d-block" style="font-size: 1.1rem; font-weight: 500;">Web Application Portal</span>
            </div>
		</div>
		<div class="pull-right">
		<?php 
if(isset($_SESSION['msg'])){
	echo $_SESSION['msg'];
	unset($_SESSION['msg']);
}
		?>
		</div>
	</div>

	<style type="text/css">
		.bar1, .bar2, .bar3 {
		    width: 25px;
		    height: 3px;
		    background-color: #fff;
		    margin: 5px 0;
		    transition: 0.4s;
		}

		.change .bar1 {
		  -webkit-transform: rotate(-45deg) translate(-5px, 5px);
		  transform: rotate(-45deg) translate(-5px, 5px);
		}

		.change .bar2 {opacity: 0;}

		.change .bar3 {
		  -webkit-transform: rotate(45deg) translate(-5px, -7px);
		  transform: rotate(45deg) translate(-5px, -7px);
		}
	</style>

	<!-- Global Navigation -->
	<div class="globalnav-bg shadow-sm">
		<div class="container">
			<nav class="navbar navbar-expand-sm navbar-dark px-0">
				<div class="d-flex w-100 b-nav-mobile">
					<button class="navbar-toggler align-self-center b-btn-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar" onclick="myFunction(this)">
						<span style="display:none;">Menu</span>
						<div>
						  <div class="bar1"></div>
						  <div class="bar2"></div>
						  <div class="bar3"></div>
						</div>
					</button>
					<button class="btn btn-outline-light align-self-center ml-auto b-btn-login" type="button" data-toggle="modal" data-target="#login-modal">
						Log In
					</button> 
				</div>
				
				<div class="collapse navbar-collapse" id="collapsibleNavbar">
					<ul class="navbar-nav main-menu d-flex align-items-center w-100">
						<li class="nav-item d-block"> <a href="index.php" class="nav-link active">Home</a> </li>
						<li class="nav-item d-block"> <a href="index.php" class="nav-link">About Us</a></li>
						<li class="nav-item d-block"> <a href="index.php" class="nav-link">Contact Us</a></li>
						<li class="nav-item d-block ml-auto b-loginbut" data-toggle="modal" data-target="#login-modal">
							<a class="nav-link btn btn-light text-primary px-3 rounded-pill font-weight-bold" href="javascript:void(0);" style="color: var(--primary-color) !important;">Log In</a>
						</li>    
					</ul>
				</div>

			</nav>
		</div>
	</div>

	<script>
function myFunction(x) {
  x.classList.toggle("change");
}
</script>
	<!-- Banner -->

	<div id="demo" class="carousel slide" data-ride="carousel">
		<!-- <ul class="carousel-indicators">
			<li data-target="#demo" data-slide-to="0" class="active"></li>
			<li data-target="#demo" data-slide-to="1"></li>
			<li data-target="#demo" data-slide-to="2"></li>
		</ul> -->
		<div class="carousel-inner">
			<div class="carousel-item active">
				<div style="background: linear-gradient(135deg, #1a2980 0%, #26d0ce 100%); height: 400px; display: flex; align-items: center; justify-content: center; color: white; text-align: center;">
                    <div>
                        <h1 class="display-4 font-weight-bold mb-4">Welcome to the Portal</h1>
                        <p class="lead">Efficiently manage your IP resources and applications.</p>
                    </div>
                </div>
				<!-- <img src="../assets/images/banner1.jpg" alt="banner 1" width="100%"> -->
			</div>
		</div>
		<!-- <a class="carousel-control-prev" href="#demo" data-slide="prev">
			<span style="display:none;">Previous</span>
			<span class="far fa-angle-left" style="font-size:40px; color:#fff"></span>
		</a>
		<a class="carousel-control-next" href="#demo" data-slide="next">
			<span style="display:none;">Next</span>
			<span class="far fa-angle-right" style="font-size:40px; color:#fff"></span>
		</a> -->
	</div>

	<!-- Dashboard -->
	<div class="py-5 bg-light" id="b-homedb">
		<div class="container">
			<div class="row text-center justify-content-center">
				<h2 class="col-md-12 mb-5 font-weight-bold text-dark">Portal Insights</h2>
				<div class="col-lg-4 p-3">
					<div class="bg-white py-5 px-4 b-dbcard h-100 border-0 shadow-sm rounded-lg hover-elevate">
						<div class="mb-4 text-primary">
                            <span class="fa fa-id-card fa-3x"></span>
                        </div>
						<h3 class="h5 font-weight-bold mb-4">Applications Received</h3>
						<div class="text-left w-100 px-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Till date</span>
                                <span class="font-weight-bold text-dark">4.88 Cr</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Current year</span>
                                <span class="font-weight-bold text-dark">23 Lakh</span>
                            </div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 p-3">
					<div class="bg-white py-5 px-4 b-dbcard h-100 border-0 shadow-sm rounded-lg hover-elevate">
						<div class="mb-4 text-success">
                            <span class="fa fa-check-square fa-3x"></span>
                        </div>
						<h3 class="h5 font-weight-bold mb-4">Applications Registered</h3>
						<div class="text-left w-100 px-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Till date</span>
                                <span class="font-weight-bold text-dark">2.23 Cr</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Current year</span>
                                <span class="font-weight-bold text-dark">1.3 Lakh</span>
                            </div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 p-3">
					<div class="bg-white py-5 px-4 b-dbcard h-100 border-0 shadow-sm rounded-lg hover-elevate">
						<div class="mb-4 text-warning">
                            <span class="fas fa-rupee-sign fa-3x"></span>
                        </div>
						<h3 class="h5 font-weight-bold mb-4">Revenue Generated</h3>
						<div class="text-left w-100 px-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Till date</span>
                                <span class="font-weight-bold text-dark">2,234.40 Cr</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Current year</span>
                                <span class="font-weight-bold text-dark">9,478.75 Cr</span>
                            </div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Footer Logos -->
	<div class="footer-carousal py-4 border-top">
		<div class="container">
            <div class="d-flex justify-content-around align-items-center flex-wrap">
			    <div class="f-logo p-2"><img src="../assets/images/carousel/digital-india.png" alt="" class="img-fluid" style="max-height: 50px;"></div>
                <div class="f-logo p-2"><img src="../assets/images/carousel/data-gov.png" alt="" class="img-fluid" style="max-height: 50px;"></div>
                <div class="f-logo p-2"><img src="../assets/images/carousel/india-gov.png" alt="" class="img-fluid" style="max-height: 50px;"></div>
                <div class="f-logo p-2"><img src="../assets/images/carousel/makeinindia.png" alt="" class="img-fluid" style="max-height: 50px;"></div>
            </div>
		</div>
	</div>


	<!----------- Footer ------------>
	<div class="footer-bs bg-dark text-white pt-5 pb-3">
	    <footer class="container">
	        <div class="row">
	        	<div class="col-md-7 col-sm-12 footer-nav mb-4">
	            	<h4 class="mb-4 text-uppercase font-weight-bold" style="letter-spacing: 2px; font-size: 1.1rem;">Quick Links</h4>
                    <div class="row">
                        <div class="col-sm-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><a href="index.php" class="text-light-50 text-decoration-none transition-hover">About Us</a></li>
                                <li class="mb-2"><a href="index.php" class="text-light-50 text-decoration-none transition-hover">Contact Us</a></li>
                                <li class="mb-2"><a href="index.php" class="text-light-50 text-decoration-none transition-hover">Help</a></li>
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <ul class="list-unstyled">
                                <li class="mb-2" data-toggle="modal" data-target="#feedback-modal"><a href="javascript:void(0)" class="text-light-50 text-decoration-none transition-hover">Feedback</a></li>
                                <li class="mb-2"><a href="index.php" class="text-light-50 text-decoration-none transition-hover">Terms & Condition</a></li>
                                <li class="mb-2"><a href="javascript:void(0);" class="text-light-50 text-decoration-none transition-hover">Privacy Policy</a></li>
                            </ul>
                        </div>
                    </div>
	            </div>
	        	<div class="col-md-5 col-sm-12 footer-social d-flex align-items-center justify-content-md-end mb-4">
        			<div class="d-inline-block">
	        			<span class="bg-white p-2 d-inline-block rounded mr-2"><img src="../assets/images/NIC.png" alt="NIC logo" height="30"></span>
	        			<span class="bg-white p-2 d-inline-block rounded"><img src="../assets/images/digital-india.png" alt="digital india logo" height="30"></span>
	        		</div>
	            </div>
	        </div>
            <hr class="border-secondary my-4">
	        <div class="text-center small text-light-50">
	        	Website Content Managed by <a class="text-white font-weight-bold" href="https://www.nic.in/">National Informatics Centre</a>, <a class="text-white font-weight-bold" href="https://meity.gov.in/">Ministry of Electronics and IT</a>, <a class="text-white font-weight-bold" href="https://www.india.gov.in/">Govt. of India.</a>
	        </div>
	    </footer>
	</div>


<div class="modal fade" id="login-modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-lg overflow-hidden">

            <!-- Modal Header -->
            <div class="modal-header text-center d-block p-4 border-bottom-0 bg-primary text-white position-relative">
                <h3 class="modal-title font-weight-bold">Welcome Back</h3>
                <p class="mb-0 text-white-50">Sign in to continue</p>
				<span class="msg d-block mt-2 text-warning font-weight-bold"></span>
                <button type="button" class="close position-absolute text-white" style="right: 15px; top: 15px; opacity: 1;" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body p-4 bg-light">
                <form action="login-exec.php" method="post" id="loginForm">
                    <div class="form-group mb-3">
                        <label for="login-UserType" class="small font-weight-bold text-muted">USER TYPE</label>
                        <select name="usertype" class="form-control form-control-lg border-0 shadow-sm" style="height: auto;" required>
							<option value="">Select User Type</option>
							<option value="1">Reporting Officer</option>
							<option value="2">Network Admin</option>
							<option value="3">Employee</option>
						</select>
                    </div>
					<div class="form-group mb-3">
                        <label for="login-email" class="small font-weight-bold text-muted">EMAIL ADDRESS</label>
                        <input type="email" class="form-control form-control-lg border-0 shadow-sm" id="email" placeholder="name@example.com" name="login-email" minlength="6" maxlength="100" readonly onfocus="this.removeAttribute('readonly');" required>
                    </div>
                    <div class="form-group mb-3">
                        <label for="login-pwd" class="small font-weight-bold text-muted">PASSWORD</label>
                        <input type="password" class="form-control form-control-lg border-0 shadow-sm" id="pwd" placeholder="Enter your password" name="login-pwd" minlength="8" maxlength="20" readonly onfocus="this.removeAttribute('readonly');" required>
                    </div>
                    
                    <div class="form-group mb-3 bg-white p-3 rounded shadow-sm border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                             <label class="small font-weight-bold text-muted mb-0">CAPTCHA</label>
                             <img src="captcha.php" alt="CAPTCHA Image" style="border-radius: 4px;">
                        </div>
                        <input type="text" class="form-control border-secondary" id="captcha_input" name="captcha_input" placeholder="Enter characters shown above" readonly onfocus="this.removeAttribute('readonly');" required>
                    </div>

                    <div class="form-group d-flex justify-content-between align-items-center mb-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="login-rem" name="remember">
                            <label class="custom-control-label small" for="login-rem">Remember me</label>
                        </div>
						<div class="small">
                            <a href="" data-toggle="modal" data-target="#signup-modal" data-dismiss="modal" class="text-primary font-weight-bold">Create Account</a>
                        </div>
                    </div>
                    
                    <div class="text-center">
					    <?php $_SESSION['token'] = $randomtoken; ?>
					    <input type="hidden" name="token" value="<?php echo $_SESSION['token']; ?>" required>
                        <button type="submit" name="submit" class="btn btn-primary btn-block btn-lg shadow-sm b-btn submit font-weight-bold" onClick="encryptpwd();" style="border-radius: 50px;">Log In</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

	<!-- Signup Modal -->
	<div class="modal fade" id="signup-modal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow-lg rounded-lg">

				<!-- Modal Header -->
				<div class="modal-header text-center d-block p-4 border-bottom-0 bg-primary text-white position-relative">
					<h3 class="modal-title font-weight-bold">Create Account</h3>
                    <p class="mb-0 text-white-50">Join our portal today</p>
					<button type="button" class="close position-absolute text-white" style="right: 15px; top: 15px; opacity: 1;" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body p-4 bg-light">
					<form action="" autocomplete="off">
						<div class="form-group">
							<label for="signup-name" class="small font-weight-bold text-muted">FULL NAME</label>
							<input type="text" class="form-control border-0 shadow-sm" id="signup-name" placeholder="John Doe" name="signup-name">
						</div>
						<div class="form-group">
							<label for="signup-email" class="small font-weight-bold text-muted">EMAIL ADDRESS</label>
							<input type="email" class="form-control border-0 shadow-sm" id="signup-email" placeholder="name@example.com" name="signup-email">
						</div>
						<div class="form-group">
							<label for="signup-mobile" class="small font-weight-bold text-muted">MOBILE NUMBER</label>
							<input type="number" class="form-control border-0 shadow-sm" id="signup-mobile" placeholder="10-digit number" name="signup-mobile">
						</div>
						<div class="form-group mb-4">
							<label for="signup-pwd" class="small font-weight-bold text-muted">PASSWORD</label>
							<input type="password" class="form-control border-0 shadow-sm" id="signup-pwd" placeholder="Choose a strong password" name="signup-pwd">
						</div>

						<p class="text-center small mb-4">Already have an account? <a href="" data-toggle="modal" data-target="#login-modal" data-dismiss="modal" class="font-weight-bold text-primary">Log In</a></p>
						<div class="text-center">
							<button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm b-btn font-weight-bold" style="border-radius: 50px;">Sign Up</button>
						</div>
						
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Feedback Modal -->
	<div class="modal fade" id="feedback-modal">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content border-0 shadow-lg rounded-lg">

				<!-- Modal Header -->
				<div class="modal-header text-center d-block p-4 border-bottom-0 bg-primary text-white position-relative">
					<h3 class="modal-title font-weight-bold">Feedback</h3>
                    <p class="mb-0 text-white-50">We value your input</p>
					<button type="button" class="close position-absolute text-white" style="right: 15px; top: 15px; opacity: 1;" data-dismiss="modal">&times;</button>
				</div>

				<!-- Modal body -->
				<div class="modal-body p-4 bg-light">
					<form action="" autocomplete="off">
						<div class="form-group">
							<label for="fdbk-name" class="small font-weight-bold text-muted">NAME</label>
							<input type="text" class="form-control border-0 shadow-sm" id="fdbk-name" placeholder="Your name" name="fdbk-name">
						</div>
						<div class="form-group">
							<label for="fdbk-email" class="small font-weight-bold text-muted">EMAIL</label>
							<input type="email" class="form-control border-0 shadow-sm" id="fdbk-email" placeholder="Your email" name="fdbk-email">
						</div>
						<div class="form-group">
							<label for="fdbk-subject" class="small font-weight-bold text-muted">SUBJECT</label>
							<select class="form-control border-0 shadow-sm" id="fdbk-subject" name="fdbk-subject">
								<option>Application issue</option>
								<option>Design issue</option>
								<option>Any other</option>
							</select>
						</div>
						<div class="form-group mb-4">
							<label for="fdbk-comment" class="small font-weight-bold text-muted">COMMENT</label>
							<textarea class="form-control border-0 shadow-sm" id="fdbk-comment" placeholder="Tell us what you think..." name="fdbk-comment" rows="5" style="resize: none;"></textarea>
						</div>

						<div class="text-center">
							<button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm b-btn font-weight-bold" style="border-radius: 50px;">Submit Feedback</button>
						</div>
						
					</form>
				</div>


			</div>
		</div>
	</div>

  <!-- Menu Toggle Script -->
  <script>
  $(document).ready(function() {

    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });

    $(".b-navdropdown-click").click(function() {
        if ($(".b-navdropdown").hasClass("hide")) {
            $(".b-navdropdown").addClass("show");
            $(".b-navdropdown").removeClass("hide");     
        } 
        else if ($(".b-navdropdown").hasClass("show")) {
            $(".b-navdropdown").addClass("hide");
            $(".b-navdropdown").removeClass("show");
        }
    });
});
    </script>
</body>
</html>

























