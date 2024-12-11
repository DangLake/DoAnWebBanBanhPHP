<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Đảm bảo đã cài PHPMailer qua Composer

function sendOrderConfirmationEmail($full_name, $phone, $email, $address, $district, $city, $cart_items, $subtotal, $shipping_fee, $total) {
    $mail = new PHPMailer(true);

    try {
        // Cấu hình email
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // SMTP của Gmail
        $mail->SMTPAuth = true;
        $mail->Username = 'vthgiang181003@gmail.com'; // Thay bằng email của bạn
        $mail->Password = 'mmwg mjrr xkzi kjhq'; // Thay bằng mật khẩu của bạn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Người gửi và người nhận
        $mail->setFrom('vthgiang181003@gmail.com', 'VGCAKE');
        $mail->addAddress($email, $full_name);

        // Nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Xac nhan don hang tu VGCake';
        $mailContent = "<h1>Cảm ơn bạn đã đặt hàng tại VGCake</h1>";
        $mailContent .= "<p>Họ tên: $full_name</p>";
        $mailContent .= "<p>Điện thoại: $phone</p>";
        $mailContent .= "<p>Địa chỉ: $address, $district, $city</p>";
        $mailContent .= "<h2>Chi tiết đơn hàng</h2>";
        $mailContent .= "<table border='1' cellpadding='5'>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SL</th>
                                <th>Tổng</th>
                            </tr>";
        foreach ($cart_items as $item) {
            $item_total = $item['quantity'] * $item['price'];
            $mailContent .= "<tr>
                                <td>" . htmlspecialchars($item['name']) . "</td>
                                <td>" . htmlspecialchars($item['quantity']) . "</td>
                                <td>" . number_format($item_total, 0, ',', '.') . " đ</td>
                             </tr>";
        }
        $mailContent .= "</table>";
        $mailContent .= "<p>Tạm tính: " . number_format($subtotal, 0, ',', '.') . " đ</p>";
        $mailContent .= "<p>Phí giao hàng: " . number_format($shipping_fee, 0, ',', '.') . " đ</p>";
        $mailContent .= "<p><strong>Tổng cộng: " . number_format($total, 0, ',', '.') . " đ</strong></p>";

        $mail->Body = $mailContent;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
<?php
session_start();

include_once './config.php';
include('./includes/link.php');
// Kiểm tra khi form được gửi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nhận dữ liệu từ POST
    $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : null;
    $cart_items = isset($_POST['cart_items']) ? json_decode($_POST['cart_items'], true) : [];
    $full_name = isset($_POST['full_name']) ? $_POST['full_name'] : null;
    $phone = isset($_POST['phone']) ? $_POST['phone'] : null;
    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $city = isset($_POST['city']) ? $_POST['city'] : null;
    $district = isset($_POST['district']) ? $_POST['district'] : null;
    $address = isset($_POST['address']) ? $_POST['address'] : null;

    if (empty($cart_items)) {
        echo "<script>
                alert('Giỏ hàng trống');
            </script>";
    }

    // Tính toán tổng giá trị đơn hàng
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['quantity'] * $item['price'];
    }
    $shipping_fee = 30000; // Phí giao hàng cố định
    $total = $subtotal + $shipping_fee;

    // Gửi email xác nhận đơn hàng
    $isSent = sendOrderConfirmationEmail($full_name, $phone, $email, $address, $district, $city, $cart_items, $subtotal, $shipping_fee, $total);

    if ($isSent) {
        echo "<script>
                alert('Đặt hàng thành công! Email xác nhận đã được gửi.');
                window.location.href = 'index.php';
            </script>";
    } else {
        echo "<script>
                alert('Đặt hàng thành công nhưng không thể gửi email.');
            </script>";
    }
}
?>

