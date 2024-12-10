document.addEventListener("DOMContentLoaded", function () {
  document.getElementById("checkoutForm").addEventListener("submit", function (event) {
      event.preventDefault();
      validateForm();
  });
});

function validateForm() {
  var name = document.getElementById("nameInput").value.trim();
  var email = document.getElementById("emailInput").value.trim();
  var phone = document.getElementById("phoneInput").value.trim();
  var password = document.getElementById("passwordInput").value.trim();
  var isValid = true;

  // Kiểm tra tên
  if (name === "") {
      document.getElementById("nameErrorMessage").textContent = "Tên không được để trống.";
      isValid = false;
  } else {
      document.getElementById("nameErrorMessage").textContent = "";
  }

  // Kiểm tra email
  if (!validateEmail(email)) {
      document.getElementById("emailErrorMessage").textContent = "Email không hợp lệ!";
      isValid = false;
  } else {
      document.getElementById("emailErrorMessage").textContent = "";
  }

  // Kiểm tra số điện thoại
  if (!validatePhone(phone)) {
      document.getElementById("phoneErrorMessage").textContent = "Số điện thoại không hợp lệ!";
      isValid = false;
  } else {
      document.getElementById("phoneErrorMessage").textContent = "";
  }

  // Kiểm tra mật khẩu
  if (password === "") {
      document.getElementById("passwordErrorMessage").textContent = "Mật khẩu không được để trống.";
      isValid = false;
  } else {
      document.getElementById("passwordErrorMessage").textContent = "";
  }

  if (isValid) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "dangki.php", true);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

      xhr.onreadystatechange = function () {
          if (xhr.readyState === 4 && xhr.status === 200) {
              var responseText = xhr.responseText.trim(); // Lấy phản hồi dạng chuỗi

              if (responseText.includes("Đăng ký thành công")) {
                  alert("Đăng ký thành công");
                  window.location.href = "dangnhap.php"; // chuyển hướng đến trang đăng nhập
              } else {
                  // Hiển thị thông báo lỗi từ PHP (hiển thị trên form)
                  document.getElementById("nameErrorMessage").textContent = "";
                  document.getElementById("emailErrorMessage").textContent = "";
                  document.getElementById("phoneErrorMessage").textContent = "";
                  document.getElementById("passwordErrorMessage").textContent = "";

                  if (responseText.includes("Tên không được để trống")) {
                      document.getElementById("nameErrorMessage").textContent = "Tên không được để trống.";
                  }
                  if (responseText.includes("Email không được để trống")) {
                      document.getElementById("emailErrorMessage").textContent = "Email không được để trống.";
                  } else if (responseText.includes("Email đã tồn tại.")) {
                      document.getElementById("emailErrorMessage").textContent = "Email đã tồn tại.";
                  }
                  if (responseText.includes("Số điện thoại không được để trống")) {
                      document.getElementById("phoneErrorMessage").textContent = "Số điện thoại không được để trống.";
                  } else if (responseText.includes("Số điện thoại đã tồn tại.")) {
                      document.getElementById("phoneErrorMessage").textContent = "Số điện thoại đã tồn tại.";
                  }
                  if (responseText.includes("Mật khẩu không được để trống")) {
                      document.getElementById("passwordErrorMessage").textContent = "Mật khẩu không được để trống.";
                  }
              }
          }
      };

      var data = `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(password)}`;
      xhr.send(data);
  }

  return false; // Ngừng gửi form nếu không hợp lệ
}
function validateEmail(email) {
  var emailRegex = /\S+@\S+\.\S+/;
  return emailRegex.test(email);
}

function validatePhone(phone) {
  var phoneRegex = /^[0-9]{10,11}$/;
  return phoneRegex.test(phone);
}
