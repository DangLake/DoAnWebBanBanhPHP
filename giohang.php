<?php
session_start();
require('config.php'); // Kết nối cơ sở dữ liệu
include('./includes/link.php');

// Kiểm tra nếu người dùng chưa đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo '<div style="min-height: 500px; display: flex; justify-content: center; align-items: center; text-align: center;">
            <p>Bạn phải <a href="dangnhap.php">đăng nhập</a> để xem giỏ hàng.</p>
          </div>';
    exit;
}

$user_id = $_SESSION['user_id']; // Lấy user_id từ session
$conn = connectDatabase();

// Xử lý cập nhật số lượng sản phẩm trong giỏ hàng
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantity'] as $cart_id => $quantity) {
        // Kiểm tra nếu số lượng hợp lệ
        if ($quantity < 1) {
            $quantity = 1; // Nếu số lượng nhỏ hơn 1, đặt lại là 1
        }

        // Cập nhật lại số lượng trong giỏ hàng
        $sql = "UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    }

    header('Location: giohang.php'); // Làm mới trang sau khi cập nhật
    exit;
}

// Xử lý xóa sản phẩm khỏi giỏ hàng
if (isset($_GET['delete'])) {
    $cart_id = $_GET['delete'];
    $sql = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    header('Location: giohang.php'); // Làm mới trang sau khi xóa
    exit;
}

// Lấy tất cả sản phẩm trong giỏ hàng
$sql = "SELECT c.id AS cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) AS total
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = :user_id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tính tổng tiền giỏ hàng
$total_price = array_sum(array_column($cart_items, 'total'));
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="styles/giohang.css">
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <div class="frm-main">
        <?php include('./includes/header.php') ?>
        <div class="main_content">
            <h2>Giỏ hàng của bạn</h2>

            <?php if (empty($cart_items)): ?>
                <p>Giỏ hàng của bạn đang trống.</p>
            <?php else: ?>
                <form action="giohang.php" method="POST">
                    <table border="1">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo number_format($item['price'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <input type="number" name="quantity[<?php echo $item['cart_id']; ?>]" 
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" style="width: 50px;">
                                    </td>
                                    <td><?php echo number_format($item['total'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <a style="text-decoration: none;color:red" href="giohang.php?delete=<?php echo $item['cart_id']; ?>" 
                                           onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">X</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3">Tổng cộng</td>
                                <td><?php echo number_format($total_price, 0, ',', '.'); ?> đ</td>
                            </tr>
                        </tfoot>
                    </table>
                    <button type="submit" name="update_cart" class="btn-update-cart">Cập nhật giỏ hàng</button>
                </form>
                <div class="actions">
                    <form action="giaohang.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="cart_items" value="<?php echo htmlspecialchars(json_encode($cart_items)); ?>">
                        <button type="submit" class="btn-submit-order">Đặt hàng</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>
</body>

</html>
