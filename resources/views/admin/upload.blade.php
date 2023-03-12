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
                            <li class="breadcrumb-item active" aria-current="page">Upload Jadwal Ubinan Bulanan</li>
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
                        <h3>Upload Jadwal Ubinan Bulanan</h3>
                        <p class="text-sm"><span>Upload dilakukan setiap Subround. Perhatian! Melakukan upload akan menghapus semua jadwal ubinan bulanan dan jadwal panen yang sudah diupload sebelumnya dalam satu Subround</span></p>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <form autocomplete="off" method="post" action="/template" class="needs-validation mb-4" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-control-label" for="file">Template</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-outline-primary">Unduh Template</button>
                        </form>
                        <form id="formupload" autocomplete="off" method="post" action="/upload" class="needs-validation" enctype="multipart/form-data" novalidate>
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
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-control-label">Subround</span></label>
                                    <select id="subround" name="subround" class="form-control" data-toggle="select" name="subround" required>
                                        <option value="0" disabled selected> -- Pilih Subround -- </option>
                                        @foreach ($subrounds as $subround)
                                        <option value="{{$subround}}" {{ old('subround') == $subround ? 'selected' : '' }}>
                                            {{ $subround }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('subround')
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
                            <button onclick="uploadFormClick()" class="btn btn-primary mt-3" id="sbmtbtn" type="submit">Upload</button>
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

<script>
    function uploadFormClick() {
        event.preventDefault();
        document.getElementById('loading-background').style.display = 'block'

        var year = document.getElementById("year");
        var idyear = year.options[year.selectedIndex].value;

        var subround = document.getElementById("subround");
        var idsubround = subround.options[subround.selectedIndex].value;

        var file_data = $('#file').prop('files')[0];
        if (file_data == null || idyear == null || idsubround == null) {
            document.getElementById('formupload').submit();
        } else {
            var form_data = new FormData();
            form_data.append('file', file_data);

            document.getElementById('sbmtbtn').disabled = true
            $.ajax({
                url: '/check-upload/' + idyear + '/' + idsubround,
                type: 'GET',
                success: function(response) {
                    document.getElementById('sbmtbtn').disabled = false
                    console.log(response)
                    if (response.is_data_exist === true) {
                        Swal.fire({
                            title: 'Perhatian',
                            text: 'Sudah ada data jadwal panen pada Subround ini, mengupload data lagi akan menghapus data yang sudah ada. Lanjutkan?',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            confirmButtonText: 'Ya',
                            cancelButtonText: 'Tidak',
                        }).then((result) => {
                            if (result.isConfirmed) {
                                document.getElementById('formupload').submit();
                            } else {
                                document.getElementById('loading-background').style.display = 'none'
                            }
                        })
                    } else {
                        document.getElementById('formupload').submit();
                    }

                },
                error: function(jqXHR, textStatus, errorMessage) {
                    document.getElementById('sbmtbtn').disabled = false
                    console.log('Error uploading file: ' + errorMessage);
                    document.getElementById('loading-background').style.display = 'none'
                }
            });
        }
    }
</script>
@endsection