var logoutBtn = document.querySelector('.logout-btn');
if (logoutBtn) {
    logoutBtn.addEventListener('click', function(event) {
        event.preventDefault(); // Ngừng hành động mặc định

        // Gửi yêu cầu AJAX đến logout.php để đăng xuất
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'logout.php', true); // Gửi yêu cầu đến logout.php
        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 400) {
                // Sau khi đăng xuất, chuyển hướng đến trang đăng nhập
                window.location.href = "index.php?page=dangnhap.php";
            } else {
                console.error('Không thể đăng xuất');
            }
        };
        xhr.send();
    });
}