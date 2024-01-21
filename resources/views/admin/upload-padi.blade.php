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
                            <li class="breadcrumb-item active" aria-current="page">Upload Jadwal Ubinan Bulanan Padi</li>
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
                        <h3>Upload Jadwal Ubinan Bulanan Padi</h3>
                        <p class="text-sm"><span>Upload dilakukan setiap Subround. Perhatian! Melakukan upload akan menghapus semua jadwal ubinan bulanan dan jadwal panen yang sudah diupload sebelumnya dalam satu Subround</span></p>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <form autocomplete="off" method="post" action="/template-padi" class="needs-validation mb-4" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-control-label" for="file">Template</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Unduh Template</button>
                        </form>
                        <form id="formupload" autocomplete="off" method="post" action="/upload-padi" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-control-label">Tahun</span></label>
                                    <select id="year" name="year" class="form-control" data-toggle="select" name="year" required>
                                        <option value="0" disabled selected> -- Pilih Tahun -- </option>
                                        @foreach ($years as $year)
                                        <option value="{{$year->id}}" {{ old('year', $currentyear->id) == $year->id ? 'selected' : '' }}>
                                            {{ $year->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('year')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-control-label" for="file">File Upload</label>
                                    <img class="img-preview-file img-fluid col-sm-5 image-preview" style="display:block">
                                    <div class="custom-file">
                                        <input name="file" type="file" class="custom-file-input" id="file" lang="en" accept=".xlsx" onchange="previewFile()">
                                        <label class="custom-file-label" for="customFileLang" id="fileLabel">Select file</label>
                                    </div>
                                    @error('file')
                                    <div class="error-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3" id="sbmtbtn" type="submit">Upload</button>
                            <!-- <button class="btn btn-primary mt-3" id="sbmtbtn" type="submit">Upload</button> -->
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')
<script src="/assets/vendor/sweetalert2/dist/sweetalert2.js"></script>
<script src="/assets/vendor/select2/dist/js/select2.min.js"></script>

<script>
    selectedFile = null

    function previewFile(event) {
        var fileLabel = document.getElementById('fileLabel');
        const file = document.querySelector('#file');
        fileLabel.innerText = file.files[0].name;
        selectedFile = file.files[0];
    }
</script>

@endsection