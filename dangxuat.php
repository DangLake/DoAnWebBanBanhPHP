<?php
session_start();
session_unset(); // Hủy tất cả session
session_destroy(); // Hủy session
header('Location: index.php'); // Chuyển hướng đến trang đăng nhập
exit();
?>
