@extends('back.app')

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Maxsulotlar</h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                        Yangi mahsulot qo'shish
                    </button>
                </div>
                <div class="card-body" id="ajax-request">
                    @include('back.products.ajax-table', ['products' => $products])
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @include('back.products.addModal')
    @include('back.products.editModal')
    <script>
        function loadProducts() {
            $.ajax({
                url: '{{ route('products.index') }}',
                type: 'GET',
                dataType: 'html',
                success: function(response) {
                    $('#ajax-request').html(response);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        $('#addProductForm').submit(function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            $.ajax({
                url: '{{ route('products.store') }}',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#addModal').modal('hide');
                    $('#addProductForm')[0].reset();
                    loadProducts();
                },
                error: function(error) {
                    console.log(error);
                }
            });
        });
        $(document).on('click', '.deleteProduct', function() {
            var id = $(this).data('id');

            if (confirm('Mahsulotni o\'chirishga ishonchingiz komilmi?')) {
                $.ajax({
                    url: '/products/' + id,
                    type: 'DELETE',
                    success: function(response) {
                        loadProducts();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    </script>
@endsection


