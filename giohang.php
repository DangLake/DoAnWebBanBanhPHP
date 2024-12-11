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

// Xử lý xóa sản phẩm khỏi giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'remove') {
    $cart_id = $_POST['cart_id'] ?? null;

    if ($cart_id) {
        $delete_sql = "DELETE FROM cart WHERE id = :cart_id AND user_id = :user_id";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $delete_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $delete_stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.']);
    }
    exit;
}

// Xử lý cập nhật số lượng sản phẩm
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
    $cart_id = $_POST['cart_id'] ?? null;
    $quantity = $_POST['quantity'] ?? 1;

    if ($cart_id && $quantity > 0) {
        $update_sql = "UPDATE cart SET quantity = :quantity WHERE id = :cart_id AND user_id = :user_id";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $update_stmt->bindParam(':cart_id', $cart_id, PDO::PARAM_INT);
        $update_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $update_stmt->execute();

        // Lấy lại dữ liệu để tính toán tổng tiền
        $sql = "SELECT c.id AS cart_id, p.name, p.price, c.quantity, (p.price * c.quantity) AS total
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :user_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Tính tổng tiền
        $total_price = array_sum(array_column($cart_items, 'total'));

        echo json_encode([
            'status' => 'success',
            'message' => 'Số lượng sản phẩm đã được cập nhật.',
            'total_price' => number_format($total_price, 0, ',', '.')
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Dữ liệu không hợp lệ.']);
    }
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

// Tính tổng tiền
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
    <script src="scripts/giohang.js"></script>
</head>

<body>
    <div class="frm-main">
        <?php include('./includes/header.php') ?>
        <div class="main_content">
            <h2>Giỏ hàng của bạn</h2>

            <?php if (empty($cart_items)): ?>
                <p>Giỏ hàng của bạn đang trống.</p>
            <?php else: ?>
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
                                    <input type="number" value="<?php echo $item['quantity']; ?>"
                                        data-cart-id="<?php echo $item['cart_id']; ?>"
                                        class="update-quantity" min="1">
                                </td>
                                <td><?php echo number_format($item['total'], 0, ',', '.'); ?> đ</td>
                                <td>
                                    <button class="remove-item" data-cart-id="<?php echo $item['cart_id']; ?>">Xóa</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Tổng cộng</td>
                            <td id="total_price"><?php echo number_format($total_price, 0, ',', '.'); ?> đ</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
                <div class="actions">
                    <form action="giaohang.php" method="POST">
                        <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                        <input type="hidden" name="cart_items" value="<?php echo htmlspecialchars(json_encode($cart_items)); ?>">
                        <button type="submit" class="btn-submit-order" >Đặt hàng</button>
                    </form>
                </div>

            <?php endif; ?>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>

    <script>
        // Xóa sản phẩm khỏi giỏ hàng
        document.querySelectorAll('.remove-item').forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.getAttribute('data-cart-id');

                fetch('giohang.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'remove',
                            cart_id: cartId
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            location.reload(); // Tải lại trang sau khi xóa
                        } else {
                            alert(data.message);
                        }
                    });
            });
        });

        // Cập nhật số lượng sản phẩm trong giỏ hàng
        document.querySelectorAll('.update-quantity').forEach(input => {
            input.addEventListener('change', function() {
                const cartId = this.getAttribute('data-cart-id');
                const quantity = this.value;

                fetch('giohang.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: new URLSearchParams({
                            action: 'update',
                            cart_id: cartId,
                            quantity: quantity
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message);
                            document.getElementById('total_price').innerText = data.total_price + ' đ'; // Cập nhật tổng tiền
                        } else {
                            alert(data.message);
                        }
                    });
            });
        });
    </script>
</body>
<style>
    .actions {
    text-align: right;
    margin-top: 20px;
}
    .btn-submit-order {
        background-color: #ff5733;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 20px;
        text-transform: uppercase;
        box-sizing: border-box;
        display: inline-block;
        text-align: center;
    }

    .btn-submit-order:hover {
        background-color: #ff3d00;
    }
</style>

</html>