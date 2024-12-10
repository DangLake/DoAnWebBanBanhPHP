<?php
// Bao gồm file kết nối cơ sở dữ liệu
include 'config.php';
include('./includes/link.php');

// Hàm lấy danh sách các loại bánh theo danh mục
function getDanhSachBanh($category_id)
{
    $pdo = connectDatabase();
    try {
        // Truy vấn lấy danh sách sản phẩm (bánh) theo category_id và kèm theo hình ảnh ngẫu nhiên từ bảng product_images
        $sql = "SELECT p.id, p.name, p.price, 
               (SELECT pi.image FROM product_images pi WHERE pi.product_id = p.id ORDER BY RAND() LIMIT 1) AS image
        FROM products p
        WHERE p.category_id = :category_id"; // Thêm dấu phẩy giữa các trường trong SELECT
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Lỗi truy vấn: " . $e->getMessage());
    }
}

// Kiểm tra nếu có category_id trong URL
if (isset($_GET['category_id'])) {
    $category_id = (int)$_GET['category_id'];  // Lấy category_id từ URL và ép kiểu về số nguyên

    // Lấy danh sách các loại bánh thuộc category_id đó
    $dsBanh = getDanhSachBanh($category_id);
} else {
    // Nếu không có category_id, hiển thị thông báo
    die("Không có danh mục nào được chọn.");
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách bánh</title>
    <script src="scripts/dsBanh.js"></script>
    <link rel="stylesheet" href="styles/dsBanh.css"> <!-- Thêm file CSS nếu cần -->
</head>

<body>
    <div class="frm-main">
        <?php include('./includes/header.php') ?>
        <div class="monmoi">
            <div class="dsSP">
                <?php if (!empty($dsBanh)): ?>
                    <?php foreach ($dsBanh as $banh): ?>
                        <?php
                        // Kiểm tra xem có hình ảnh không, nếu không thì sử dụng một hình ảnh mặc định
                        $image_url = !empty($banh['image']) ? htmlspecialchars($banh['image']) : 'default.jpg';
                        $product_name = htmlspecialchars($banh['name']);
                        $product_price = number_format($banh['price'], 0, ',', '.');
                        $product_id = $banh['id'];  // Lấy product_id
                        ?>

                        <div class="product-item">
                            <a href="banh.php?product_id=<?php echo $product_id; ?>">
                                <img src="<?php echo $image_url; ?>" alt="Hình ảnh <?php echo $product_name; ?>" />
                                <h2><?php echo $product_name; ?></h2>
                                <p><?php echo $product_price . " đ"; ?></p>
                            </a>
                        </div>

                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có loại bánh nào trong danh mục này.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>
</body>

</html>