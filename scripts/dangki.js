document.addEventListener("DOMContentLoaded", function () {
    document
      .getElementById("checkoutForm")
      .addEventListener("submit", function (event) {
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
      document.getElementById("nameErrorMessage").textContent =
        "Tên không được để trống.";
      isValid = false;
    } else {
      document.getElementById("nameErrorMessage").textContent = "";
    }
  
    // Kiểm tra email
    if (!validateEmail(email)) {
      document.getElementById("emailErrorMessage").textContent =
        "Email không hợp lệ!";
      isValid = false;
    } else {
      document.getElementById("emailErrorMessage").textContent = "";
    }
  
    // Kiểm tra số điện thoại
    if (!validatePhone(phone)) {
      document.getElementById("phoneErrorMessage").textContent =
        "Số điện thoại không hợp lệ!";
      isValid = false;
    } else {
      document.getElementById("phoneErrorMessage").textContent = "";
    }
  
    if (isValid) {
      var xhr = new XMLHttpRequest();
      xhr.open("POST", "dangki.php", true);
      console.log(xhr);
      xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
      xhr.onreadystatechange = function () {
        if (xhr.readyState === 4) {
          console.log("Response Text:", xhr.responseText);
          if (xhr.status === 200) {
            try {
              var response = JSON.parse(xhr.responseText);
              if (response.status === "success") {
                alert("Đăng ký thành công");
                window.location.href = "index.php?page=dangnhap.php"; // chuyển hướng đến trang đăng nhập
              } else {
                // Hiển thị thông báo lỗi từ server
                if (response.message.includes("Email")) {
                  document.getElementById("emailErrorMessage").textContent =
                    response.message;
                } else if (response.message.includes("Số điện thoại")) {
                  document.getElementById("phoneErrorMessage").textContent =
                    response.message;
                }
              }
            } catch (e) {
              alert("Lỗi xử lý dữ liệu: " + e.message);
            }
          } else {
            alert("Lỗi HTTP:??? " + xhr.status);
          }
        }
      };
      var data = `name=${encodeURIComponent(name)}&email=${encodeURIComponent(
        email
      )}&phone=${encodeURIComponent(phone)}&password=${encodeURIComponent(
        password
      )}`;
      xhr.send(data);
    }
    return false;
  }
  
  function validateEmail(email) {
    var emailRegex = /\S+@\S+\.\S+/;
    return emailRegex.test(email);
  }
  
  function validatePhone(phone) {
    var phoneRegex = /^[0-9]{10,11}$/;
    return phoneRegex.test(phone);
  }
  
  function validatePassword(password) {
    var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;
    return passwordRegex.test(password);
  }
  