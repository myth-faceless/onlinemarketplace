<?php
// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "shopping");

// Fetch all products and categories
$products = $mysqli->query("SELECT * FROM products");
$categories = $mysqli->query("SELECT * FROM category");

// For a given user
$userId = $_SESSION['id']; // Example user ID

// Create a user profile vector based on categories
$userProfile = [];
while ($category = $categories->fetch_assoc()) {
    $countBoughtCategory = $mysqli->query("SELECT COUNT(*) as count FROM orders 
    JOIN products ON orders.productId = products.id 
    WHERE userId = $userId AND products.category = " . $category['id'])->fetch_assoc()['count'];
    $userProfile[$category['id']] = $countBoughtCategory;
}

// Check if the user has ordered any products
$totalOrderedProducts = array_sum($userProfile);

$recommendedProductIds = [];

if ($totalOrderedProducts == 0) {
    echo "Please order something to get recommended.";
} else {
    // Calculate the cosine similarity between the user profile and each product
    $productScores = [];
    while ($product = $products->fetch_assoc()) {
        $productVector = [];
        foreach ($userProfile as $categoryId => $value) {
            $productVector[$categoryId] = ($product['category'] == $categoryId) ? 1 : 0;
        }
        $dotProduct = array_sum(array_map(function($a, $b) { return $a * $b; }, $userProfile, $productVector));
        $magnitudeA = sqrt(array_sum(array_map(function($a) { return $a * $a; }, $userProfile)));
        $magnitudeB = sqrt(array_sum(array_map(function($a) { return $a * $a; }, $productVector)));
        $productScores[$product['id']] = $dotProduct / ($magnitudeA * $magnitudeB);
    }
    // Sort products by their scores in descending order
    arsort($productScores);

    // Recommend the top N products
    $N = 6;
    $recommendedProducts = array_slice($productScores, 0, $N, true);
    $recommendedProductIds = array_keys($recommendedProducts);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- ... other meta tags and styles ... -->

    <style>
       /* Product Grid CSS *//* Product Grid CSS */
/* Product Grid CSS */
.product-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 10px; /* Reduced gap */
}

.product-item {
    border: 1px solid #e1e1e1;
    padding: 5px; /* Reduced padding */
    transition: transform 0.2s;
}

.product-item:hover {
    transform: scale(1.05);
}

.product-image img {
    max-width: 120px; /* Reduced image size */
    height: auto;
    display: block;
    margin: 0 auto;
}

.product-info {
    text-align: center;

    padding: 5px 0; /* Reduced padding */
}

.product-price {
    color: #333333;
    font-weight: bold;
    text-decoration: line-through;
    float: right; /* Display on the right side */
}

.discounted-price {
    color: #ff9d00;
    font-weight: bold;

    float: left; /* Display on the left side */
}

.action .btn {
    background-color: grey;
    color: #fff;
    border: none;
    padding: 3px 8px; /* Reduced padding */
    border-radius: 15px; /* Rounded borders */
    transition: background-color 0.3s;
    font-size: 12px; /* Reduced font size */
}

.action .btn:hover {
    background-color: #ff9d00; 
}


    </style>
     <link rel="stylesheet" href="assets/css/main.css">
	    <link rel="stylesheet" href="assets/css/black.css">
</head>
<body class="cnt-home">
<!-- ... rest of the HTML code ... -->

<div class="body-content outer-top-xs">
    <div class='container'>
        <div class='row outer-bottom-sm'>
            <div class='col-md-12'>
                <div class="search-result-container">
                    <div class="product-grid">
                        <?php
                        foreach ($recommendedProductIds as $productId) {
                            $productDetails = $mysqli->query("SELECT * FROM products WHERE id = $productId")->fetch_assoc();
                            if ($productDetails) {
                                echo '<div class="product-item">';
                                echo '<div class="product-image">';
                                echo '<a href="product-details.php?pid=' . $productDetails['id'] . '">';
                                echo '<img src="admin/productimages/' . $productDetails['id'] . '/' . $productDetails['productImage1'] . '" alt="' . $productDetails['productName'] . '">';
                                echo '</a>';
                                echo '</div>';
                                echo '<div class="product-info">';
                                echo '<h3 class="name"><a href="product-details.php?pid=' . $productDetails['id'] . '">' . $productDetails['productName'] . '</a></h3>';
                                echo '<div class="discounted-price">Rs. ' . $productDetails['productPrice'] . '</div>';
                                echo '<div class="product-price">Rs. ' . $productDetails['productPriceBeforeDiscount'] . '</div>';
                                if ($productDetails['productAvailability'] == 'In Stock') {
                                    echo '<div class="action"><a href="index.php?page=product&action=add&id=' . $productDetails['id'] . '" class="lnk btn">Add to Cart</a></div>';
                                } else {
                                    echo '<div class="out-of-stock">Out of Stock</div>';
                                }
                                echo '</div>';
                                echo '</div>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>		
</div>

<!-- ... footer and scripts ... -->


</body>
</html>
