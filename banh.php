<?php
session_start();
require('config.php'); // Kết nối cơ sở dữ liệu
include('./includes/link.php');

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

// Thêm sản phẩm vào giỏ hàng khi nhấn nút
if (isset($_POST['add_to_cart'])) {
    $user_id = $_SESSION['user_id']; // Lấy user_id từ session
    $quantity = $_POST['quantity']; // Số lượng sản phẩm

    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng, nếu có thì cập nhật số lượng
    $check_sql = "SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $conn->prepare($check_sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Cập nhật số lượng sản phẩm nếu sản phẩm đã có trong giỏ
        $update_sql = "UPDATE cart SET quantity = quantity + :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $update_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $update_stmt->execute();
    } else {
        // Thêm sản phẩm vào giỏ hàng mới
        $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $insert_stmt->execute();
    }

    // Thông báo thêm thành công
    echo '<script>alert("Sản phẩm đã được thêm vào giỏ hàng!"); window.location.href="giohang.php";</script>';
    exit;
}
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
    <div class="frm-main">
        <?php include('./includes/header.php') ?>
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
                        <form action="" method="POST">
                            <div class="quantity">
                                <div class="pro-qty">
                                    <button class="qty-btn minus" onclick="giamsl()">-</button>
                                    <input type="text" value="1" id="quantity" name="quantity" readonly>
                                    <button class="qty-btn plus" onclick="tangsl()">+</button>
                                </div>
                            </div>
                            <div class="btnthemGH">
                                <input type="submit" name="add_to_cart" value="Thêm vào giỏ hàng">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="motabanh">
                <p id="mota">Mô tả</p>
                <div class="motachinh">
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>
    <script src="scripts/banh.js"></script>
</body>

</html>