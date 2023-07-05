@extends('layouts.master-dashboard')

@section('title', 'Referensi Anggota Aktivitas')

@section('content')

<div class="row">

    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">Referensi Anggota Aktivitas</h3>
            </div>
        </div>
    </div>
</div>
<div class="row">

    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-5">Data Referensi Anggota Aktivitas</h4>
                <div class="row">
                    <!-- <div class="col-4 grid-margin stretch-card"> -->
                        <div class="form-group col-lg-6 @error('semester') has-danger @enderror">
                            <label>Semester</label>
                            <select class="form-control @error('semester') form-control-danger @enderror" name="semester">
                                @foreach ($semester['data'] as $semester)
                                <option value="{{ $semester['id_semester'] }}" {{ old('semester') == $semester['id_semester'] ? 'selected' : '' }}>
                                    {{ $semester['nama_semester'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('semester')
                            <label class="error text-danger mt-2">{{ $message }}</label>
                            @enderror
                        </div>
                    <!-- </div> -->
                    <!-- <div class="col-4 grid-margin stretch-card"> -->
                        <div class="form-group col-lg-6 @error('prodi') has-danger @enderror">
                            <label>Semester</label>
                            <select class="form-control @error('prodi') form-control-danger @enderror" name="prodi">
                                @foreach ($prodi['data'] as $prodi)
                                <option value="{{ $prodi['id_prodi'] }}" {{ old('prodi') == $prodi['id_prodi'] ? 'selected' : '' }}>
                                    {{ $prodi['nama_jenjang_pendidikan'] }}
                                    {{ $prodi['nama_program_studi'] }}
                                </option>
                                @endforeach
                            </select>
                            @error('prodi')
                            <label class="error text-danger mt-2">{{ $message }}</label>
                            @enderror
                        </div>
                    <!-- </div> -->
                    <div class="col-12 grid-margin stretch-card"><button>Filter</button></div>

                </div>
                <div class="table-responsive">
                    <table id="refAnggota" class="display expandable-table table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>ID Aktivitas</th>
                                <th>ID Anggota</th>
                                <th>NIM</th>
                                <th>Nama Mhs</th>
                                <th>Judul</th>
                                <th>Peran</th>
                                <th>Status Sync</th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th>#</th>
                                <th>ID Aktivitas</th>
                                <th>ID Anggota</th>
                                <th>NIM</th>
                                <th>Nama Mhs</th>
                                <th>Judul</th>
                                <th>Peran</th>
                                <th>Status Sync</th>
                            </tr>
                        </tfoot>
                        
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
        let table = $('#refAnggota').DataTable({
            ajax: {
                url: 'ref-listanggota'
            },
            // pageLength: 25,
            buttons: [
                { extend: 'copy', className: 'btn btn-outline-secondary' },
                { extend: 'print', className: 'btn btn-outline-primary' },
                { extend: 'excel', className: 'btn btn-outline-success' },
                { extend: 'csv', className: 'btn btn-outline-info' }
            ],
            // dom: '<"row"B>lfrtip',
            // dom: '<"top"lf>rt<"bottom"ifp><"clear">',
            // buttons: [
            //     'copy', 'csv', 'excel', 'pdf', 'print'
            // ],
            columns: [{
                    data: null,
                    orderable: false,
                    searchable: false
                },
                {

                    data: 'id_aktivitas',
                    orderable: false,
                    searchable: false
                },
                
                {

                    data: 'id_anggota',
                    orderable: false,
                    searchable: false
                },
                
                {
                    data: 'nim'
                },
                {
                    data: 'nama_mahasiswa'
                },
                {
                    data: 'judul'
                },
                {
                    data: 'nama_jenis_peran'
                },
                {
                    data: 'status_sync'
                }
            ],
           /*  dom: 'Qlfrtip',
            searchBuilder: {
                columns: ([2,3,4])
            } */
            initComplete: function () {
            this.api()
                .columns([3,4,5])
                .every(function () {
                    var column = this;
                    var select = $('<select><option value=""></option></select>')
                        .appendTo($(column.footer()).empty())
                        .on('change', function () {
                            var val = $.fn.dataTable.util.escapeRegex($(this).val());
 
                            column.search(val ? '^' + val + '$' : '', true, false).draw();
                        });
 
                    column
                        .data()
                        .unique()
                        .sort()
                        .each(function (d, j) {
                            select.append('<option value="' + d + '">' + d + '</option>');
                        });
                });
            }, 
        });
        table.on('order.dt search.dt', function() {
            table.column(0, {
                search: 'applied',
                order: 'applied'
            }).nodes().each(function(cell, i) {
                cell.innerHTML = i + 1;
            });
        });
    });
</script>
@endpush