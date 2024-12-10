<link rel="stylesheet" href="./styles/timkiem.css">
<link rel="stylesheet" href="./styles/main.css">
<?php
require 'config.php';
include('./includes/link.php');
// Kết nối cơ sở dữ liệu

// Kết nối cơ sở dữ liệu
$pdo = connectDatabase();

// Kiểm tra từ khóa tìm kiếm
if (isset($_GET['query'])) {
    $query = trim($_GET['query']); // Loại bỏ khoảng trắng
    $query = htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); // Bảo mật chống XSS

    // Tách từ khóa thành các từ
    $keywords = explode(" ", $query);
    
    // Tạo câu SQL với nhiều điều kiện LIKE
    $sql = "SELECT p.*, pi.image 
            FROM products p 
            LEFT JOIN product_images pi ON p.id = pi.product_id 
            WHERE ";
    $conditions = [];
    foreach ($keywords as $key => $word) {
        $conditions[] = "p.name LIKE :keyword{$key}";
    }
    $sql .= implode(" AND ", $conditions); // Kết hợp các điều kiện với "AND"
    $sql .= " GROUP BY p.id"; // Lấy sản phẩm với hình ảnh đầu tiên

    $stmt = $pdo->prepare($sql);

    // Gán giá trị cho từng từ khóa
    foreach ($keywords as $key => $word) {
        $stmt->bindValue(":keyword{$key}", '%' . $word . '%', PDO::PARAM_STR);
    }

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Hiển thị kết quả
    echo "<div class='search-results'>";
    if ($result) {
        echo "<h3>Kết quả tìm kiếm cho: <em>" . htmlspecialchars($query, ENT_QUOTES, 'UTF-8') . "</em></h3>";
        echo "<div class='product-list'>";
        foreach ($result as $row) {
            $image = $row['image'] ? $row['image'] : 'default-image.jpg'; // Ảnh mặc định nếu không có
            echo "<div class='product-item'>
                    <img src='" . $image . "' alt='" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "'>
                    <h4>" . htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8') . "</h4>
                    <p>" . number_format($row['price'], 2) . " VND</p>
                    <a href='banh.php?product_id=" . $row['id'] . "' class='btn'>Xem chi tiết</a>
                </div>";
        }
        echo "</div>";
    } else {
        echo "<h3 style='min-height: 500px;'>Không tìm thấy sản phẩm phù hợp.</h3>";
    }
    echo "</div>";
} else {
    echo "<h3>Vui lòng nhập từ khóa tìm kiếm.</h3>";
}
?>