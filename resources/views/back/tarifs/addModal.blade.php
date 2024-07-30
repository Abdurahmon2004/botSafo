<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModalTitle">Tarif qo'shish</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addTarifForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Nomi</label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_price" class="form-label">Narxi</label>
                        <input type="text" class="form-control" id="add_price" name="price" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_date" class="form-label">Sana</label>
                        <input type="text" class="form-control" id="add_date" name="date" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_text" class="form-label">Matn</label>
                        <textarea class="form-control" id="add_text" name="text" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="add_items" class="form-label">Items</label>
                        <div id="add-items-container"></div>
                        <button type="button" id="add-add-item" class="btn btn-primary mt-2">Item qo'shish</button>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bekor qilish</button>
                    <button type="submit" class="btn btn-primary">Saqlash</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add new item input field
        $('#add-add-item').click(function() {
            $('#add-items-container').append(`
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="items[]" required>
                    <button type="button" class="btn btn-danger remove-item">-</button>
                </div>
            `);
        });

        // Remove item input field
        $(document).on('click', '.remove-item', function() {
            $(this).closest('.input-group').remove();
        });

        // Handle form submission
        $('#addTarifForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '/tarifs',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#addModal').modal('hide');
                    loadTarifs();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
    });
</script>
