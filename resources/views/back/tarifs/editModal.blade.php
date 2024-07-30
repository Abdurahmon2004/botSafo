<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModalTitle">Tarifni tahrirlash</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editTarifForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Narxi</label>
                        <input type="text" class="form-control" id="edit_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_date" class="form-label">Sana</label>
                        <input type="text" class="form-control" id="edit_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_text" class="form-label">Matn</label>
                        <textarea class="form-control" id="edit_text" name="text" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_items" class="form-label">Items</label>
                        <div id="edit-items-container"></div>
                        <button type="button" id="edit-add-item" class="btn btn-primary mt-2">Item qo'shish</button>
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
    $(document).ready(function() {
        // Add new item input field for edit
        $('#edit-add-item').click(function() {
            $('#edit-items-container').append(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="items[]" required>
                    <button type="button" class="btn btn-danger remove-item">-</button>
                </div>
            `);
        });

        // Remove item input field for edit
        $(document).on('click', '.remove-item', function() {
            $(this).closest('.input-group').remove();
        });

        // Handle form submission for edit
        $('#editTarifForm').submit(function(e) {
            e.preventDefault();
            var id = $('#edit_id').val();
            var formData = new FormData(this);

            $.ajax({
                url: '/tarifs/' + id,
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#editModal').modal('hide');
                    loadTarifs();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        // Edit tarif
        $(document).on('click', '.editTarif', function() {
            var id = $(this).data('id');
            $('#edit_id').val(id);
            $('#edit_name').val($(this).data('name'));
            $('#edit_price').val($(this).data('price'));
            $('#edit_date').val($(this).data('date'));
            $('#edit_text').val($(this).data('text'));

            var items = $(this).data('items');
            $('#edit-items-container').empty();
            $.each(items, function(index, item) {
                $('#edit-items-container').append(`
                    <div class="input-group mb-2">
                        <input type="text" class="form-control" name="items[]" value="${item}" required>
                        <button type="button" class="btn btn-danger remove-item">-</button>
                    </div>
                `);
            });

            $('#editModal').modal('show');
        });
    });
</script>
