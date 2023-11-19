@extends('layouts.admin')

@section('main-content')
    <div class="align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <h5 class="card-title fw-semibold">Product Lists</h5>
                    <a class="btn btn-warning mb-4" style="font-size: 14px;" href="{{ route('products.create') }}">Create
                        Product</a>
                </div>
                <div style="max-height: 40vh;" class="table-responsive mb-3 overflow-y-scroll" id="scroll">
                    <table class="table table-hover table-striped bg-white" id="Tables">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                                <th colspan="2" class="text-center">#</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($products != 0)
                                @php
                                    $i = 1;
                                @endphp
                                @foreach ($products as $key => $item)
                                    <tr>
                                        <td>{{ $i++ }}</td>
                                        @if (isset($item['image_url']))
                                            <td><img width="100px" src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}"></td>
                                        @endif
                                        <td>{{ $item['name'] }}</td>
                                        <td>{{ $item['desc'] }}</td>
                                        <td>{{ $item['category'] }}</td>
                                        <td>{{ $item['price'] }}</td>
                                        <td>{{ $item['stock'] }}</td>
                                        <td><a href="{{ route('products.edit', ['product' => $key]) }}" class="btn btn-outline-dark btn-sm"><i class="fa-solid fa-pen-to-square"></i></td>
                                        <td>
                                            <div>
                                                <form action="{{ route('products.destroy', ['product' => $key]) }}" method="POST">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="btn btn-outline-dark btn-sm me-2"><i class="fa-regular fa-trash-can"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <td colspan="8" class="text-center">Gaada Data Cuy</td>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.3.2/js/dataTables.fixedHeader.min.js"></script>
    <script type="module">
        $(document).ready(function() {
            $("#Tables").DataTable({
                responsive: true,
                // searching: false,
            });
        });
    </script>
@endpush
