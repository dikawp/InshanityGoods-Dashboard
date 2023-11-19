@extends('layouts.admin')

@section('main-content')
    {{-- @vite('resources/js/datatable.js') --}}
    <div class="align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between">
                    <h5 class="card-title fw-semibold">User Lists</h5>
                    <a style="font-size: 24px;" href=""><i class="bi bi-arrow-right"></i></a>
                </div>
                <div style="max-height: 40vh;" class="table-responsive mb-3 overflow-y-scroll" id="scroll">
                    <table class="table table-hover table-striped bg-white" id="Tables">
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Phone</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Wleo</td>
                                <td>Halooooo</td>
                                <td>Halooooo</td>
                                <td>Halooooo</td>
                                <td><a href="" class="btn btn-outline-dark btn-sm me-2"><i class="fa-solid fa-pen-to-square"></i></td>
                            </tr>
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
