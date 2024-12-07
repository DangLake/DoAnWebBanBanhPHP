<?php
require 'config.php'; // Điều chỉnh đường dẫn nếu file nằm trong folder con

$emailError = '';
$passwordError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Kết nối cơ sở dữ liệu
    $conn = connectDatabase();

    // Kiểm tra email và mật khẩu không rỗng
    if (empty($email)) {
        $emailError = 'Vui lòng nhập email.';
    }

    if (empty($password)) {
        $passwordError = 'Vui lòng nhập mật khẩu.';
    }

    if (empty($emailError) && empty($passwordError)) {
        // Truy vấn người dùng từ cơ sở dữ liệu
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kiểm tra mật khẩu (sử dụng password_verify nếu mật khẩu được hash)
            if ($password === $user['password']) { // Thay bằng password_verify($password, $user['password']) nếu dùng hash
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Kiểm tra vai trò và điều hướng
                if ($user['role'] == 0) {
                    // Admin
                    session_unset(); // Hủy tất cả session
                    session_destroy(); // Hủy session

                    echo '<script>alert("Đăng nhập thành công với vai trò Admin!"); window.location.href = "./admin/admin.php";</script>';
                } else {
                    // User
                    echo '<script>alert("Đăng nhập thành công!"); window.location.href = "index.php";</script>';
                }
                exit();
            } else {
                $passwordError = 'Mật khẩu không chính xác.';
            }
        } else {
            $emailError = 'Email không tồn tại.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="styles/dangnhap.css">
</head>

<body>
    <section class="checkout">
        <div class="container">
            <h4>Đăng nhập</h4>
            <form id="checkoutForm" action="dangnhap.php" method="post">
                <div class="row">
                    <div class="checkout__input">
                        <p>Email<span>*</span></p>
                        <input type="text" id="emailInput" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES); ?>">
                        <div class="error-message"><?php echo $emailError; ?></div>
                    </div>
                    <div class="checkout__input">
                        <p>Mật khẩu<span>*</span></p>
                        <input type="password" id="matkhauInput" name="password">
                        <div class="error-message"><?php echo $passwordError; ?></div>
                    </div>
                </div>
                <div class="checkout__order">
                    <button type="submit" class="site-btn">Đăng nhập</button>
                    <p>hoặc</p>
                    <button type="button" class="newcreat-btn" onclick="dangki()">Tạo tài khoản</button>
                </div>

            </form>
        </div>
    </section>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const checkoutForm = document.getElementById("checkoutForm");

            checkoutForm.addEventListener("submit", function(event) {
                const email = document.getElementById("emailInput").value.trim();
                const password = document.getElementById("matkhauInput").value.trim();

                let isValid = true;

                if (!email) {
                    alert("Email không được để trống.");
                    isValid = false;
                } else if (!/\S+@\S+\.\S+/.test(email)) {
                    alert("Vui lòng nhập một địa chỉ email hợp lệ.");
                    isValid = false;
                }

                if (!password) {
                    alert("Mật khẩu không được để trống.");
                    isValid = false;
                }
//dfgdfgdfgdfg
                if (!isValid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>

</html>