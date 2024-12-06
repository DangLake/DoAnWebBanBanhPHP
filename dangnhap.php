<?php
include './config.php';

$emailError = '';
$passwordError = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

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
        // Lấy thông tin người dùng từ CSDL
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Kiểm tra mật khẩu trực tiếp
            if ($password === $user['password']) {
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                // Đăng nhập thành công và thông báo bằng alert
                echo '<script>alert("Đăng nhập thành công!"); window.location.href = "index.php";</script>';
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
            <form id="checkoutForm" action="dangnhap.php" method="post" onsubmit="return validateForm()">
                <div class="row">
                    <div class="checkout__input">
                        <p>Email<span>*</span></p>
                        <input type="text" id="emailInput" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
                        <div class="error-message" id="emailErrorMessage"><?php echo $emailError; ?></div>
                    </div>
                    <div class="checkout__input">
                        <p>Mật khẩu<span>*</span></p>
                        <input type="password" id="matkhauInput" name="password">
                        <div class="error-message" id="matkhauErrorMessage"><?php echo $passwordError; ?></div>
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
            document
                .getElementById("checkoutForm")
                .addEventListener("submit", function(event) {
                    event.preventDefault();
                    validateForm();
                });
        });

        function validateForm() {
            var email = emailInput.value.trim();
            var matkhau = matkhauInput.value.trim();
            var isValid = true;

            if (!email) {
                emailErrorMessage.textContent = "Email không được để trống.";
                isValid = false;
            } else {
                emailErrorMessage.textContent = "";
            }

            if (!matkhau) {
                matkhauErrorMessage.textContent = "Mật khẩu không được để trống.";
                isValid = false;
            } else {
                matkhauErrorMessage.textContent = "";
            }

            // Kiểm tra định dạng email
            if (email && !validateEmail(email)) {
                emailErrorMessage.textContent = "Vui lòng nhập một địa chỉ email hợp lệ.";
                isValid = false;
            }

            // Kiểm tra định dạng mật khẩu
            if (matkhau && !validatePassword(matkhau)) {
                matkhauErrorMessage.textContent = "Mật khẩu phải chứa ít nhất 8 ký tự, bao gồm chữ hoa, chữ thường, số và ký tự đặc biệt.";
                isValid = false;
            }

            if (isValid) {
                checkoutForm.submit();
            }
        }

        function validateEmail(email) {
            var emailRegex = /\S+@\S+\.\S+/;
            return emailRegex.test(email);
        }

        function validatePassword(password) {
            var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d])(?=.*[\W_]).{8,}$/;
            return passwordRegex.test(password);
        }
    </script>
</body>

</html>