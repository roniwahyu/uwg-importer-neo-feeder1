@extends('layouts.master-dashboard')

@section('title', 'Referensi Dosen')

@section('content')
<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Referensi Dosen</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-5">Data Referensi Dosen</h4>
                <div class="table-responsive">
                    <table id="refDosen" class="display expandable-table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Dosen</th>
                                <th>NIDN</th>
                                <th>Nama Dosen</th>

                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('page-stylesheet')
@endpush

@push('page-script')
<script>
    $(document).ready(function() {
        let table = $('#refDosen').DataTable({
            ajax: {
                url: 'ref-dosen'
            },
            columns: [
                { data: null, orderable: false, searchable: false },
                { data: 'id_dosen' },
                { data: 'nidn' },
                { data: 'nama_dosen' }
            ]
        });
        table.on('order.dt search.dt', function () {
            table.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                cell.innerHTML = i+1;
            });
        });
    });
</script>
@endpush
