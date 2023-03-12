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
    <div class="row">
        <div class="col">
            <div class="card-wrapper">
                <!-- Custom form validation -->
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header pb-0">
                        <h3>Monitoring Jadwal Panen</h3>
                        <p class="text-sm"><span>Tabel berikut menampilkan Jadwal Panen Bulanan dan Tanggal Perkiraan Panen.</span></p>
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

@endsection