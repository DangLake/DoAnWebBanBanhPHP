let slideIndex = 1;//khởi tạo giá trị ban đầu là 1, đại diện cho slide hiện tại đang được hiển thị.

// showSlides, plusSlides, currentSlide: Các hàm điều khiển việc hiển thị các slide và các dot tương ứng.


showSlides(slideIndex);//hiển thị slide đầu tiên khi trang được tải.
function plusSlides(n) {//thay đổi slide hiện tại dựa trên tham số n.
    showSlides(slideIndex += n);// Tăng hoặc giảm giá trị của slideIndex bằng n và gọi hàm showSlides để cập nhật slide hiển thị.
}


function currentSlide(n) {//đặt slideIndex thành n và gọi hàm showSlides để hiển thị slide tương ứng.
    showSlides(slideIndex = n);
}

function showSlides(n) {
    let i;
    let slides = document.getElementsByClassName("mySlides");//Lấy tất cả các phần tử có class "mySlides" và gán vào biến slides.
    let dots = document.getElementsByClassName("dot");//Lấy tất cả các phần tử có class "dot" và gán vào biến dots.
    if (slides.length === 0 || dots.length === 0) {
        //Nếu không tìm thấy phần tử nào có class "mySlides" hoặc "dot", hiển thị thông báo lỗi và thoát khỏi hàm.
        console.error("Slides or dots elements are not found");
        return;
    }
    if (n > slides.length) {slideIndex = 1}//Nếu giá trị n lớn hơn số lượng slide, đặt slideIndex về 1 (vòng lại slide đầu tiên).
    if (n < 1) {slideIndex = slides.length}//Nếu giá trị n nhỏ hơn 1, đặt slideIndex về slide cuối cùng.
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";//Vòng lặp để ẩn tất cả các slide.
    }
    for (i = 0; i < dots.length; i++) {
        //Vòng lặp để xóa class "active" khỏi tất cả các dot.
        dots[i].className = dots[i].className.replace(" active", "");
    }
    if (slides[slideIndex - 1]) {//Kiểm tra nếu slide tại slideIndex - 1 tồn tại, hiển thị slide đó.
        slides[slideIndex - 1].style.display = "block";
    } else {
        console.error("Slide not found at index: " + (slideIndex - 1));
    }
    if (dots[slideIndex - 1]) {//Kiểm tra nếu dot tại slideIndex - 1 tồn tại, thêm class "active" vào dot đó.
        dots[slideIndex - 1].className += " active";
    } else {
        console.error("Dot not found at index: " + (slideIndex - 1));
    }
}
setInterval(function() {//Đặt khoảng thời gian 4000ms (4 giây) để tự động gọi hàm plusSlides(1) và thay đổi slide.
    plusSlides(1);
}, 4000);

// tangsl, giamsl: Các hàm để tăng hoặc giảm số lượng sản phẩm.
function tangsl() {
    event.preventDefault()
    var quantityInput = document.getElementById('quantity');//Lấy phần tử có id "quantity".
    var currentValue = parseInt(quantityInput.value);//Lấy giá trị hiện tại của ô nhập liệu và chuyển đổi nó thành số nguyên.
    quantityInput.value = currentValue + 1;//Tăng giá trị hiện tại thêm 1 và cập nhật lại ô nhập liệu.
}

function giamsl() {
    event.preventDefault()
    var quantityInput = document.getElementById('quantity');
    var currentValue = parseInt(quantityInput.value);
    if (currentValue > 1) {
        quantityInput.value = currentValue - 1;
    }
}