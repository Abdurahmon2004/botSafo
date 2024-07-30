@extends('back.app')

@section('content')
<div class="container">
    <h1>Tariflar</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        Tarif qo'shish
    </button>
    <table class="table table-bordered mt-3" id="tarifTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nomi</th>
                <th>Narxi</th>
                <th>Sana</th>
                <th>Matn</th>
                <th>Items</th>
                <th>Harakatlar</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be loaded here via AJAX -->
        </tbody>
    </table>
</div>

<!-- Include Add Modal -->
@include('back.tarifs.addModal')
<!-- Include Edit Modal -->
@include('back.tarifs.editModal')

<script>
    $(document).ready(function() {
        loadTarifs();

        // Function to load tarifs data
        function loadTarifs() {
            $.ajax({
                url: '/tarifs',
                method: 'GET',
                success: function(response) {
                    let rows = '';
                    response.forEach(function(tarif) {
                        rows += `
                            <tr>
                                <td>${tarif.id}</td>
                                <td>${tarif.name}</td>
                                <td>${tarif.price}</td>
                                <td>${tarif.date}</td>
                                <td>${tarif.text}</td>
                                <td>${tarif.items.join(', ')}</td>
                                <td>
                                    <button class="btn btn-sm btn-warning editTarif" data-id="${tarif.id}" data-name="${tarif.name}" data-price="${tarif.price}" data-date="${tarif.date}" data-text="${tarif.text}" data-items='${JSON.stringify(tarif.items)}'>Tahrirlash</button>
                                    <button class="btn btn-sm btn-danger deleteTarif" data-id="${tarif.id}">O'chirish</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tarifTable tbody').html(rows);
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }

        // Handle delete tarif
        $(document).on('click', '.deleteTarif', function() {
            if (confirm("Tarifni o'chirishni xohlaysizmi?")) {
                var id = $(this).data('id');
                $.ajax({
                    url: '/tarifs/' + id,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        loadTarifs();
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }
        });
    });
</script>
@endsection
