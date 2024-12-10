function initSlider() {
    //Lấy phần tử đầu tiên trong tài liệu mà có class là "image-list" và nằm trong phần tử có class là "slider-wrapper", gán vào biến imageList.
    const imageList = document.querySelector(".slider-wrapper .image-list")
    const slideButtons = document.querySelectorAll(".slider-wrapper .slide-button")
    const slideScrollBar = document.querySelector(".mainslider .slider-scrollbar")
    const scrollBarThumb = slideScrollBar.querySelector(".scrollbar-thumb")//Lấy phần tử đầu tiên trong slideScrollBar
    //Tính toán sự chênh lệch giữa chiều rộng của phần tử và chiều rộng của khu vực hiển thị, gán vào biến max.
    const max = imageList.scrollWidth - imageList.clientWidth

    //Thêm sự kiện "mousedown" cho phần tử scrollBarThumb.
    //mousedown là người dùng nhấn chuột xuống trên một phần tử trên trang web
    // mousemove: Là sự kiện xảy ra khi người dùng di chuyển chuột.
    // mouseup: Là sự kiện xảy ra khi người dùng nhả chuột ra khỏi một phần tử.

    scrollBarThumb.addEventListener("mousedown", function(e){
        //function(e) { ... }: Đây là một hàm xử lý sự kiện cho sự kiện "mousedown". Tham số e chứa thông tin về sự kiện chuột.
        const startX = e.clientX;//Lấy vị trí ngang của con trỏ chuột khi người dùng nhấn chuột vào scrollBarThumb.

        //Lấy vị trí ngang của scrollBarThumb tính từ phần tử cha gần nhất có thuộc tính position không phải là static.
        const thumbPosition = scrollBarThumb.offsetLeft

        //Thực hiện tính toán và cập nhật vị trí của scrollBarThumb và imageList dựa trên sự di chuyển của chuột.
        function handlerMousemove(e){
            const deltaX = e.clientX - startX
            //Tính toán khoảng cách mà chuột đã di chuyển trên trục ngang từ lúc người dùng nhấn chuột xuống (được lưu trong startX) 
            //đến thời điểm hiện tại (được cung cấp bởi e.clientX, tọa độ ngang của chuột).

            const newThumbP = thumbPosition + deltaX
            //Tính toán vị trí mới của thanh cuộn (scrollBarThumb) dựa trên vị trí hiện tại của nó 
            //(được lưu trong thumbPosition) cộng với khoảng cách mà chuột đã di chuyển (deltaX).


            const maxThumbP=slideScrollBar.getBoundingClientRect().width-scrollBarThumb.offsetWidth
            //Xác định giá trị tối đa mà thanh cuộn có thể di chuyển, được tính bằng cách lấy chiều rộng 
            //của thanh cuộn cha (slideScrollBar) trừ đi chiều rộng của thanh cuộn (scrollBarThumb).


            const boundedP=Math.max(0, Math.min(maxThumbP,newThumbP))
            //Giới hạn vị trí mới của thanh cuộn trong khoảng từ 0 đến maxThumbP bằng cách 
            //sử dụng Math.max() và Math.min(). Điều này đảm bảo rằng thanh cuộn không di chuyển quá biên của phần tử cha.


            const scrollPosition= (boundedP/maxThumbP) *max
            //Tính toán vị trí mới của thanh cuộn trên imageList dựa trên vị trí của thanh cuộn trên thanh cuộn cha (boundedP). 
            //Vị trí này được chia tỷ lệ với giá trị tối đa mà 
            //thanh cuộn có thể di chuyển (maxThumbP) và nhân với giá trị tối đa mà imageList có thể cuộn (max).


            scrollBarThumb.style.left = `${boundedP}px`
            //Cập nhật vị trí ngang của thanh cuộn (scrollBarThumb) trên thanh cuộn cha (slideScrollBar), 
            //biến boundedP được sử dụng để đảm bảo thanh cuộn không di chuyển quá biên của thanh cuộn cha.


            imageList.scrollLeft=scrollPosition
            //Cập nhật vị trí ngang của imageList để cuộn đến vị trí mới tính toán được (scrollPosition).
        }

        //Loại bỏ các hàm xử lý "mousemove" và "mouseup" khỏi document, kết thúc việc kéo scroll bar.
        function handlerMouseup() {
            document.removeEventListener("mousemove", handlerMousemove);
            document.removeEventListener("mouseup", handlerMouseup);
        }

        //Thêm các hàm xử lý "mousemove" và "mouseup" vào document, cho phép các sự kiện này được lắng nghe trên toàn bộ tài liệu.
        document.addEventListener("mousemove", handlerMousemove);
        document.addEventListener("mouseup", handlerMouseup);
    })

    slideButtons.forEach(button => {
        button.addEventListener("click", function(){//Duyệt qua mỗi nút điều khiển slide và thêm sự kiện "click" cho chúng.
            //Xác định hướng cuộn bằng cách kiểm tra ID của nút điều khiển. Nếu ID là "prev-slide" (nút cuộn về trước),
            // direction sẽ là -1; ngược lại, nếu ID là "next-slide" (nút cuộn tiếp theo), direction sẽ là 1.
            const direction = button.id === "prev-slide" ? -1 : 1
            //Xác định lượng cuộn bằng cách nhân chiều rộng của danh sách hình ảnh (imageList) với hướng (direction). 
            //Nếu direction là -1, slider sẽ cuộn về phía trước; nếu direction là 1, slider sẽ cuộn sang trang tiếp theo.
            const crollAmount = imageList.clientWidth * direction

            //Cuộn imageList sang phải một khoảng bằng crollAmount sử dụng phương thức scrollBy().
            //left: crollAmount xác định lượng cuộn theo chiều ngang.
            //behavior: "smooth" làm cho cuộn trở nên mượt mà, thay vì nhảy nhót.
            imageList.scrollBy({ left: crollAmount, behavior: "smooth" })
        })
    })

    // // Hàm cập nhật vị trí của scrollBarThumb khi imageList được cuộn.
    function updateScroll(){
        const scrollPosition = imageList.scrollLeft//Lấy vị trí hiện tại của thanh cuộn trên trục ngang của danh sách hình ảnh (imageList).
        const thumbPosition =Math.floor((scrollPosition / max) * (slideScrollBar.clientWidth - scrollBarThumb.offsetWidth))
//         Tính toán vị trí mới của thanh cuộn dựa trên vị trí cuộn hiện tại của imageList.
//          max là sự chênh lệch giữa chiều rộng của imageList và chiều rộng của khu vực hiển thị.
//      (scrollPosition / max) tính tỷ lệ vị trí hiện tại của thanh cuộn trong khoảng từ 0 đến 1.
//      (slideScrollBar.clientWidth - scrollBarThumb.offsetWidth) tính chiều rộng có sẵn cho thanh cuộn.
//      Kết quả của phép nhân này là vị trí mới của thanh cuộn, nhưng có thể vượt qua biên.
//       Math.floor() được sử dụng để làm tròn giá trị này xuống thành số nguyên gần nhất.


        scrollBarThumb.style.left = `${thumbPosition}px`;
    //Cập nhật thuộc tính left của thanh cuộn (scrollBarThumb) với giá trị mới tính toán được.
    // Điều này sẽ làm di chuyển thanh cuộn đến vị trí mới trên thanh cuộn.

    }

    // // Thêm sự kiện "scroll" cho imageList để gọi hàm updateScroll mỗi khi imageList được cuộn.
    imageList.addEventListener("scroll", function(){
        updateScroll()
    })
}

//// Khi tài liệu được tải xong, gọi hàm initSlider để khởi tạo slider.
window.addEventListener("load", initSlider);