<?php
session_start();
require('../config.php');

// Kết nối cơ sở dữ liệu
$conn = connectDatabase();

// Hàm kiểm tra trùng tên loại bánh
function isDuplicateCategoryName($conn, $category_name, $category_id = null) {
    $sql = "SELECT COUNT(*) FROM categories WHERE name = :category_name";
    if ($category_id !== null) {
        $sql .= " AND id != :category_id";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':category_name', $category_name);
    if ($category_id !== null) {
        $stmt->bindParam(':category_id', $category_id);
    }
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

// Xử lý thêm loại bánh
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);

    if (isDuplicateCategoryName($conn, $category_name)) {
        echo '<script>alert("Loại bánh đã tồn tại");</script>';
    } else {
        try {
            $sql = "INSERT INTO categories (name) VALUES (:category_name)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':category_name', $category_name);
            $stmt->execute();

            header("Location: cata.php");
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Xử lý xóa loại bánh
if (isset($_GET['delete'])) {
    $category_id = $_GET['delete'];

    try {
        $sql = "DELETE FROM categories WHERE id = :category_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        header("Location: cata.php");
        exit();
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Xử lý chỉnh sửa loại bánh
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'];
    $category_name = trim($_POST['category_name']);

    if (isDuplicateCategoryName($conn, $category_name, $category_id)) {
        echo '<script>alert("Loại bánh đã tồn tại");</script>';
    } else {
        try {
            $sql = "UPDATE categories SET name = :category_name WHERE id = :category_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':category_name', $category_name);
            $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
            $stmt->execute();

            header("Location: cata.php");
            exit();
        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}

// Lấy danh sách loại bánh
try {
    $sql = "SELECT * FROM categories";
    $stmt = $conn->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf8mb4">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Loại Bánh</title>
    <script src="./admin.js"></script>
    <link rel="stylesheet" href="./admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
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

        <!-- Main Content -->
        <main class="main-content">
            <section id="categories">
                <h1>Quản Lý Loại Bánh</h1>

                <!-- Thông báo lỗi -->
                <?php if (!empty($error_message)): ?>
                    <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
                <?php endif; ?>

                <!-- Form thêm loại bánh -->
                <form action="cata.php" method="POST">
                    <label for="category_name">Tên Loại Bánh:</label>
                    <input type="text" id="category_name" name="category_name" required>
                    <button type="submit" name="add_category" class="btn">Thêm Loại Bánh</button>
                </form>

                <!-- Form chỉnh sửa loại bánh -->
                <div id="edit-form" style="display:none;">
                    <h3>Chỉnh Sửa Loại Bánh</h3>
                    <form action="cata.php" method="POST">
                        <input type="hidden" id="category_id" name="category_id">
                        <label for="edit_category_name">Tên Loại Bánh:</label>
                        <input type="text" id="edit_category_name" name="category_name" required>
                        <button type="submit" name="edit_category" class="btn">Cập Nhật Loại Bánh</button>
                    </form>
                </div>

                <!-- Danh sách loại bánh -->
                <h2>Danh Sách Các Loại Bánh</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Loại Bánh</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (!empty($categories)) {
                            foreach ($categories as $row) {
                                echo "<tr>
                                        <td>" . htmlspecialchars($row["id"], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>" . htmlspecialchars($row["name"], ENT_QUOTES, 'UTF-8') . "</td>
                                        <td>
                                            <button class='btn' onclick='editCategory(" . $row["id"] . ", \"" . addslashes($row["name"]) . "\")'>Sửa</button>
                                            <a href='cata.php?delete=" . $row["id"] . "' class='btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa loại bánh này?\")'>Xóa</a>
                                        </td>
                                    </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3'>Không có loại bánh nào</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
