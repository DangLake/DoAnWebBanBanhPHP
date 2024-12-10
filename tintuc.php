<!DOCTYPE html>
<html lang="en">
<?php include('./config.php');
include('./includes/link.php'); ?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : "Trang tin tức"; ?></title>
    <link rel="stylesheet" href="styles/tintuc.css">
    <link rel="stylesheet" href="styles/main.css">
</head>

<body>
    <div class="frm-main">
        <?php
        include('./includes/header.php')
        ?>
        <div class="banner">
            <img src="images/bnTintuc.jpg" alt="">
        </div>

        <div class="main">
            <!-- Giới thiệu CakeShop -->
            <div class="gioithieu">
                <div class="tieude">
                    <p>The VG Cake</p>
                </div>
                <div class="content">
                    <p>
                        Ở tiệm bánh The VG Cake, mỗi chiếc bánh là một câu chuyện riêng với hơi thở và tinh thần
                        chẳng thể lẫn vào đâu được.
                    </p>
                    <p>
                        Chúng mình – những người thợ làm bánh thủ công luôn cố gắng tôn trọng tính nguyên bản,
                        tự nhiên và chân thật nhất của từng nguyên liệu. Để dù có những bất toàn trong mỗi
                        thứ riêng rẽ nhưng sau cùng vẫn tạo nên một ổ bánh hài hòa và ngon nhất.
                    </p>
                    <p>
                        Vậy nên, bất cứ khi nào bạn cần những hương vị mộc mạc, tinh tế nhưng chẳng kém phần
                        hấp dẫn, hãy đến với chúng mình. Luôn có rất nhiều những điều đặc biệt đợi bạn khám
                        phá đó nhé!
                    </p>
                </div>
            </div>

            <!-- Thông tin liên hệ -->
            <div class="gioithieu">
                <div class="tieude">
                    <p>Thông tin liên hệ</p>
                </div>
                <div class="content">
                    <p>Hotline: <a href="#">1900 2727</a></p>
                    <p>Giải quyết khiếu nại: <a href="#">0947 937 893</a></p>
                    <p>Email: contact@thevgcake.com</p>
                    <p>
                        Chi nhánh 1: 180 Cao Lỗ, Phường 4, Quận 8, TPHCM<br>
                        Chi nhánh 2: 36 Nguyễn Thị Thập, Phường Bến Thành, Quận 5, TPHCM<br>
                        Chi nhánh 3: 07-09 Hoa Hồng, Phường 02, Quận 7, TPHCM
                    </p>
                    <p>Ngày làm việc: Tất cả các ngày trong tuần</p>
                    <p>Giờ làm việc: 09h00 – 21h30</p>
                </div>
            </div>

            <!-- Bản đồ -->
            <div class="gioithieu">
                <div class="tieude">
                    <p>Bản đồ</p>
                </div>
                <div class="content">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1959.9763501350733!2d106.67657813509197!3d10.738128999061278!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31752f62a90e5dbd%3A0x674d5126513db295!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBDw7RuZyBuZ2jhu4cgU8OgaSBHw7Ju!5e0!3m2!1svi!2s!4v1731993711912!5m2!1svi!2s" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>

        </div>
       
    </div>
    <?php include('./includes/footer.php') ?>
</body>