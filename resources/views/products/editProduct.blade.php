@extends('layouts.admin')

@section('main-content')
<div class="container px-5 my-5">
    <h3 class="mb-5">Edit Product</h3>
    <form method="POST" action="{{ route('products.update',['product'=> $productId]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label" for="productName">Product Name</label>
            <input class="form-control" id="productName" name="productName" type="text" placeholder="Product Name" value="{{ $productData['name'] }}" required/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="description">Description</label>
            <input class="form-control" id="description" name="description" type="text" placeholder="Description" value="{{ $productData['desc'] }}" required/>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select name="category" id="category" class="form-control">
                <option value="furniture">Furniture</option>
                <option value="workspace">Workspace</option>
                <option value="accessories">Accessories</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label" for="price">Price</label>
            <input class="form-control" id="price" name="price" type="number"  placeholder="Price" value="{{ $productData['price'] }}" required/>
        </div>
        <div class="mb-3">
            <label class="form-label" for="stock">Stock</label>
            <input class="form-control" id="stock" name="stock" type="number" placeholder="Stock" value="{{ $productData['stock'] }}" required/>
        </div>
        <div class="mb-3">
            <label for="image" class="form-label">Image</label>
            <input type="file" class="form-control" name="image" id="image" accept="image/*">
        </div>
        <div class="d-grid">
            <button class="btn btn-primary" id="submitButton" type="submit">Submit</button>
        </div>
    </form>
</div>
@endsection
