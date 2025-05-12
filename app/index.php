<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Azienda ospedaliera</title>
    <link rel="stylesheet" type="text/css" href="./css/style.css">
</head>
<body>
<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-md-5">
                <div class="header-left">
                    <div class="logo">
                        <a href="index.html"><img src="images/logo.png" alt=""/></a>
                    </div>
                    <div class="menu">
                        <a class="toggleMenu" href="#"><img src="images/nav.png" alt="" /></a>
                        <ul class="nav" id="nav">
                            <li><a href="index.html">Home</a></li>
                            <li><a href="about.html">About</a></li>
                            <li><a href="services.html">Services</a></li>
                            <li><a href="blog.html">Blog</a></li>
                            <li><a href="contact.html">Contact</a></li>
                        </ul>
                        <script type="text/javascript" src="js/responsive-nav.js"></script>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="logo-top">
                    <h6>MilliPW</h6>
                </div>
            </div>
            <div class="col-md-4">
                <div class="header_right">
                    <ul class="icon1 sub-icon1">
                        <li><a href="login.html">Login</a></li>
                        <li><a href="register.html">Register</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php	
	include 'nav.html';
	include 'footer.html';
?>
		<div id="content">     
			...         	
		</div>
</body>
</html>