<?php 
session_start();
error_reporting(0);
include('includes/config.php');
if(isset($_GET['action']) && $_GET['action']=="add"){
	$id=intval($_GET['id']);
	if(isset($_SESSION['cart'][$id])){
		$_SESSION['cart'][$id]['quantity']++;
	}else{
		$sql_p="SELECT * FROM products WHERE id={$id}";
		$query_p=mysqli_query($con,$sql_p);
		if(mysqli_num_rows($query_p)!=0){
			$row_p=mysqli_fetch_array($query_p);
			$_SESSION['cart'][$row_p['id']]=array("quantity" => 1, "price" => $row_p['productPrice']);
					echo "<script>alert('Product has been added to the cart')</script>";
		echo "<script type='text/javascript'> document.location ='my-cart.php'; </script>";
		}else{
			$message="Product ID is invalid";
		}
	}
}
$pid=intval($_GET['pid']);
if(isset($_GET['pid']) && $_GET['action']=="wishlist" ){
	if(strlen($_SESSION['login'])==0)
    {   
header('location:login.php');
}
else
{
mysqli_query($con,"insert into wishlist(userId,productId) values('".$_SESSION['id']."','$pid')");
echo "<script>alert('Product aaded in wishlist');</script>";
header('location:my-wishlist.php');

}
}
if(isset($_POST['submit']))
{
	$qty=$_POST['quality'];
	$price=$_POST['price'];
	$value=$_POST['value'];
	$name=$_POST['name'];
	$summary=$_POST['summary'];
	$review=$_POST['review'];
	mysqli_query($con,"insert into productreviews(productId,quality,price,value,name,summary,review) values('$pid','$qty','$price','$value','$name','$summary','$review')");
}


?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
		<meta name="description" content="">
		<meta name="author" content="">
	    <meta name="keywords" content="MediaCenter, Template, eCommerce">
	    <meta name="robots" content="all">
	    <title>Product Details</title>
	    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
	    <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/black.css">
	    <link rel="stylesheet" href="assets/css/owl.carousel.css">
		<link rel="stylesheet" href="assets/css/owl.transitions.css">
		<link href="assets/css/lightbox.css" rel="stylesheet">
		<link rel="stylesheet" href="assets/css/animate.min.css">
		<link rel="stylesheet" href="assets/css/rateit.css">
		<link rel="stylesheet" href="assets/css/bootstrap-select.min.css">
		<link rel="stylesheet" href="assets/css/config.css">

		<!-- <link href="assets/css/green.css" rel="alternate stylesheet" title="Green color">
		<link href="assets/css/blue.css" rel="alternate stylesheet" title="Blue color">
		<link href="assets/css/red.css" rel="alternate stylesheet" title="Red color">
		<link href="assets/css/orange.css" rel="alternate stylesheet" title="Orange color">
		<link href="assets/css/dark-green.css" rel="alternate stylesheet" title="Darkgreen color"> -->
		<link rel="stylesheet" href="assets/css/font-awesome.min.css">

        <!-- Fonts --> 
		<link href='http://fonts.googleapis.com/css?family=Roboto:300,400,500,700' rel='stylesheet' type='text/css'>
		<link rel="shortcut icon" href="assets/images/favicon.ico">

		<script>
			// Assuming you already have the averageRating variable from your PHP code
var averageRating = <?php echo $averageRating; ?>;

// Display the average rating
document.getElementById("average-rating").textContent = averageRating.toFixed(2);

// Calculate the number of filled stars based on the average rating
var starRating = document.getElementById("star-rating");
var numFilledStars = Math.round(averageRating);
var stars = starRating.getElementsByClassName("star");

for (var i = 0; i < numFilledStars; i++) {
    stars[i].classList.add("filled");
}

		</script>
	</head>
    <body class="cnt-home">
	
<header class="header-style-1">

<?php include('includes/top-header.php');?>
<?php include('includes/main-header.php');?>
<?php include('includes/menu-bar.php');?>

</header>

<div class="breadcrumb">
	<div class="container">
		<div class="breadcrumb-inner">
			<?php
			$ret=mysqli_query($con,"select category.categoryName as catname,subCategory.subcategory as subcatname,products.productName as pname from products join category on category.id=products.category join subcategory on subcategory.id=products.subCategory where products.id='$pid'");
			while ($rw=mysqli_fetch_array($ret)) {
			?>
				<ul class="list-inline list-unstyled">
					<li><a href="index.php">Home</a></li>
					<li><?php echo htmlentities($rw['catname']);?></a></li>
					<li><?php echo htmlentities($rw['subcatname']);?></li>
					<li class='active'><?php echo htmlentities($rw['pname']);?></li>
				</ul>
			<?php }?>
		</div>
	</div>
</div>

