<?php @session_start();

	if ( isset( $_GET[ 'page' ] ) ) {
		$page = $_GET[ 'page' ];
	}
	include_once( "pages/classes.php" );
?>

<!DOCTYPE html>
<html lang = "en">

<head>
	<meta charset = "UTF-8">
	<meta name = "viewport" content = "width=device-width, initial-scale=1">
	<title>Online Shopping</title>

	<link rel = "stylesheet" href = "css/bootstrap.min.css">
	<link rel = "stylesheet" href = "css/style.css">

</head>

<body>
<div class = "container">

	<div class = "row">
		<header class = "col-sm-12 col-md-12 col-lg-12 head">
			<?php
				include_once( 'pages/login.php' );
			?>
		</header>
	</div>

	<div class = "row">
		<nav class = "col-sm-12 col-md-12 col-lg-12 head">
			<?php
				include_once( 'pages/menu.php' );
			?>
		</nav>
	</div>

	<section class = "main-content">

		<div class = "row">
			<?php
				if ( isset( $_GET[ 'page' ] ) ) {
					if ( $page == 1 ) include_once( "pages/catalog.php" );
					if ( $page == 2 ) include_once( "pages/cart.php" );
					if ( $page == 3 ) include_once( "pages/registration.php" );
					if ( $page == 4 ) include_once( "pages/admin.php" );
				}
			?>
		</div>

	</section>

	<div class = "row">
		<footer style = "clear:both;">
			<p class = "text-center">Step Academy &copy;</p>
		</footer>
	</div>
</div>

<script src = "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src = "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity = "sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin = "anonymous"></script>
<!--<script type = "text/javascript" src = "js/bootstrap.min.js"></script>-->
<script src = "js/script.js"></script>
</body>

</html>
