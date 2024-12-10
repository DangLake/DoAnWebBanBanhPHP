<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<?php
include_once('./config.php');
include('./includes/link.php');
?>

<body onload="myFunction()">
    <div id="loader"></div>
    <div id="myDiv" style="display:none;">
        <div class="frm-main">
            <?php include ('./includes/header.php') ?>

            <div class="main_content">
                <div class="banner">
                    <img src="images/background2.png" alt="">
                    <div class="banner-text">
                        <h1>Bánh ngon tiệc vui</h1>
                        <h2>Đón hè rực rỡ</h2>
                    </div>
                </div>
                <div class="mainslider">
                    <div class="slider-wrapper">
                        <button id="prev-slide" class="slide-button material-symbols-rounded">chevron_left</button>
                        <div class="image-list">
                            <?php
                            // Truy vấn lấy 9 ảnh ngẫu nhiên từ các sản phẩm khác nhau
                            $sql = "
                                    SELECT product_id, image
                                    FROM product_images
                                    GROUP BY product_id
                                    ORDER BY RAND()
                                    LIMIT 9
                                ";
                            $stmt = $conn->query($sql);

                            // Kiểm tra nếu có dữ liệu trả về
                            if ($stmt->rowCount() > 0) {
                                // Duyệt qua các ảnh và hiển thị chúng
                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    // Đảm bảo tên ảnh được thoát để tránh các vấn đề bảo mật
                                    $image_url = htmlspecialchars($row['image']);
                                    $product_id = $row['product_id'];  // Lấy product_id để sử dụng trong URL
                                    echo '<a href="banh.php?product_id=' . $product_id . '"><img src="' . $image_url . '" alt="product-image" class="image-item"></a>';
                                }
                            } else {
                                // Nếu không có ảnh nào
                                echo '<p>No images available.</p>';
                            }
                            ?>
                        </div>
                        <button id="next-slide" class="slide-button material-symbols-rounded">chevron_right</button>
                    </div>
                    <div class="slider-scrollbar">
                        <div class="scrollbar-track">
                            <div class="scrollbar-thumb"></div>
                        </div>
                    </div>
                </div>
                <div class="gioithieu_main">
                    <div>
                        <img src="images/gioithieu.png" alt="">
                    </div>
                    <div class="text">
                        <h3>Không chỉ là chiếc bánh, mà là một món quà</h3>
                        <p>Dù bạn là ai, chúng tôi mong rằng, bạn sẽ luôn tìm được chiếc bánh phù hợp với khẩu vị của
                            riêng mình tại VG Cake.</p>
                        <p>Từ chiếc hộp, cây nến, tấm bưu thiệp hay cách chúng tôi trao tới bạn tận tay món quà ấy, đều
                            sẽ
                            được chuẩn bị thật chu đáo.</p>
                    </div>
                </div>
                <div class="monmoi">
                    <div class="main">
                        <h1>Sản Phẩm Mới</h1>
                    </div>
                    <div class="dsSP">
                        <?php
                        // Truy vấn lấy 8 sản phẩm ngẫu nhiên từ bảng products và product_images
                        $sql = "SELECT p.id, p.name, pi.image, p.price 
                                    FROM products p
                                    JOIN product_images pi ON p.id = pi.product_id
                                    GROUP BY p.id
                                    ORDER BY RAND() 
                                    LIMIT 8";  // Lấy 8 sản phẩm ngẫu nhiên
                        $stmt = $conn->query($sql);

                        // Kiểm tra nếu có dữ liệu trả về
                        if ($stmt->rowCount() > 0) {
                            // Duyệt qua các sản phẩm và hiển thị chúng
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                // Đảm bảo tên ảnh được thoát để tránh các vấn đề bảo mật
                                $image_url = htmlspecialchars($row['image']);
                                $product_name = htmlspecialchars($row['name']);
                                $product_price = number_format($row['price'], 0, ',', '.');  // Định dạng giá thành tiền
                                $product_id = $row['id'];  // Lấy product_id

                                echo '<div>
                                            <a href="banh.php?product_id=' . $product_id . '">
                                                <img src="' . $image_url . '" height="400" />
                                                <h2>' . $product_name . '</h2>
                                                <p>' . $product_price . " đ" . '</p>
                                            </a>
                                        </div>';
                            }
                        } else {
                            // Nếu không có sản phẩm nào
                            echo '<p>No products available.</p>';
                        }
                        ?>

                    </div>
                </div>
            </div>
        </div>
        <?php include ('./includes/footer.php') ?>
    </div>
</body>

</html>