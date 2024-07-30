<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Images</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->price }}</td>
                <td>{{ $product->description }}</td>
                <td>
                    @foreach(json_decode($product->images, true) as $image)
                        <img src="{{ asset('storage/products/' . $image) }}" style="object-fit: contain" width="100px">
                    @endforeach
                </td>
                <td>
                    <button type="button"
                            class="btn btn-primary btn-sm editProduct"
                            data-id="{{ $product->id }}"
                            data-name="{{ $product->name }}"
                            data-price="{{ $product->price }}"
                            data-description="{{ $product->description }}"
                            data-images="{{ $product->images }}">
                        Tahrirlash
                    </button>
                    <button type="button"
                            class="btn btn-danger btn-sm deleteProduct"
                            data-id="{{ $product->id }}">
                        O'chirish
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