<div class="body-content outer-top-xs">
	<div class='container'>
		<div class='row single-product outer-bottom-sm '>
			<div class='col-md-3 sidebar'>
				<div class="sidebar-module-container">
					<div class="sidebar-widget outer-bottom-xs wow fadeInUp">
						<h3 class="section-title">Category</h3>
						<div class="sidebar-widget-body m-t-10">
							<div class="accordion">

						<?php $sql=mysqli_query($con,"select id,categoryName  from category");
						while($row=mysqli_fetch_array($sql))
						{
							?>
									<div class="accordion-group">
										<div class="accordion-heading">
											<a href="category.php?cid=<?php echo $row['id'];?>"  class="accordion-toggle collapsed">
											<?php echo $row['categoryName'];?>
											</a>
										</div>
									
									</div>
									<?php } ?>
	    </div>
	</div>
					</div>


				</div>
			</div>
<?php 
$ret=mysqli_query($con,"select * from products where id='$pid'");
while($row=mysqli_fetch_array($ret))
{

?>


			<div class='col-md-9'>
				<div class="row  wow fadeInUp">
					     <div class="col-xs-12 col-sm-6 col-md-5 gallery-holder">
    <div class="product-item-holder size-big single-product-gallery small-gallery">

        <div id="owl-single-product">

 <div class="single-product-gallery-item" id="slide1">
                <a data-lightbox="image-1" data-title="<?php echo htmlentities($row['productName']);?>" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>">
                    <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" width="370" height="350" />
                </a>
            </div>




            <div class="single-product-gallery-item" id="slide1">
                <a data-lightbox="image-1" data-title="<?php echo htmlentities($row['productName']);?>" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>">
                    <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" width="370" height="350" />
                </a>
            </div>

            <div class="single-product-gallery-item" id="slide2">
                <a data-lightbox="image-1" data-title="Gallery" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>">
                    <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>" />
                </a>
            </div>

            <div class="single-product-gallery-item" id="slide3">
                <a data-lightbox="image-1" data-title="Gallery" href="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>">
                    <img class="img-responsive" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" />
                </a>
            </div>

        </div>


        <div class="single-product-gallery-thumbs gallery-thumbs">

            <div id="owl-single-product-thumbnails">
                <div class="item">
                    <a class="horizontal-thumb active" data-target="#owl-single-product" data-slide="1" href="#slide1">
                        <img class="img-responsive"  alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" />
                    </a>
                </div>

            <div class="item">
                    <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="2" href="#slide2">
                        <img class="img-responsive" width="85" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage2']);?>"/>
                    </a>
                </div>
                <div class="item">

                    <a class="horizontal-thumb" data-target="#owl-single-product" data-slide="3" href="#slide3">
                        <img class="img-responsive" width="85" alt="" src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage3']);?>" height="200" />
                    </a>
                </div>

               
               
                
            </div>

            

        </div>

    </div>
</div>     			




					<div class='col-sm-6 col-md-7 product-info-block'>
						<div class="product-info">
							<h1 class="name"><?php echo htmlentities($row['productName']);?></h1>
							<?php 

$rt = mysqli_query($con, "SELECT AVG(quality) AS average_rating, COUNT(*) AS review_count FROM productreviews WHERE productId='$pid'");
$ratingRow = mysqli_fetch_assoc($rt);
$averageRating = $ratingRow['average_rating'];
$reviewCount = $ratingRow['review_count'];
{
?>
	
						
<?php } ?>
<div class="stock-container info-container m-t-10">
    <div class="row">
        <div class="col-sm-3">
            <div class="stock-box">
                <span class="label">Ratings:</span>
            </div>
        </div>
        <div class="col-sm-9">
            <div class="stock-box">
                <span class="value">
                    <b><span id="average-rating" style="font-size: 20px;"><?php echo number_format($averageRating, 2); ?> </span></b>
                    <span class="value"><a href="#" class="lnk">(<?php echo htmlentities($reviewCount);?> Reviews)</a></span>
					<div class="star-rating" id="star-rating">
                       
                    </div>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="stock-container info-container m-t-10">
								<div class="row">
									<div class="col-sm-3">
										<div class="stock-box">
											<span class="label">Availability :</span>
										</div>	
									</div>
									<div class="col-sm-9">
										<div class="stock-box">
											<span class="value" style="font-size: 20px;"><?php echo htmlentities($row['productAvailability']);?></span>
										</div>	
									</div>
								</div>	
							</div>



<div class="stock-container info-container m-t-10">
								<div class="row">
									<div class="col-sm-3">
										<div class="stock-box">
											<span class="label">Product Brand :</span>
										</div>	
									</div>
									<div class="col-sm-9">
										<div class="stock-box">
											<span class="value" style="font-size: 20px;"><?php echo htmlentities($row['productCompany']);?></span>
										</div>	
									</div>
								</div>	
							</div>


<div class="stock-container info-container m-t-10">
								<div class="row">
									<div class="col-sm-3">
										<div class="stock-box">
											<span class="label">Shipping Charge :</span>
										</div>	
									</div>
									<div class="col-sm-9">
										<div class="stock-box">
											<span class="value" style="font-size: 20px;"><?php if($row['shippingCharge']==0)
											{
												echo "Free";
											}
											else
											{
												echo htmlentities($row['shippingCharge']);
											}

											?></span>
										</div>	
									</div>
								</div>	
							</div>

							<div class="price-container info-container m-t-20">
								<div class="row">
									

									<div class="col-sm-6">
										<div class="price-box">
											<span class="price">Rs. <?php echo htmlentities($row['productPrice']);?></span>
											<span class="price-strike">Rs.<?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
										</div>
									</div>




									<div class="col-sm-6">
										<div class="favorite-button m-t-10">
											<a class="btn btn-primary" data-toggle="tooltip" data-placement="right" title="Wishlist" href="product-details.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist">
											    <i class="fa fa-heart"></i>
											</a>
											
											</a>
										</div>
									</div>

								</div>
							</div>

	




							<div class="quantity-container info-container">
								<div class="row">
									

									<div class="col-sm-7">
<?php if($row['productAvailability']=='In Stock'){?>
										<a href="product-details.php?page=product&action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary"><i class="fa fa-shopping-cart inner-right-vs"></i> ADD TO CART</a>
													<?php } else {?>
							<div class="action" style="color:red">Out of Stock</div>
					<?php } ?>
									</div>

									
								</div>
							</div>
	
						</div>
					</div>
				</div>

				
				<div class="product-tabs inner-bottom-xs  wow fadeInUp">
					<div class="row">
						<div class="col-sm-3">
							<ul id="product-tabs" class="nav nav-tabs nav-tab-cell">
								<li class="active"><a data-toggle="tab" href="#description">DESCRIPTION</a></li>
								<li><a data-toggle="tab" href="#review">REVIEW</a></li>
							</ul>
						</div>
						<div class="col-sm-9">

							<div class="tab-content">
								
								<div id="description" class="tab-pane in active">
									<div class="product-tab">
										<p class="text"><?php echo $row['productDescription'];?></p>
									</div>	
								</div>

								<div id="review" class="tab-pane">
									<div class="product-tab">
																				
										<div class="product-reviews">
											<h4 class="title">Customer Reviews</h4>
<?php $qry=mysqli_query($con,"select * from productreviews where productId='$pid'");
while($rvw=mysqli_fetch_array($qry))
{
?>

<div class="reviews" style="border: solid 1px #000; padding-left: 2% ">
	<div class="review">
		<div class="review-title">
			<span class="summary"><?php echo htmlentities($rvw['summary']);?></span>
			<span class="date"><i class="fa fa-calendar"></i>
				<span><?php echo htmlentities($rvw['reviewDate']);?></span>
			</span>
		</div>

		<div class="text">
			<b>Ratings :</b> <?php echo htmlentities($rvw['quality']);?> Star
		</div>

		<div class="text">
			<b>Review :</b>"<?php echo htmlentities($rvw['review']);?>"
		</div>
		<div class="author m-t-15">
			<i class="fa fa-pencil-square-o"></i> 
			<span class="name"><?php echo htmlentities($rvw['name']);?></span>
		</div>
	</div>
</div>

<?php } ?>
</div>
<br>
<form role="form" class="cnt-form" name="review" method="post">
<div class="product-add-review">
<h4 class="title">Give Ratings:</h4>
<div class="review-table">
<div class="table-responsive">
<table class="table table-bordered">	
<thead>
<tr>
<th class="cell-label">&nbsp;</th>
<th>1 star</th>
<th>2 stars</th>
<th>3 stars</th>
<th>4 stars</th>
<th>5 stars</th>
</tr>
</thead>	
<tbody>
	<tr>
		<td class="cell-label">Ratings:</td>
		<td><input type="radio" name="quality" class="radio" value="1"></td>
		<td><input type="radio" name="quality" class="radio" value="2"></td>
		<td><input type="radio" name="quality" class="radio" value="3"></td>
		<td><input type="radio" name="quality" class="radio" value="4"></td>
		<td><input type="radio" name="quality" class="radio" value="5"></td>
	</tr>
	
</tbody>
</table>
</div>
</div>

<div class="review-form">
<div class="form-container">

	
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group">
				<label for="exampleInputName">Your Name <span class="astk">*</span></label>
			<input type="text" class="form-control txt" id="exampleInputName" placeholder="" name="name" required="required">
			</div><!-- /.form-group -->
			<div class="form-group">
				<label for="exampleInputReview">Review <span class="astk">*</span></label>
				<textarea class="form-control txt txt-review" id="exampleInputReview" rows="4" placeholder="" name="review" required="required"></textarea>
			</div>
		</div>
	</div>
	
	<div class="action text-right">
		<button name="submit" class="btn btn-primary btn-upper">SUBMIT REVIEW</button>
	</div>

</form>
</div>
</div>

</div>									

</div>
</div>



</div>
</div>
</div>

</div>

<?php $cid=$row['category'];
$subcid=$row['subCategory']; } ?>
                



            
            </div>
            <div class="clearfix"></div>
        </div>
	<section class="section featured-product wow fadeInUp">

	<!-- <div class="recommended-products-section outer-top-xs"> -->
    <h3 class="section-title">Similar Product</h3>
    <div class="recommended-products">
	<div class="search-result-container">
					<div id="myTabContent" class="tab-content">
						<div class="tab-pane active " id="grid-container">
							<div class="category-product  inner-top-vs">
								<div class="row">									
			<?php
