document.addEventListener("DOMContentLoaded", function() {
    const checkoutForm = document.getElementById("checkoutForm");
    const emailInput = document.getElementById("emailInput");
    const matkhauInput = document.getElementById("matkhauInput");

    if (!emailInput || !matkhauInput) {
        console.error("Không tìm thấy phần tử emailInput hoặc matkhauInput");
        return; // Dừng lại nếu không tìm thấy phần tử
    }

    checkoutForm.addEventListener("submit", function(event) {
        const email = emailInput.value.trim();
        const password = matkhauInput.value.trim();

        let isValid = true;

        // Kiểm tra email
        if (!email) {
            alert("Email không được để trống.");
            isValid = false;
        } else if (!/\S+@\S+\.\S+/.test(email)) {
            alert("Vui lòng nhập một địa chỉ email hợp lệ.");
            isValid = false;
        }

        // Kiểm tra mật khẩu
        if (!password) {
            alert("Mật khẩu không được để trống.");
            isValid = false;
        }

        // Nếu không hợp lệ, ngừng gửi form
        if (!isValid) {
            event.preventDefault();
        }
    });
    const createAccountBtn = document.getElementById("createAccountBtn");

    if (createAccountBtn) {
        createAccountBtn.addEventListener("click", function() {
            window.location.href = "dangki.php";
        });
    }
});
