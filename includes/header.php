
<header class="header">
    <div class="main_header">
        <div class="header_logo">
            <img src="images/logo.png" alt="">
        </div>
        <div class="header_menu">
            <div class="icon-bar">
                <i class="fa fa-bars" onclick="toggleMenu()"></i>
            </div>
            <ul class="mainmenu">
                <li>
                    <form action="timkiem.php" method="get" class="search-form">
                        <input type="text" name="query" placeholder="Tìm kiếm..." required>
                        <button type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </form>
                </li>
                <li>
                    <a href="index.php" id="home-link">TRANG CHỦ</a>
                </li>
                <div class="menubanh">
                    <div><a href="#">MENU BÁNH</a></div>
                    <span class="menu_danhsach"></span>
                    <?php
                    $conn = connectDatabase();

                    // Truy vấn cơ sở dữ liệu để lấy danh sách các danh mục
                    $sql = "SELECT id, name FROM categories";  // Truy vấn lấy tên và id các danh mục
                    $result = $conn->query($sql);  // Thực hiện truy vấn

                    // Kiểm tra nếu có danh mục trả về
                    if ($result->rowCount() > 0) {
                        // Duyệt qua từng danh mục và hiển thị trên giao diện
                        echo '<ul>';
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo '<li><a onclick="closeMenu()" href="dsBanh.php?category_id=' . htmlspecialchars($row['id']) . '">' . htmlspecialchars($row['name']) . '</a></li>';
                        }
                        echo '</ul>';
                    } else {
                        echo '<ul><li>Không có danh mục nào.</li></ul>';
                    }
                    ?>
                </div>
                <li><a href="tintuc.php">TIN TỨC</a></li>
                <li>
                    <a href="giohang.php" class="icon-link">
                        <i class="fa fa-shopping-cart"></i>
                    </a>
                </li>
                <li>
                    <?php

                    // Kiểm tra xem session có tồn tại không
                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null) {
                        // Nếu người dùng đã đăng nhập, hiển thị icon tài khoản và trang thông tin người dùng
                        echo '<a href="thongtinuser.php" onclick="closeMenu()" class="icon-link">
        <i class="fa fa-user"></i></a>';
                    } else {
                        // Nếu chưa đăng nhập, hiển thị liên kết đăng nhập
                        echo '<a href="dangnhap.php" onclick="closeMenu()" class="icon-link">
        <i class="fa fa-user"></i></a>';
                    }
                    ?>
                </li>
            </ul>
        </div>
    </div>
</header>
