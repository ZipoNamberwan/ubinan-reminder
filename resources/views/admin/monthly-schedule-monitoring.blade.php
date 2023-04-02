@extends('main')

@section('stylesheet')
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="/assets/vendor/datatables2/datatables.min.css" />
<link rel="stylesheet" href="/assets/vendor/@fortawesome/fontawesome-free/css/fontawesome.min.css" />
<link rel="stylesheet" href="/assets/css/container.css">
<link rel="stylesheet" href="/assets/css/text.css">
@endsection

@section('container')
<div class="header bg-success pb-6">
    <div class="container-fluid">
        <div class="header-body">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 col-7">
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jadwal Panen</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid mt--6">

    @if (session('success-edit') || session('success-create'))
    <div class="alert alert-primary alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
        <span class="alert-text"><strong>Sukses! </strong>{{ session('success-create') }} {{ session('success-edit') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    @endif

    @if (session('success-delete'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
        <span class="alert-text"><strong>Sukses! </strong>{{ session('success-delete') }}</span>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
        </button>
    </div>
    @endif

    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- Custom form validation -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header pb-0">
                        <div class="row">
                            <div class="col-md-7">
                                <h3>Monitoring Jadwal Panen</h3>
                                <p class="text-sm"><span>Tabel berikut menampilkan Jadwal Panen Bulanan dan Tanggal Perkiraan Panen.</span></p>
                            </div>
                            <div class="col-md-5 text-right">
                                <a href="{{url('/jadwal-panen/create')}}" class="btn btn-primary btn-round btn-icon" data-toggle="tooltip" data-original-title="Tambah Jadwal Panen">
                                    <span class="btn-inner--icon"><i class="fas fa-plus-circle"></i></span>
                                    <span class="btn-inner--text">Tambah Jadwal</span>
                                </a>
                                <form class="d-inline" method="POST" action="/jadwal-panen/download" data-toggle="tooltip" data-original-title="Unduh Jadwal Panen">
                                    @csrf
                                    <button class="btn btn-icon btn-outline-primary" type="submit">
                                        <span class="btn-inner--icon"><i class="fas fa-download"></i></span>
                                        <span class="btn-inner--text">Download</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-2">
                                <label class="form-control-label mb-3" for="subround">Subround</label>
                                <select class="form-control d-inline" data-toggle="select" name="subround" id="subround">
                                    @foreach($subrounds as $subround)
                                    <option value="{{$subround}}" @if($subround==$currentsubround) selected @endif>{{$subround}}</option>
                                    @endforeach
                                </select>
                                <button onclick="getDataBySubround()" class="btn btn-primary mt-3 d-inline" type="button">Tampilkan</button>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="row-table">
                            <div class="table-responsive">
                                <table class="table" id="datatable-id" width="100%">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>Identitas Sampel</th>
                                            <!-- Identitas Sampel mencakup kode kec, kode desa, kode sls, nbs, nks, no sampel, nama krt dan alamat -->
                                            <th>Responden</th>
                                            <th>Komoditas</th>
                                            <th>Bulan Panen</th>
                                            <th>Jenis Sampel</th>
                                            <th>Petugas</th>
                                            <th>Tanggal Perkiraan Panen</th>
                                            <th>Aksi</th>
                                            <!-- Aksi mencakup detail -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/select2/dist/js/select2.min.js"></script>
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/datatables2/datatables.min.js"></script>

<script>
    var table = $('#datatable-id').DataTable({
        "order": [],
        "serverSide": true,
        "processing": true,
        "ajax": {
            "url": '/jadwal-panen/data',
            "type": 'GET',
        },
        "columns": [{
                "responsivePriority": 8,
                "width": "10%",
                "data": "bs_name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<p class="mb-0"><span class="badge badge-primary">' + row.bs_id + '</span></p>' +
                            '<p class="mb-0"><span class="badge badge-success">' + data + '</span></p>';
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "resp_name",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        return '<strong>' + data + '</strong>' + '<br>' +
                            row.resp_address;
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "commodity_name",
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "month_name",
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "sample_type_name",
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "user_name",
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "harvest_schedule",
                "render": function(data, type, row) {
                    if (type === 'display') {
                        if (data == null) {
                            return '<span class="badge badge-danger">Belum Diisi</span>';
                        }
                    }
                    return data;
                }
            },
            {
                "responsivePriority": 1,
                "width": "5%",
                "data": "id",
                "render": function(data, type, row) {
                    return "<a href=\"/jadwal-panen/" + data + "/edit\" class=\"btn btn-outline-info  btn-sm\" role=\"button\" aria-pressed=\"true\" data-toggle=\"tooltip\" data-original-title=\"Ubah Data\">" +
                        "<span class=\"btn-inner--icon\"><i class=\"fas fa-edit\"></i></span></a>" +
                        "<form class=\"d-inline\" id=\"formdelete" + data + "\" name=\"formdelete" + data + "\" onsubmit=\"deleteSchedule('" + data + "','" + (row.bs_name + ' ' + row.resp_name) + "')\" method=\"POST\" action=\"/jadwal-panen/" + data + "\">" +
                        '@method("delete")' +
                        '@csrf' +
                        "<button class=\"btn btn-icon btn-outline-danger btn-sm\" type=\"submit\" data-toggle=\"tooltip\" data-original-title=\"Hapus Data\">" +
                        "<span class=\"btn-inner--icon\"><i class=\"fas fa-trash-alt\"></i></span></button></form>";
                }
            },
        ],
        "language": {
            'paginate': {
                'previous': '<i class="fas fa-angle-left"></i>',
                'next': '<i class="fas fa-angle-right"></i>'
            }
        }
    });

    function getDataBySubround() {
        var e = document.getElementById('subround');
        var idsubround = e.options[e.selectedIndex].value;
        table.ajax.url('/jadwal-panen/data/' + idsubround).load();
    }
</script>

<script>
    function deleteSchedule(id, name) {
        event.preventDefault();
        Swal.fire({
            title: 'Yakin Hapus Jadwal Ini?',
            text: name,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya',
            cancelButtonText: 'Tidak',
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('formdelete' + id).submit();
            }
        })
    }
</script>

@endsection