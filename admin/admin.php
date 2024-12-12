<?php
session_start();
require('../config.php');

// Kết nối cơ sở dữ liệu
$conn = connectDatabase();

// Hàm kiểm tra trùng tên sản phẩm
function isDuplicateProductName($conn, $product_name, $product_id = null)
{
    $sql = "SELECT COUNT(*) FROM products WHERE name = :product_name";
    if ($product_id !== null) {
        $sql .= " AND id != :product_id";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_name', $product_name);
    if ($product_id !== null) {
        $stmt->bindParam(':product_id', $product_id);
    }
    $stmt->execute();
    return $stmt->fetchColumn() > 0;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description']; // Lấy dữ liệu từ form

    if (isDuplicateProductName($conn, $product_name)) {
        echo '<script>alert("Sản phẩm đã tồn tại");</script>';
    } else {
        // Thêm sản phẩm vào bảng products
        $sql = "INSERT INTO products (name, price, category_id, description) 
                VALUES (:product_name, :price, :category_id, :description)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':description', $description); // Gắn giá trị cho description
        
        if ($stmt->execute()) {
            $product_id = $conn->lastInsertId(); // Lấy ID của sản phẩm vừa thêm vào
            
            // Xử lý ảnh tải lên
            if (isset($_FILES['images']) && $_FILES['images']['error'][0] == 0) {
                $uploads_dir = __DIR__ . '/../images/';
                if (!is_dir($uploads_dir)) {
                    mkdir($uploads_dir, 0777, true);
                }

                foreach ($_FILES['images']['name'] as $key => $image_name) {
                    $image_tmp = $_FILES['images']['tmp_name'][$key];
                    $image_relative_path = 'images/' . basename($image_name); // Đường dẫn tương đối
                    $image_absolute_path = __DIR__ . '/../' . $image_relative_path; // Đường dẫn tuyệt đối
                
                    if ($_FILES['images']['error'][$key] == 0) {
                        // Di chuyển file ảnh từ tạm vào thư mục đích
                        if (move_uploaded_file($image_tmp, $image_absolute_path)) {
                            // Lưu đường dẫn tương đối vào cơ sở dữ liệu
                            $sql = "INSERT INTO product_images (product_id, image) VALUES (:product_id, :image)";
                            $stmt = $conn->prepare($sql);
                            $stmt->bindParam(':product_id', $product_id);
                            $stmt->bindParam(':image', $image_relative_path); // Lưu đường dẫn tương đối
                            $stmt->execute();
                        } else {
                            echo "Error moving uploaded file.";
                        }
                    } else {
                        echo "Error uploading image: " . $_FILES['images']['error'][$key];
                    }
                }                
            }

            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}




// Xử lý chỉnh sửa sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_product'])) {
    $product_id = $_POST['product_id'];
    $product_name = $_POST['product_name'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $description = $_POST['description'];
    if (isDuplicateProductName($conn, $product_name, $product_id)) {
        echo '<script>alert("Sản phẩm đã tồn tại");</script>';
    } else {
        $sql = "UPDATE products SET name = :product_name, price = :price, category_id = :category_id WHERE id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_name', $product_name);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':category_id', $category_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->bindParam(':description', $description);
        if ($stmt->execute()) {
            header("Location: admin.php");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}

// Xử lý xóa sản phẩm
// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];

    // Lấy các ảnh liên quan đến sản phẩm
    $sql = "SELECT image FROM product_images WHERE product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    // Xóa ảnh trong thư mục
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $image_path = __DIR__ . '/../' . $row['image'];  // Đảm bảo đường dẫn tuyệt đối

        // Kiểm tra nếu ảnh tồn tại và xóa
        if (file_exists($image_path)) {
            if (unlink($image_path)) {
                echo "Xóa ảnh thành công: $image_path<br>";
            } else {
                echo "Không thể xóa ảnh: $image_path<br>";
            }
        } else {
            echo "Ảnh không tồn tại: $image_path<br>";
        }
    }

    // Xóa các bản ghi ảnh trong bảng product_images
    $sql = "DELETE FROM product_images WHERE product_id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();

    // Xóa sản phẩm trong bảng products
    $sql = "DELETE FROM products WHERE id = :product_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':product_id', $product_id);

    if ($stmt->execute()) {
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }
}



// Phân trang
$products_per_page = 7; // Số sản phẩm trên mỗi trang
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$offset = ($current_page - 1) * $products_per_page;

// Lấy sản phẩm với phân trang
$sql = "SELECT p.id, p.name AS product_name, p.price, c.name AS category_name, c.id AS category_id 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LIMIT :limit OFFSET :offset";
$stmt = $conn->prepare($sql);
$stmt->bindValue(':limit', $products_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Lấy tổng số sản phẩm
$total_products_sql = "SELECT COUNT(*) FROM products";
$total_products = $conn->query($total_products_sql)->fetchColumn();
$total_pages = ceil($total_products / $products_per_page);

// Lấy danh mục
$category_sql = "SELECT * FROM categories";
$category_stmt = $conn->query($category_sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm</title>
    <script src="./admin.js"></script>
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
            <section id="products">
                <h1>Quản Lý Bánh</h1>

                <!-- Form thêm sản phẩm -->
                <form action="admin.php" method="POST" enctype="multipart/form-data">
                    <label for="product_name">Tên bánh:</label>
                    <input type="text" id="product_name" name="product_name" required>

                    <label for="price">Giá:</label>
                    <input type="number" id="price" name="price" required>
                    <label for="description">Mô tả:</label>
                    <input type="text" id="description" name="description" required>
                    <label for="category_id">Loại bánh:</label>
                    <select name="category_id" id="category_id">
                        <?php
                        while ($category = $category_stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                        ?>
                    </select>
                    <label for="images">Ảnh sản phẩm:</label>
                    <input type="file" name="images[]" id="images" multiple>
                    <button type="submit" name="add_product" class="btn">Thêm</button>
                </form>

                <!-- Form chỉnh sửa sản phẩm -------->
                <div id="edit-form" style="display:none;">
                    <h3>Chỉnh Sửa Sản Phẩm</h3>
                    <form action="admin.php" method="POST">
                        <input type="hidden" id="edit_product_id" name="product_id">
                        <label for="edit_product_name">Tên bánh:</label>
                        <input type="text" id="edit_product_name" name="product_name" required>
                        <label for="edit_price">Giá:</label>
                        <input type="number" id="edit_price" name="price" required>
                        <label for="edit_category_id">Loại bánh:</label>
                        <select name="category_id" id="edit_category_id">
                            <?php
                            $category_stmt = $conn->query($category_sql);
                            while ($category = $category_stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') . "</option>";
                            }
                            ?>
                        </select>
                        <button type="submit" name="edit_product" class="btn">Cập Nhật</button>
                    </form>
                </div>
                <h2>Danh Sách Các Sản Phẩm</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên Bánh</th>
                            <th>Giá</th>
                            <th>Loại Bánh</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['product_name']}</td>
                                        <td>{$row['price']} đ</td>
                                        <td>{$row['category_name']}</td>
                                     <td>
                             <button class='btn' onclick='editProduct({$row["id"]}, \"" . addslashes($row["product_name"]) . "\", {$row["price"]}, {$row["category_id"]})'>Sửa</button>
                            <a href='admin.php?delete={$row["id"]}' class='btn' onclick='return confirm(\"Bạn có chắc chắn muốn xóa sản phẩm này?\")'>Xóa</a>
            </td>
        </tr>";
                        }
                        ?>
                    </tbody>

                </table>

                <!-- Phân trang -->
                <nav class="pagination">
                    <?php if ($current_page > 1): ?>
                        <a href="?page=<?php echo $current_page - 1; ?>" class="btn">Trang Trước</a>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="btn <?php echo $i == $current_page ? 'active' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                    <?php if ($current_page < $total_pages): ?>
                        <a href="?page=<?php echo $current_page + 1; ?>" class="btn">Trang Sau</a>
                    <?php endif; ?>
                </nav>
            </section>
        </main>
    </div>

</body>

</html>