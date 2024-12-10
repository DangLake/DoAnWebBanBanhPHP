<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : "The CakeShop"; ?></title>
    <link rel="stylesheet" href="styles/giaohang.css">
    <script src="scripts/giaohang.js"></script>
</head>
<body>


<section class="checkout">
    <div class="container">
        <h2>THÔNG TIN THANH TOÁN</h2>
        <form id="checkoutForm" action="#">
            <div class="row">
                <div class="checkout__input">
                    <p>Họ và tên<span>*</span></p>
                    <input type="text" id="HotenInput">
                    <div class="error-message" id="hotenErrorMessage"></div>
                </div>
                <div class="checkout__input">
                    <p>Số điện thoại<span>*</span></p>
                    <input type="text" id="sdtInput">
                    <div class="error-message" id="sdtErrorMessage"></div>
                </div>
                <div class="checkout__input">
                    <p>Email<span>*</span></p>
                    <input type="text" id="emailInput">
                    <div class="error-message" id="emailErrorMessage"></div>
                </div>
                <div class="checkout__input">
                    <p>Tỉnh/thành phố<span>*</span></p>
                    <input type="text" id="tinhInput">
                    <div class="error-message" id="tinhErrorMessage"></div>
                </div>
                <div class="checkout__input">
                    <p>Quận huyện<span>*</span></p>
                    <input type="text" id="quanInput">
                    <div class="error-message" id="quanErrorMessage"></div>
                </div>
                <div class="checkout__input">
                    <p>Địa chỉ<span>*</span></p>
                    <input type="text" id="diachiInput">
                    <div class="error-message" id="diachiErrorMessage"></div>
                </div>
            </div>
            <div class="checkout__input__checkbox">
                <label for="diff-acc">
                    <input type="checkbox" id="diff-acc">
                    Giao tới địa chỉ khác
                    <span class="checkmark"></span>
                </label>
            </div>
        </form>

        <h2>THÔNG TIN ĐƠN HÀNG</h2>
        <div class="ttdh">
            <div class="ttcontent">
                <h4>Sản phẩm</h4>
                <p>Soft Glow - Flan Gato Mè Đen Matcha - 20cm </p>
            </div>
            <div class="ttcontent">
                <h4>SL</h4>
                <p>1</p>
            </div>
            <div class="ttcontent">
                <h4>Tổng</h4>
                <p>525.000 đ</p>
            </div>
            <div class="ttcontent">
                <h4>Xóa</h4>
                <a href="#">X</a>
            </div>
        </div>

        <div class="tinhtien">
            <div class="tinh tam">
                <h4>Tạm tính</h4>
                <p>525.000 đ</p>
            </div>
            <div class="tinh giao">
                <h4>Giao hàng</h4>
                <p>30.000 đ</p>
            </div>
            <div class="tinh tong">
                <h4>Tổng</h4>
                <p>555.000 đ</p>
            </div>
        </div>

        <div class="checkout__order">
            <button type="button" class="site-btn" onclick="validateForm1()">ĐẶT HÀNG</button>
        </div>
    </div>
</section>

