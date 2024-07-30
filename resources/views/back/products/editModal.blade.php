<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Mahsulotni tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editProductForm" enctype="multipart/form-data">
                @csrf

                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Mahsulot nomi</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Narxi</label>
                        <input type="text" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Tavsifi</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_images" class="form-label">Rasmlar</label>
                        <input type="file" class="form-control" id="edit_images" name="images[]" multiple>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Yangilash</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#editProductForm').submit(function(e) {
        e.preventDefault();
        var id = $('#edit_id').val();
        var formData = new FormData(this);

        $.ajax({
            url: '/admin/product/' + id + '/edit',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#editModal').modal('hide');
                $('#editProductForm')[0].reset();
                loadProducts();
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

    $(document).on('click', '.editProduct', function() {
        var id = $(this).data('id');
        $('#edit_id').val(id);
        $('#edit_name').val($(this).data('name'));
        $('#edit_price').val($(this).data('price'));
        $('#edit_description').val($(this).data('description'));
        $('#editModal').modal('show');
    });
</script>