$ret=mysqli_query($con,"select * from products where category='$cid' LIMIT 4;");
$num=mysqli_num_rows($ret);
if($num>0)
{
while ($row=mysqli_fetch_array($ret)) 
{?>							
		<div class="col-sm-6 col-md-3 wow fadeInUp">
			<div class="products">				
	<div class="product">		
		<div class="product-image">
			<div class="image">
				<a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><img  src="assets/images/blank.gif" data-echo="admin/productimages/<?php echo htmlentities($row['id']);?>/<?php echo htmlentities($row['productImage1']);?>" alt="" width="200" height="300"></a>
			</div><!-- /.image -->			                      		   
		</div><!-- /.product-image -->
			
		
		<div class="product-info text-left">
			<h3 class="name"><a href="product-details.php?pid=<?php echo htmlentities($row['id']);?>"><?php echo htmlentities($row['productName']);?></a></h3>
			<div class="rating rateit-small"></div>
			<div class="description"></div>

			<div class="product-price">	
				<span class="price">
					Rs. <?php echo htmlentities($row['productPrice']);?>			</span>
										     <span class="price-before-discount">Rs. <?php echo htmlentities($row['productPriceBeforeDiscount']);?></span>
									
			</div><!-- /.product-price -->
			
		</div><!-- /.product-info -->
					<div class="cart clearfix animate-effect">
				<div class="action">
					<ul class="list-unstyled">
						<li class="add-cart-button btn-group">
						
								<?php if($row['productAvailability']=='In Stock'){?>
										<button class="btn btn-primary icon" data-toggle="dropdown" type="button">
								<i class="fa fa-shopping-cart"></i>													
							</button>
							<a href="category.php?page=product&action=add&id=<?php echo $row['id']; ?>">
							<button class="btn btn-primary" type="button">Add to cart</button></a>
								<?php } else {?>
							<div class="action" style="color:red">Out of Stock</div>
					<?php } ?>
													
						</li>
	                   
		                <li class="lnk wishlist">
							<a class="add-to-cart" href="category.php?pid=<?php echo htmlentities($row['id'])?>&&action=wishlist" title="Wishlist">
								 <i class="icon fa fa-heart"></i>
							</a>
						</li>

						
					</ul>
				</div>
			</div>
		</div>
			</div>
		</div>
	  <?php } } else {?>
	
		<div class="col-sm-6 col-md-4 wow fadeInUp"> <h3>No Product Found</h3>
		</div>
		
<?php } ?>	
		
					</div>
							</div>
						
						</div>
						
				

				</div>

			</div>
    </div>

	
