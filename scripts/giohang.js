document.querySelectorAll('.remove-item').forEach(button => {
    button.addEventListener('click', function () {
        const cartId = this.getAttribute('data-cart-id');

        fetch('giohang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'remove',
                cart_id: cartId
            })
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  location.reload();
              } else {
                  alert(data.message);
              }
          });
    });
});

// Cập nhật số lượng sản phẩm trong giỏ hàng
document.querySelectorAll('.update-quantity').forEach(input => {
    input.addEventListener('change', function () {
        const cartId = this.getAttribute('data-cart-id');
        const quantity = this.value;

        fetch('giohang.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'update',
                cart_id: cartId,
                quantity: quantity
            })
        }).then(response => response.json())
          .then(data => {
              if (data.status === 'success') {
                  location.reload();
              } else {
                  alert(data.message);
              }
          });
    });
});