
function validateForm1() {
    var hoten = document.getElementById('HotenInput').value.trim();
    var sdt = document.getElementById('sdtInput').value.trim();
    var email = document.getElementById('emailInput').value.trim();
    var tinh = document.getElementById('tinhInput').value.trim();
    var quan = document.getElementById('quanInput').value.trim();
    var diachi = document.getElementById('diachiInput').value.trim();

    var isValid = true;

    // Validate họ tên
    if (hoten === "") {
        document.getElementById('hotenErrorMessage').textContent = 'Vui lòng nhập họ tên của bạn.';
        isValid = false;
    } else {
        document.getElementById('hotenErrorMessage').textContent = "";
    }

    // Validate số điện thoại
    if (sdt === "") {
        document.getElementById('sdtErrorMessage').textContent = 'Vui lòng nhập số điện thoại của bạn.';
        isValid = false;
    } else {
        if (isNaN(sdt)) {
            document.getElementById('sdtErrorMessage').textContent = 'Vui lòng nhập số điện thoại là số.';
            isValid = false;
        } else {
            if (sdt.startsWith('0') == false) {
                document.getElementById('sdtErrorMessage').textContent = 'sdt phai bat dau = 0.'
                isValid = false;
            }
            else {
                if (sdt.length != 10) {
                    document.getElementById('sdtErrorMessage').textContent = 'Vui lòng nhập số điện thoại có 10 số.';
                    isValid = false;
                } else {
                    document.getElementById('sdtErrorMessage').textContent = "";
                }
            }
        }
    }

    // Validate email
    if (!validateEmail(email)) {
        document.getElementById('emailErrorMessage').textContent = 'Vui lòng nhập một địa chỉ email hợp lệ.';
        isValid = false;
    } else {
        document.getElementById('emailErrorMessage').textContent = "";
    }

    // Validate tỉnh/thành phố
    if (tinh === "") {
        document.getElementById('tinhErrorMessage').textContent = 'Vui lòng nhập tỉnh/thành phố của bạn.';
        isValid = false;
    } else {
        document.getElementById('tinhErrorMessage').textContent = "";
    }

    // Validate quận/huyện
    if (quan === "") {
        document.getElementById('quanErrorMessage').textContent = 'Vui lòng nhập quận huyện của bạn.';
        isValid = false;
    } else {
        document.getElementById('quanErrorMessage').textContent = "";
    }

    // Validate địa chỉ
    if (diachi === "") {
        document.getElementById('diachiErrorMessage').textContent = 'Vui lòng nhập địa chỉ của bạn.';
        isValid = false;
    } else {
        document.getElementById('diachiErrorMessage').textContent = "";
    }

    // Nếu hợp lệ, thông báo thành công và chuyển hướng về trang chủ
    if (isValid) {
        alert('Đặt hàng thành công!');
        window.location.href = 'main.html';// chuyển hướng về trang chủ
    }
}

function validateEmail(email) {
    var emailRegex = /\S+@\S+\.\S+/;
    return emailRegex.test(email);
}
//     : Dấu gạch chéo mở và đóng để bắt đầu và kết thúc biểu thức chính quy.
//      \S+: Ký tự \S đại diện cho bất kỳ ký tự nào không phải là khoảng trắng. Dấu + có nghĩa là phải có ít nhất một hoặc nhiều ký tự không phải khoảng trắng trước ký tự @.
//       @: Dấu @ là bắt buộc trong mọi địa chỉ email.
//      \S+: Tương tự như trên, yêu cầu ít nhất một hoặc nhiều ký tự không phải khoảng trắng sau ký tự @ và trước dấu ..
//      .: Dấu chấm . phải có trong email (phân tách tên miền và phần mở rộng tên miền).
//       \S+: Yêu cầu ít nhất một hoặc nhiều ký tự không phải khoảng trắng sau dấu chấm.
