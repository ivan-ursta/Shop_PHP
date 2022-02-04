<!DOCTYPE html>
<html lang = "en">
<head>
	<meta charset = "UTF-8">
	<title>Hotel Info</title>
	<link href = "/examples/vendors/bootstrap-3.3.7/css/bootstrap.min.css" rel = "stylesheet">
</head>
<body>

<?php
	include_once( 'classes.php' );
	$id = $_GET[ 'name' ];

	$images = Image ::fromDb( $id );
	$items = Item ::GetItems();

	foreach ( $items as $item ) {
		$arrItem = (array)$item;

		if ( $arrItem[ 'id' ] == $id ) {

			$arrImages = (array)$images;
			$item -> DrawForInfo( $arrImages );

			?>

			<?php
		}
	}
?>
<script src = "https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src = "https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity = "sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin = "anonymous"></script>

</html>
