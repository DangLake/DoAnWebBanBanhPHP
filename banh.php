<?php 
require ('config.php');  // Kết nối cơ sở dữ liệu

// Kiểm tra xem có truyền product_id qua URL không
if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
} else {
    die('Sản phẩm không tồn tại');
}

// Kết nối cơ sở dữ liệu và lấy thông tin sản phẩm
$conn = connectDatabase();
$sql = "SELECT p.*, pi.image, p.description FROM products p 
        LEFT JOIN product_images pi ON p.id = pi.product_id 
        WHERE p.id = :product_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die('Sản phẩm không tồn tại');
}

// Lấy tất cả ảnh sản phẩm từ bảng product_images
$query = "SELECT image FROM product_images WHERE product_id = :product_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
$stmt->execute();
$images = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $images[] = $row['image'];
}

$sql_categories = "SELECT id, name FROM categories";
$stmt = $conn->prepare($sql_categories);
$stmt->execute();
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Product Details</title>
    <link rel="stylesheet" href="styles/banh.css">
</head>

<body>
    <div class="banhbanh">
        <div class="banhup">
            <div class="slideshow-container">
                <?php
                foreach ($images as $image_url) {
                    echo '<div class="mySlides fade">
                            <img src="' . htmlspecialchars($image_url) . '" style="width:100%">
                          </div>';
                }
                ?>

                <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
                <a class="next" onclick="plusSlides(1)">&#10095;</a>

                <div class="dots-container">
                    <?php
                    // Tạo dots cho các slide
                    foreach ($images as $index => $image_url) {
                        echo '<span class="dot" onclick="currentSlide(' . ($index + 1) . ')"></span>';
                    }
                    ?>
                </div>
            </div>
            <div class="thongtinbanh">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <h3><?php echo number_format($product['price'], 0, ',', '.'); ?> đ</h3>
                <div class="addGH">
                    <div class="quantity">
                        <div class="pro-qty">
                            <button class="qty-btn minus" onclick="giamsl()">-</button>
                            <input type="text" value="1" id="quantity" readonly>
                            <button class="qty-btn plus" onclick="tangsl()">+</button>
                        </div>
                    </div>
                    <div class="btnthemGH">
                        <input type="button" value="Thêm vào giỏ hàng" onclick="giohang()">
                    </div>
                </div>
            </div>
        </div>
        <div class="motabanh">
            <p id="mota">Mô tả</p>
            <div class="motachinh">
            <p>
            <?php echo htmlspecialchars($product['description']); ?>
        </p>
                <h4>Xem thêm các loại bánh khác</h4>
                <ul>
                <?php foreach ($categories as $category): ?>
                    <li id="khac">
                        <a href="dsBanh.php?category_id=<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <script src="scripts/banh.js"></script>
</body>
</html>
