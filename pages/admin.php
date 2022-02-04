<?php
	if ( !isset( $_SESSION[ 'radmin' ] ) ) {
		echo "<h3/><span style='color:red;'>For Administrators Only!</span><h3/>";
		exit();
	} else {


		if ( !isset( $_POST[ 'addItemBtn' ] ) ) {
			?>
			<h1 class = "text-center">Item</h1>
			<form action = "index.php?page=4" method = "post" enctype = "multipart/form-data">

				<div class="form-group " style = "margin-bottom: 20px">
					<label for = "catid">Category:</label>
					<select name = "catid">
						<?php

							$pdo = Tools ::connect();
							$list = $pdo -> query( "SELECT * FROM Categories" );

							while ( $row = $list -> fetch() ) {
								echo '<option value="' . $row[ 'id' ] . '">' . $row[ 'category' ] . '</option>';
							}
						?>
					</select>
				</div>

				<div class = "form-group">
					<label for = "name">Name:</label>
					<input type = "text" class = "" name = "name">
				</div>

				<div class = "form-group">
					<label for = "pricein">Incoming Price and Sale Price:</label>
					<div>
						<input type = "number" class = "" name = "pricein">
						<input type = "number" class = "" name = "pricesale">
					</div>
				</div>

				<div class = "form-group">
					<label for = "info">Description:</label>
					<div><textarea class = "" name = "info"></textarea></div>
				</div>

				<div class = "form-group">
					<label for = "imagepath">Select image:</label>
					<input type = "file" class = "" name = "imagepath">
				</div>

				<button type = "submit" class = "btn btn-primary" name = "addItemBtn">Add item</button>

			</form>

			<?php

		} else {

			if ( is_uploaded_file( $_FILES[ 'imagepath' ][ 'tmp_name' ] ) ) {
				$path = "images/" . $_FILES[ 'imagepath' ][ 'name' ];
				move_uploaded_file( $_FILES[ 'imagepath' ][ 'tmp_name' ], $path );
			}

			$catid = $_POST[ 'catid' ];
			$pricein = $_POST[ 'pricein' ];
			$pricesale = $_POST[ 'pricesale' ];
			$name = trim( htmlspecialchars( $_POST[ 'name' ] ) );
			$info = trim( htmlspecialchars( $_POST[ 'info' ] ) );

			$item = new Item( $name, $catid, $pricein, $pricesale, $info, $path );
			$item -> intoDb();
		}

		if ( !isset( $_POST[ 'addCatBtn' ] ) ) {
			?>
			<h1 class = "text-center">Category</h1>
			<form action = "index.php?page=4" method = "post">

				<div class = "form-group">
					<label for = "name">Name:</label>
					<input type = "text" class = "" name = "name">
				</div>

				<button type = "submit" class = "btn btn-primary" name = "addCatBtn">Add category</button>

			</form>

			<?php

		} else {

			$name = trim( htmlspecialchars( $_POST[ 'name' ] ) );

			$category = new Category( $name );
			$category -> intoDb();
		}

		if ( !isset( $_POST[ 'addImgBtn' ] ) ) {
			?>
			<h1 class = "text-center">Image</h1>
			<form action = "index.php?page=4" method = "post" enctype = "multipart/form-data">

				<div class="form-group" style = "margin-bottom: 20px">
					<label for = "itemid">Item:</label>
					<select name = "itemid">
						<?php

							$pdo = Tools ::connect();
							$list = $pdo -> query( "SELECT * FROM items" );

							while ( $row = $list -> fetch() ) {
								echo '<option value="' . $row[ 'id' ] . '">' . $row[ 'itemname' ] . '</option>';
							}
						?>
					</select>
				</div>

				<div class = "form-group">
					<label for = "imagepath">Select image:</label>
					<input type = "file" class = "" name = "imagepath">
				</div>

				<button type = "submit" class = "btn btn-primary" name = "addImgBtn">Add image</button>

			</form>

			<?php

		} else {

			if ( is_uploaded_file( $_FILES[ 'imagepath' ][ 'tmp_name' ] ) ) {
				$path = "images/" . $_FILES[ 'imagepath' ][ 'name' ];
				move_uploaded_file( $_FILES[ 'imagepath' ][ 'tmp_name' ], $path );
			}

			$itemid = $_POST[ 'itemid' ];

			$img = new Image( $itemid ,$path);
			$img -> intoDb();
		}
	}