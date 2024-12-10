<?php
session_start();
require('../config.php');

// Kết nối cơ sở dữ liệu
$conn = connectDatabase();

// Hàm kiểm tra trùng tên người dùng
function isDuplicateUsername($conn, $username, $user_id = null)
{
    $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
    if ($user_id !== null) {
        $sql .= " AND id != :user_id";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':username', $username);
    if ($user_id !== null) {
        $stmt->bindParam(':user_id', $user_id);
    }
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Xử lý xóa người dùng
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];

    // Kiểm tra nếu người dùng có tồn tại
    $checkUserSql = "SELECT COUNT(*) FROM users WHERE id = :id";
    $checkStmt = $conn->prepare($checkUserSql);
    $checkStmt->bindParam(':id', $user_id);
    $checkStmt->execute();
    if ($checkStmt->fetchColumn() == 0) {
        $_SESSION['message'] = 'Người dùng không tồn tại!';
    } else {
        // Câu lệnh xóa người dùng
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $user_id); // Đảm bảo bind tham số đúng với SQL

        if ($stmt->execute()) {
            $_SESSION['message'] = '';
        } else {
            $_SESSION['message'] = 'Lỗi: Không thể xóa người dùng.';
        }
    }
    header("Location: user.php");
    exit();
}

// Phân trang
$users_per_page = 7;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$offset = ($current_page - 1) * $users_per_page;

$sql = "SELECT * FROM users WHERE role = 1 LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $users_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$total_users_sql = "SELECT COUNT(*) FROM users WHERE role = 1";
$total_users = $conn->query($total_users_sql)->fetchColumn();
$total_pages = ceil($total_users / $users_per_page);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng</title>
    <link rel="stylesheet" href="./admin.css">
</head>

<body>
    <div class="admin-container">
        <aside class="sidebar">
            <h2>Admin Panel</h2>
            <nav>
                <ul>
                    <li><a href="./admin.php">Bánh</a></li>
                    <li><a href="./cata.php">Loại bánh</a></li>
                    <li><a href="./user.php">Người dùng</a></li>
                </ul>
            </nav>
        </aside>
        <main class="main-content">
            <section id="users">
                <h1>Quản Lý Người Dùng</h1>

                <!-- Hiển thị thông báo -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert">
                        <?= $_SESSION['message'] ?>
                    </div>
                    <?php unset($_SESSION['message']); ?>
                <?php endif; ?>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên tài khoản</th>
                            <th>Email</th>
                            <th>Điện thoại</th>
                            <th>Địa chỉ</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td><?= htmlspecialchars($user['address']) ?></td>
                                <td>
                                    <a href="user.php?delete=<?= $user['id'] ?>" class="btn" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')">Xóa</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <!-- Phân trang -->
                <nav class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>" class="btn <?= $i == $current_page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </nav>
            </section>
        </main>
    </div>
</body>

</html>
