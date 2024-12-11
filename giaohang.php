<?php
session_start();
include_once './config.php';
include('./includes/link.php');
// Nhận dữ liệu từ POST
$user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
$cart_items = isset($_POST['cart_items']) ? json_decode($_POST['cart_items'], true) : [];

// Kiểm tra dữ liệu
if (!$user_id || empty($cart_items)) {
    die("Dữ liệu không hợp lệ!");
}

// Tính toán tổng giá trị đơn hàng
$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['quantity'] * $item['price'];
}
$shipping_fee = 30000; // Phí giao hàng cố định
$total = $subtotal + $shipping_fee;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Thanh Toán</title>
    <link rel="stylesheet" href="styles/giaohang.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="scripts/giaohang.js"></script>
</head>

<body>
    <div class="frm-main">
        <?php include('./includes/header.php'); ?>
        <section class="checkout">
            <div class="container">
                <h2>THÔNG TIN THANH TOÁN</h2>
                <form id="checkoutForm" method="POST" action="">
                    <div class="row">
                        <div class="checkout__input">
                            <p>Họ và tên<span>*</span></p>
                            <input type="text" name="full_name" required>
                        </div>
                        <div class="checkout__input">
                            <p>Số điện thoại<span>*</span></p>
                            <input type="text" name="phone" required>
                        </div>
                        <div class="checkout__input">
                            <p>Email<span>*</span></p>
                            <input type="email" name="email" required>
                        </div>
                        <div class="checkout__input">
                            <p>Tỉnh/thành phố<span>*</span></p>
                            <input type="text" name="city" required>
                        </div>
                        <div class="checkout__input">
                            <p>Quận huyện<span>*</span></p>
                            <input type="text" name="district" required>
                        </div>
                        <div class="checkout__input">
                            <p>Địa chỉ<span>*</span></p>
                            <input type="text" name="address" required>
                        </div>
                    </div>

                    <h2>THÔNG TIN ĐƠN HÀNG</h2>
                    <table class="ttdh">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SL</th>
                                <th>Tổng</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo number_format($item['quantity'] * $item['price'], 0, ',', '.'); ?> đ</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div class="tinhtien">
                        <div class="tinh tam">
                            <h4>Tạm tính</h4>
                            <p><?php echo number_format($subtotal, 0, ',', '.'); ?> đ</p>
                        </div>
                        <div class="tinh giao">
                            <h4>Giao hàng</h4>
                            <p><?php echo number_format($shipping_fee, 0, ',', '.'); ?> đ</p>
                        </div>
                        <div class="tinh tong">
                            <h4>Tổng</h4>
                            <p><?php echo number_format($total, 0, ',', '.'); ?> đ</p>
                        </div>
                    </div>

                    <div class="checkout__order">
                        <button type="submit" class="site-btn">ĐẶT HÀNG</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
    <?php include('./includes/footer.php'); ?>
</body>

</html>
