@extends('main')

@section('stylesheet')
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
<link rel="stylesheet" href="/assets/vendor/datatables2/datatables.min.css" />
<link rel="stylesheet" href="/assets/vendor/@fortawesome/fontawesome-free/css/fontawesome.min.css" />
@endsection

@section('breadcrumb')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-1 px-0 me-sm-6 me-5">
        <li class="breadcrumb-item text-sm"><a class="opacity-5 text-white" href="/">Home</a></li>
        <li class="breadcrumb-item text-sm text-white active" aria-current="page">Upload Jadwal Panen</li>
    </ol>
    <h6 class="font-weight-bolder text-white mb-0">Upload Jadwal Panen</h6>
</nav>
@endsection

@section('container')
<!-- Page content -->

<div class="container-fluid mt--6">
    @if (session('success-edit') || session('success-create'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
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

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <!-- Table -->
    <div class="row">
        <div class="col">
            <div class="card z-index-2 h-100">
                <div class="card-header pb-0 pt-3 bg-transparent">
                    <h4 class="text-capitalize">Upload Jadwal Panen</h4>
                    <!-- <p class="text-sm mb-0">
                        <i class="fa fa-arrow-up text-success"></i>
                        <span class="font-weight-bold">4% more</span> in 2021
                    </p> -->
                </div>
                <div class="card-body p-3">
                    <form id="formupdate" autocomplete="off" method="post" action="/upload" class="needs-validation" enctype="multipart/form-data" novalidate>
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-control-label" for="file">File Upload</label>
                                <img class="img-preview-file img-fluid mb-3 col-sm-5 image-preview" style="display:block">
                                <div class="custom-file">
                                    <input name="file" type="file" class="custom-file-input" id="file" lang="en" accept=".xlsx">
                                    <label class="custom-file-label" for="customFileLang" id="fileLabel">Select file</label>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3" id="submit" type="submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('optionaljs')

@endsection