</div>

      

    
        
     </div>
</section>
</div>

</div>


<?php include('includes/footer.php');?>

	<script src="assets/js/jquery-1.11.1.min.js"></script>
	
	<script src="assets/js/bootstrap.min.js"></script>
	
	<script src="assets/js/bootstrap-hover-dropdown.min.js"></script>
	<script src="assets/js/owl.carousel.min.js"></script>
	
	<script src="assets/js/echo.min.js"></script>
	<script src="assets/js/jquery.easing-1.3.min.js"></script>
	<script src="assets/js/bootstrap-slider.min.js"></script>
    <script src="assets/js/jquery.rateit.min.js"></script>
    <script type="text/javascript" src="assets/js/lightbox.min.js"></script>
    <script src="assets/js/bootstrap-select.min.js"></script>
    <script src="assets/js/wow.min.js"></script>
	<script src="assets/js/scripts.js"></script>

	
	<script src="switchstylesheet/switchstylesheet.js"></script>
	
	<script>
		$(document).ready(function(){ 
			$(".changecolor").switchstylesheet( { seperator:"color"} );
			$('.show-theme-options').click(function(){
				$(this).parent().toggleClass('open');
				return false;
			});
		});

		$(window).bind("load", function() {
		   $('.show-theme-options').delay(2000).trigger('click');
		});
	</script>

	

</body>
</html>