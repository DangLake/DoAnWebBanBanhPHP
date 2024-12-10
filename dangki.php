<?php
include './config.php';
include './includes/link.php';

$nameError = '';
$emailError = '';
$phoneError = '';
$passwordError = '';
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn = connectDatabase();

    // Kiểm tra nếu các trường bắt buộc trống
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        if (empty($name)) {
            $nameError = 'Tên không được để trống.';
        }
        if (empty($email)) {
            $emailError = 'Email không được để trống.';
        }
        if (empty($phone)) {
            $phoneError = 'Số điện thoại không được để trống.';
        }
        if (empty($password)) {
            $passwordError = 'Mật khẩu không được để trống.';
        }
    } else {
        // Kiểm tra email hoặc số điện thoại đã tồn tại
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['email'] === $email) {
                $emailError = 'Email đã tồn tại.';
            } elseif ($user['phone'] === $phone) {
                $phoneError = 'Số điện thoại đã tồn tại.';
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
            $successMessage = 'Đăng ký thành công';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="styles/dangki.css">
    <link rel="stylesheet" href="styles/main.css">
    <script src="scripts/dangki.js"></script>
</head>
<body>
    <div class="frm-main">
        <?php include './includes/header.php' ?>
        <section class="checkout">
            <div class="container">
                <h4>Đăng ký tài khoản</h4>
                <form id="checkoutForm" action="dangki.php" method="post" onsubmit="return validateForm()">
                    <div class="row">
                        <div class="checkout__input">
                            <p>Họ tên<span>*</span></p>
                            <input type="text" id="nameInput" name="name" value="<?php echo htmlspecialchars($name ?? ''); ?>">
                            <div class="error-message" id="nameErrorMessage"><?php echo $nameError; ?></div>
                        </div>
                        <div class="checkout__input">
                            <p>Email<span>*</span></p>
                            <input type="email" id="emailInput" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
                            <div class="error-message" id="emailErrorMessage"><?php echo $emailError; ?></div>
                        </div>
                        <div class="checkout__input">
                            <p>Số điện thoại<span>*</span></p>
                            <input type="text" id="phoneInput" name="phone" value="<?php echo htmlspecialchars($phone ?? ''); ?>">
                            <div class="error-message" id="phoneErrorMessage"><?php echo $phoneError; ?></div>
                        </div>
                        <div class="checkout__input">
                            <p>Mật khẩu<span>*</span></p>
                            <input type="password" id="passwordInput" name="password" value="<?php echo htmlspecialchars($password ?? ''); ?>">
                            <div class="error-message" id="passwordErrorMessage"><?php echo $passwordError; ?></div>
                        </div>
                    </div>
                    <div class="checkout__order">
                        <button type="submit" class="site-btn">Đăng ký</button>
                    </div>
                    <?php if ($successMessage) : ?>
                        <div class="success-message"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                </form>
            </div>
        </section>
    </div>
    <?php include './includes/footer.php' ?>
</body>
</html>
