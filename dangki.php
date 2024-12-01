<?php
include './config.php';
header('Content-Type: application/json; charset=utf-8');
// error_reporting(0);
// ini_set('display_errors', 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';
    $conn = connectDatabase();

    // Kiểm tra email hoặc số điện thoại đã tồn tại
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Nếu email đã tồn tại
        if ($user['email'] === $email) {
            echo json_encode(['status' => 'error', 'message' => 'Email đã tồn tại.']);
        }
        // Nếu số điện thoại đã tồn tại
        else if ($user['phone'] === $phone) {
            echo json_encode(['status' => 'error', 'message' => 'Số điện thoại đã tồn tại.']);
        }
    } else {
        // Thêm người dùng mới vào cơ sở dữ liệu
        $stmt = $conn->prepare("INSERT INTO users (username, phone, email, password, role) VALUES (:name, :phone, :email, :password, '1')");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        // Trả về phản hồi thành công
        echo json_encode(['status' => 'success', 'message' => 'Đăng ký thành công']);
    }
    exit;
}
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="styles/dangki.css">
</head>

<body>
    <section class="checkout">
        <div class="container">
            <h4>Đăng ký tài khoản</h4>
            <form id="checkoutForm" action="#" method="post" onsubmit="return validateForm()">
                <div class="row">
                    <div class="checkout__input">
                        <p>Họ tên<span>*</span></p>
                        <input type="text" id="nameInput" name="name">
                        <div class="error-message" id="nameErrorMessage"></div>
                    </div>
                    <div class="checkout__input">
                        <p>Email<span>*</span></p>
                        <input type="email" id="emailInput" name="email">
                        <div class="error-message" id="emailErrorMessage"></div>
                    </div>
                    <div class="checkout__input">
                        <p>Số điện thoại<span>*</span></p>
                        <input type="text" id="phoneInput" name="phone">
                        <div class="error-message" id="phoneErrorMessage"></div>
                    </div>
                    <div class="checkout__input">
                        <p>Mật khẩu<span>*</span></p>
                        <input type="password" id="passwordInput" name="password">
                        <div class="error-message" id="passwordErrorMessage"></div>
                    </div>
                </div>
                <div class="checkout__order">
                    <button type="submit" class="site-btn">Đăng ký</button>
                </div>
            </form>
        </div>
    </section>
    <script src="scripts/dangki.js" defer></script>
</body>

</html>
