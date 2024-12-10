<?php
session_start();
include './config.php';
include('./includes/link.php');

// Kiểm tra nếu người dùng đã đăng nhập
if (!isset($_SESSION['user_id'])) {
    header('Location: dangnhap.php');
    exit();
}

// Kết nối cơ sở dữ liệu
$conn = connectDatabase();

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE id = :id");
$stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra và thay thế NULL bằng chuỗi rỗng
$username = isset($user['username']) ? htmlspecialchars($user['username']) : '';
$email = isset($user['email']) ? htmlspecialchars($user['email']) : '';
$phone = isset($user['phone']) ? htmlspecialchars($user['phone']) : '';
$address = isset($user['address']) ? htmlspecialchars($user['address']) : '';

// Xử lý cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Lấy dữ liệu từ form
    $new_username = trim(htmlspecialchars($_POST['name']));
    $new_email = trim(htmlspecialchars($_POST['email']));
    $new_phone = trim(htmlspecialchars($_POST['phone']));
    $new_address = trim(htmlspecialchars($_POST['address']));

    // Cập nhật thông tin người dùng trong cơ sở dữ liệu
    try {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET username = :username, email = :email, phone = :phone, address = :address 
            WHERE id = :id
        ");
        $update_stmt->bindParam(':username', $new_username, PDO::PARAM_STR);
        $update_stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
        $update_stmt->bindParam(':phone', $new_phone, PDO::PARAM_STR);
        $update_stmt->bindParam(':address', $new_address, PDO::PARAM_STR);
        $update_stmt->bindParam(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        if ($update_stmt->execute()) {
            // Lưu thông báo thành công vào session
            $_SESSION['update_message'] = 'Cập nhật thông tin thành công!';
            header('Location: index.php');
            exit();
        } else {
            $_SESSION['update_message'] = 'Cập nhật không thành công, vui lòng thử lại!';
        }
    } catch (PDOException $e) {
        $_SESSION['update_message'] = 'Lỗi: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Người Dùng</title>
    <link rel="stylesheet" href="./styles/thongtinuser.css">
    <link rel="stylesheet" href="./styles/main.css">
</head>

<body>
    <div class="frm-main">
        <?php include('./includes/header.php') ?>
        <div class="thongtin">
            <h2>Thông Tin Của Bạn</h2>

            <!-- Hiển thị thông báo cập nhật -->
            <?php
            if (isset($_SESSION['update_message'])) {
                echo "<script type='text/javascript'>alert('" . $_SESSION['update_message'] . "');</script>";
                unset($_SESSION['update_message']); // Xóa thông báo sau khi đã hiển thị
            }
            ?>

            <!-- Form cập nhật thông tin -->
            <form action="thongtinuser.php" method="POST">
                <div class="form-group">
                    <label for="name">Họ và Tên</label>
                    <input type="text" id="name" name="name" value="<?php echo $username; ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email; ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Số Điện Thoại</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $phone; ?>">
                </div>
                <div class="form-group">
                    <label for="address">Địa Chỉ</label>
                    <input type="text" id="address" name="address" value="<?php echo $address; ?>">
                </div>

                <button type="submit" class="update-btn">Cập Nhật Thông Tin</button>
            </form>

            <!-- Form đăng xuất -->
            <form action="dangxuat.php" method="POST">
                <button type="submit" name="logout" class="logout-btn">Đăng Xuất</button>
            </form>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>
</body>

</html>