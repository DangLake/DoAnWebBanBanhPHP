function editProduct(id, name, price, categoryId) {
    document.getElementById('edit-form').style.display = 'block';
    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_product_name').value = name;
    document.getElementById('edit_price').value = price;
    document.getElementById('description').value = description; 
    document.getElementById('edit_category_id').value = categoryId;
}
function editCategory(id, name) {
    document.getElementById('category_id').value = id;
    document.getElementById('edit_category_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit-form').style.display = 'block';
}