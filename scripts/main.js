var myVar;

function myFunction() {
    myVar = setTimeout(showPage, 3000); // Tạo độ trễ 3 giây
}

function showPage() {
    document.getElementById("loader").style.display = "none";
    document.getElementById("myDiv").style.display = "block";
}

function scrollToTop() {
    window.scrollTo({
        top: 0, 
        behavior: 'smooth' // Cuộn mượt
    });
}
function toggleMenu() {
    var menu = document.querySelector('.mainmenu'); /* Lấy phần tử menu */
    menu.classList.toggle('active'); /* Thêm hoặc xóa lớp "active" khỏi phần tử menu */
}

window.addEventListener("load", () => {
    clearTimeout(myVar); // Xóa bộ hẹn giờ nếu trang tải trước khi hết 3 giây
    showPage(); // Hiển thị nội dung trang ngay lập tức
});


