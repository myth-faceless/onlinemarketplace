<?php
// Connect to the database
$mysqli = new mysqli("localhost", "root", "", "shopping");

// Fetch all products and categories
$products = $mysqli->query("SELECT * FROM products");
$categories = $mysqli->query("SELECT * FROM category");

// For a given user
$userId = $_SESSION['id']; // Example user ID

// echo $userId;

$totalOrders = $mysqli->query("SELECT COUNT(*) as count FROM orders WHERE userId = $userId")->fetch_assoc()['count'];

$recommendedProductIds = [];

// If the user has no orders, display a message
if ($totalOrders == 0) {
    echo "Please order something to get recommended.";
} else {
    // Calculate the probability of the user buying each product
    $productProbabilities = [];
    while ($product = $products->fetch_assoc()) {
        $countBoughtProduct = $mysqli->query("SELECT COUNT(*) as count FROM orders WHERE userId = $userId AND productId = " . $product['id'])->fetch_assoc()['count'];
        $productProbabilities[$product['id']] = $countBoughtProduct / $totalOrders;
    }

    // Calculate the probability of the user buying from each category
    $categoryProbabilities = [];
    while ($category = $categories->fetch_assoc()) {
        $countBoughtCategory = $mysqli->query("SELECT COUNT(*) as count FROM orders JOIN products ON orders.productId = products.id WHERE userId = $userId AND products.category = " . $category['id'])->fetch_assoc()['count'];
        $categoryProbabilities[$category['id']] = $countBoughtCategory / $totalOrders;
    }

    // Use Bayes theorem to refine our product probabilities based on category probabilities
    $refinedProbabilities = [];
    foreach ($productProbabilities as $productId => $probability) {
        $productCategory = $mysqli->query("SELECT category FROM products WHERE id = $productId")->fetch_assoc()['category'];
        $refinedProbabilities[$productId] = $probability * $categoryProbabilities[$productCategory];
    }

    // Sort products by their refined probabilities
    arsort($refinedProbabilities);

    // Recommend the top N products
    $N = 6;
    $recommendedProducts = array_slice($refinedProbabilities, 0, $N, true);

    $recommendedProductIds = array_keys($recommendedProducts); // Assuming $recommendedProducts is the result from the recommendation algorithm
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
    grid-template-columns: repeat(3, 1fr);
    gap: 5px; /* Reduced gap */
}

.product-item {
    border: 2px solid #e1e1e1;
    padding: 5px; /* Reduced padding */
    transition: transform 0.3s;
}

.product-item:hover {
    transform: scale(1.15);
    background-color: white;
    border: #ff9d00 solid 2px;
}

.product-image img {
    max-width: 100px; /* Reduced image size */
    height: 100px;
    display: block;
    /* align-items: center; */
    margin: 0 auto;
}

.product-info {
    text-align: center;
    padding: 5px 0; /* Reduced padding */
}

.product1-price {
    color: #333333;
    text-decoration: line-through;
    font-weight: bold;
    font-size: 15px;
    float: right;
    padding-right: 15px;

}

.discounted-price {
    color: #ff9d00;
    font-weight: bold;
    font-size: 15px;
    float: left; /* Display on the left side */
    padding-left: 15px;
}

.action .btn {
    background-color: grey;
    color: #fff;
    margin: 20px;
    padding: 3px 8px; /* Reduced padding */
    border-radius: 3px; /* Rounded borders */
    transition: background-color 0.3s;
    font-weight: bold;
    font-size: 15px;
}

.action .btn:hover {
    background-color: #ff9d00; /* Marron color on hover */
}


    </style>
</head>
<body class="cnt-home">

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
                                echo '<div class="product1-price">Rs. ' . $productDetails['productPriceBeforeDiscount'] . '</div>';
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