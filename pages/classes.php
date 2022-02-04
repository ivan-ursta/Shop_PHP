<?php

	class Tools
	{
		static function connect( $host = "localhost", $user = "root", $pass1 = "12345", $dbname = "shop" )
		{
			$cs = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8;';
			$options = array(
					PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
					PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
			);
			try {
				$pdo = new PDO( $cs, $user, $pass1, $options );
				return $pdo;
			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}

		static function register( $name, $pass1, $imagepath )
		{
			$name = trim( $name );
			$pass1 = trim( $pass1 );
			$imagepath = trim( $imagepath );

			if ( $name == "" || $pass1 == "" ) {
				echo "<h3/><span style='color:red;'>Fill All Required Fields!</span><h3/>";
				return false;
			}

			if ( strlen( $name ) < 3 || strlen( $name ) > 30 || strlen( $pass1 ) < 3 || strlen( $pass1 ) > 30 ) {
				echo "<h3/><span style='color:red;'>Values Length Must Be Between 3 And 30!</span><h3/>";
				return false;
			}

			Tools ::connect();
			$customer = new Customer( $name, md5( $pass1 ), $imagepath );
			$err = $customer -> intoDb();

			if ( $err ) {
				if ( $err == 1062 )
					echo "<h3/><span style='color:red;'>This Login Is Already Taken!</span><h3/>";
				else
					echo "<h3/><span style='color:red;'> Error code:" . $err . "!</span><h3/>";
				return false;
			}
			return true;
		}

		static function login( $name, $pass )
		{
			$name = trim( htmlspecialchars( $name ) );
			$pass = trim( htmlspecialchars( $pass ) );

			if ( $name == "" || $pass == "" ) {

				echo "<h3/><span style='color:red;'>Fill All Required Fields!</span><h3/>";
				return false;

			}

			if ( strlen( $name ) < 3 || strlen( $name ) > 30 || strlen( $pass ) < 3 || strlen( $pass ) > 30 ) {

				echo "<h3/><span style='color:red;'>Value Length Must Be Between 3 And 30!</span><h3/>";
				return false;

			}

			$pdo = Tools ::connect();
			$list = $pdo -> query( 'select * from customers where login="' . $name . '" and pass1="' . md5( $pass ) . '"' );

			if ( $row = $list -> fetch( PDO::FETCH_NUM ) ) {

				$_SESSION[ 'ruser' ] = $name;

				if ( $row[ 3 ] == 1 ) {
					echo $row[ 3 ];
					$_SESSION[ 'radmin' ] = $name;
				}

				return true;

			} else {

				echo "<h3/><span style='color:red;'>No Such User!</span><h3/>";
				return false;

			}
		}
	}

	class Customer
	{
		public $id;
		public $login;
		public $pass1;
		public $roleid;
		public $discount;
		public $total;
		public $imagepath;

		function __construct( $login, $pass1, $imagepath, $id = 0 )
		{
			$this -> login = $login;
			$this -> pass1 = $pass1;
			$this -> imagepath = $imagepath;
			$this -> id = $id;
			$this -> total = 0;
			$this -> discount = 0;
			$this -> roleid = 2;
		}

		function intoDb()
		{

			try {

				$pdo = Tools ::connect();

				$ar = (array)$this;//преобразование объекта класса в массив
				array_shift( $ar ); //убрать из массива первый элемент, содержащий значение свойства $id

//              якщо властивості protected

//				$array = array(
//					'login' => $this -> login,
//					'pass1' => $this -> pass1,
//					'roleid' => $this -> roleid,
//					'discount' => $this -> discount,
//					'total' => $this -> total,
//					'imagepath' => $this -> imagepath
//				);

				$sql = "INSERT INTO customers (login, pass1, roleid, discount, total, imagepath) VALUES (:login, :pass1, :roleid, :discount, :total, :imagepath)";
				$ps = $pdo -> prepare( $sql );
				echo '</br>';
				$ps -> execute( $ar );


			} catch ( PDOException $e ) {

				$err = $e -> getMessage();
				if ( substr( $err, 0, strrpos( $err, ":" ) ) == 'SQLSTATE[23000]:Integrity constraint violation' ) return 1062;
				else return $e -> getMessage();

			}
		}

		static function fromDb( $id )
		{
			$customer = null;
			try {
				$pdo = Tools ::connect();
				$ps = $pdo -> prepare( ( "SELECT * FROM Customers WHERE id=?" ) );
				$res = $ps -> execute( array( $id ) );
				$row = $res -> fetch();
				$customer = new Customer( $row[ 'login' ], $row[ 'pass' ], $row[ 'imagepath' ], $row[ 'id' ] );
				return $customer;

			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}
	}

	class Item
	{
		public $id, $itemname, $catid, $pricein, $pricesale, $info, $rate, $imagepath, $action;

		function __construct( $itemname, $catid, $pricein, $pricesale, $info, $imagepath, $rate = 0, $action = 0, $id = 0 )
		{
			$this -> id = $id;
			$this -> itemname = $itemname;
			$this -> catid = $catid;
			$this -> pricein = $pricein;
			$this -> pricesale = $pricesale;
			$this -> info = $info;
			$this -> rate = $rate;
			$this -> imagepath = $imagepath;
			$this -> action = $action;
		}

		function intoDb()
		{
			try {

				$pdo = Tools ::connect();
				$ps = $pdo -> prepare( "INSERT INTO Items (itemname, catid, pricein, pricesale, info, rate, imagepath, action)
 				VALUES (:itemname, :catid, :pricein, :pricesale, :info, :rate, :imagepath, :action)" );

				$ar = (array)$this;
				array_shift( $ar );
				$ps -> execute( $ar );

			} catch ( PDOException $e ) {

				return $e -> getMessage();

			}
		}

		static function fromDb( $id )
		{
			$item = null;
			try {
				$pdo = Tools ::connect();
				$ps = $pdo -> prepare( "SELECT * FROM Items WHERE id=?" );

				$ps -> execute( array( $id ) );
				$row = $ps -> fetch();

				$item = new Item( $row[ 'itemname' ], $row[ 'catid' ], $row[ 'pricein' ],
						$row[ 'pricesale' ], $row[ 'info' ], $row[ 'imagepath' ],
						$row[ 'rate' ], $row[ 'action' ], $row[ 'id' ] );
				return $item;

			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}

		static function GetItems( $catid = 0 )
		{
			$ps = null;
			$items = null;
			try {
				$pdo = Tools ::connect();

				if ( $catid == 0 ) {
					$ps = $pdo -> prepare( 'select * from items' );
					$ps -> execute();
				} else {
					$ps = $pdo -> prepare
					( 'select * from items where catid=?' );
					$ps -> execute( array( $catid ) );
				}

				while ( $row = $ps -> fetch() ) {
					$item = new Item( $row[ 'itemname' ], $row[ 'catid' ], $row[ 'pricein' ], $row[ 'pricesale' ],
							$row[ 'info' ], $row[ 'imagepath' ], $row[ 'rate' ], $row[ 'action' ], $row[ 'id' ] );
					$items[] = $item;
				}

				return $items;
			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}

		function Draw()
		{
			?>

			<div class = "col-lg-4 col-sm-6 mb-3">

				<div class = "product-card">

					<div class = "product-thumb">

						<span class = 'pull-right' style = 'margin-right:20px;'>
							<?php echo $this -> rate ?> &nbsp;rate
						</span>

						<a href = "#"><img src = "<?php echo $this -> imagepath ?>" alt = "" style = "width: 200px; height: 200px"></a>
					</div>

					<div class = "product-details">

						<h4>
							<a href = 'index.php?name=<?php echo $this -> id ?>'><?php echo $this -> itemname ?></a>
						</h4>

						<p><?php echo $this -> info ?></p>

						<div class = "product-bottom-details d-flex justify-content-between">

							<div class = "product-price">

								$<?php echo $this -> pricesale ?>

							</div>

							<div class = "product-links">

								<?php
									$ruser = '';

									if ( !isset( $_SESSION[ 'reg' ] ) || $_SESSION[ 'reg' ] == "" ) {

										$ruser = "cart_" . $this -> id;

									} else {

										$ruser = $_SESSION[ 'reg' ] . "_" . $this -> id;

									}
									echo "<button class='btn btn-success col-xs-3 col-lg-3' onclick=createCookie('" . $ruser . "','" . $this -> id . "')>To Cart</button>";
								?>

							</div>
						</div>
					</div>
				</div>
			</div>


			<?php
		}

		function DrawForCart()
		{
			?>
			<div class = 'row' style = 'margin:2px'>

				<img src = " <?php echo $this -> imagepath ?>" width = '70px' class = 'col-sm-1 col-md-1 col-lg-1'/>

				<span style = 'margin-right:10px; background-color:#ddeeaa; color:blue; font-size:16pt' class = 'col-sm-3 col-md-3 col-lg-3'>
			<?php echo $this -> itemname ?></span>

				<span style = 'margin-left:10px; color:red; font-size:16pt; background-color:#ddeeaa;' class = 'col-sm-2 col-md-2 col-lg-2'>
			$&nbsp; <?php echo $this -> pricesale ?></span>

				<?php
					$ruser = '';

					if ( !isset( $_SESSION[ 'reg' ] ) || $_SESSION[ 'reg' ] == "" ) {

						$ruser = "cart_" . $this -> id;

					} else {

						$ruser = $_SESSION[ 'reg' ] . "_" . $this -> id;

					}
				?>

				<button class = 'btn btn-sm btn-danger' style = 'margin-left:10px; ' onclick = eraseCookie('<?php echo $ruser ?>')>
					x
				</button>

			</div>
			<?php
		}


		function DrawForInfo($images)
		{
			?>
			<div class = 'row' style = 'display: flex; flex-direction: column; margin:2px;'>

				<span style = 'background-color:#ddeeaa; color:blue; font-size:16pt' class = 'col-sm-3 col-md-3 col-lg-3'><?php echo $this -> itemname ?></span>
				<div style = "display: flex; ">
<!--					<img src = "--><?php //echo $this -> imagepath ?><!--" class = 'col-sm-4 col-md-4 col-lg-6'/>-->
					<div class = "container text-center">

						<div id = "carousel" class = "carousel slide" data-ride = "carousel" style = "display: inline-block;">
							<div class = "carousel-inner">
								<div class = "item active">
									<img src = "<?php echo $this -> imagepath ?>" style="height: 30em; width: 30em" alt = "...">
								</div>
								<?php
									foreach ($images as $img)
									{

										echo "<div class = 'item'>";
										echo "<img src = '" . $img->imagepath. "' style='height: 30em; width: 30em' alt = '...'>";
										echo "</div>";
									}

								?>
							</div>

							<!-- Элементы управления -->
							<a class = "left carousel-control" href = "#carousel" role = "button" data-slide = "prev">
								<span class = "glyphicon glyphicon-chevron-left" aria-hidden = "true"></span>
								<span class = "sr-only">Предыдущий</span>
							</a>
							<a class = "right carousel-control" href = "#carousel" role = "button" data-slide = "next">
								<span class = "glyphicon glyphicon-chevron-right" aria-hidden = "true"></span>
								<span class = "sr-only">Следующий</span>
							</a>

						</div>

					</div>

					<span style = 'margin-left:10px; margin-top: 100px; color:red; height: 1.5em; font-size:16pt; background-color:#ddeeaa;' class = 'col-sm-2 col-md-2 col-lg-2'><?php echo "$&nbsp;" . $this -> pricesale ?></span>

				</div>

				<p style = 'font-size:16pt; background-color:#ddeeaa;' class = ''><?php echo $this -> info ?></p>

				<?php
					$ruser = '';

					if ( !isset( $_SESSION[ 'reg' ] ) || $_SESSION[ 'reg' ] == "" ) {

						$ruser = "cart_" . $this -> id;

					} else {

						$ruser = $_SESSION[ 'reg' ] . "_" . $this -> id;

					}
				?>

			</div>
			<?php
		}


		function Sale()
		{
			try {
				$pdo = Tools ::connect();
				$ruser = 'cart';

				if ( isset( $_SESSION[ 'reg' ] ) && $_SESSION[ 'reg' ] != "" ) {
					$ruser = $_SESSION[ 'reg' ];
				}

//Incresing total field for Customer
				$sql = "UPDATE Customers SET total=total+ ? WHERE login = ?";
				$ps = $pdo -> prepare( $sql );
				$ps -> execute( array( $this -> pricesale, $ruser ) );

//Inserting info about sold item into table Sales
				$ins = "insert into Sales(customername, itemname, pricein, pricesale, datesale)values(?,?,?,?,?)";
				$ps = $pdo -> prepare( $ins );
				$ps -> execute( array( $ruser, $this -> itemname, $this -> pricein, $this -> pricesale, @date( "Y/m/d H:i:s" ) ) );

//deleting item from Items table
				$del = "DELETE FROM Items WHERE id = ?";
				$ps = $pdo -> prepare( $del );
				$ps -> execute( array( $this -> id ) );

			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}
	}

	class Category
	{
		public $id;
		public $category;

		function __construct( $category, $id = 0 )
		{
			$this -> category = $category;
			$this -> id = $id;
		}

		function intoDb()
		{
			try {
				$pdo = Tools ::connect();

				$arr = (array)$this;
				array_shift( $arr );

				$sql = "INSERT INTO categories(category) VALUES (:category)";
				$ps = $pdo -> prepare( $sql );
				$ps -> execute( $arr );
			} catch ( PDOException $e ) {

				$err = $e -> getMessage();
				if ( substr( $err, 0, strrpos( $err, ":" ) ) == 'SQLSTATE[23000]:Integrity constraint violation' ) return 1062;
				else return $e -> getMessage();

			}
		}
	}

	class Image
	{
		public $id;
		public $itemid;
		public $imagepath;

		function __construct( $itemid, $imagepath, $id = 0 )
		{
			$this -> itemid = $itemid;
			$this -> imagepath = $imagepath;
		}

		function intoDb()
		{
			try {

				$pdo = Tools ::connect();

				$arr = (array)$this;
				array_shift( $arr );

				$sql = "INSERT INTO images(itemid, imagepath) VALUES (:itemid, :imagepath)";
				$ps = $pdo -> prepare( $sql );
				$ps -> execute( $arr );

			} catch ( PDOException $e ) {

				$err = $e -> getMessage();
				if ( substr( $err, 0, strrpos( $err, ":" ) ) == 'SQLSTATE[23000]:Integrity constraint violation' ) return 1062;
				else return $e -> getMessage();

			}
		}

		static function fromDb($id)
		{
			$image = null;
			try {
				$pdo = Tools ::connect();
				$ps = $pdo -> prepare( "SELECT * FROM Images WHERE itemid=?" );
				$ps -> execute( array( $id ) );

				while($row = $ps -> fetch()){
					$image = new Image( $row[ 'itemid' ], $row[ 'imagepath' ], $row[ 'id' ]);
					$images[] = $image;

				}
				return $images;

			} catch ( PDOException $e ) {
				echo $e -> getMessage();
				return false;
			}
		}
	}

