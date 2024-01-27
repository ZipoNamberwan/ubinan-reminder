@extends('main')

@section('stylesheet')
<link rel="stylesheet" href="/assets/vendor/select2/dist/css/select2.min.css">
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
                            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item"><a href="/users">Pengguna</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ubah Pengguna</li>
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
                    <div class="card-header">
                        <h3 class="mb-0">Ubah Pengguna</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <form id="formupdate" autocomplete="off" method="post" action="/users/{{$user->id}}" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @method('put')
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label class="form-control-label" for="role">Peran <span class="text-danger">*</span></label>
                                    <div class="custom-control custom-radio mb-3">
                                        <input onchange="onChangeRole('PPL')" name="role" class="custom-control-input" id="rolePPL" value="PPL" type="radio" {{ old('role', count($user->roles) > 0 ? $user->roles[0]->name : null) == 'PPL' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="rolePPL">
                                            Pencacah Lapangan
                                        </label>
                                    </div>
                                    <div class="custom-control custom-radio mb-3">
                                        <input onchange="onChangeRole('PML')" name="role" class="custom-control-input" id="rolePML" value="PML" type="radio" {{ old('role', count($user->roles) > 0 ? $user->roles[0]->name : null) == 'PML' ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="rolePML">
                                            Pengawas Lapangan
                                        </label>
                                    </div>
                                    @error('role')
                                    <div class="text-valid">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label class="form-control-label" for="name">Nama <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="validationCustom03" value="{{ @old('name', $user->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <!-- <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label class="form-control-label" for="email">Email <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" id="validationCustom03" value="{{ @old('email', $user->email) }}">
                                    @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div> -->
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label class="form-control-label" for="password">Nomor Whatsapp <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-merge">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">
                                                +62
                                            </span>
                                        </div>
                                        <input name="phone_number" type="number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ @old('phone_number', $user->phone_number) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    @error('phone_number')
                                    <div class="error-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <label class="form-control-label">Pengawas <span class="text-danger">*</span></label>
                                    <select id="supervisor" name="supervisor" class="form-control" data-toggle="select" name="supervisor" required>
                                        <option value="0" disabled selected> -- Pilih Pengawas -- </option>
                                        @foreach ($supervisors as $supervisor)
                                        <option value="{{ $supervisor->id }}" {{ old('supervisor', $user->hasRole('PPL') ? $user->getPML->id : null) == $supervisor->id ? 'selected' : '' }}>
                                            {{ $supervisor->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('supervisor')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3 mb-3">
                                    <label class="form-control-label" for="password">Password <span class="text-danger">*</span>
                                        <p><small>Abaikan jika password tidak diubah, isikan password baru jika ingin password diubah</small></p>
                                    </label>
                                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" id="validationCustom03" value="{{ @old('password', $user->password) }}">
                                    @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <button class="btn btn-primary mt-3" id="submit" type="submit">Submit</button>
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
    function onChangeRole(role) {
        if (role === 'PPL') {
            document.getElementById('supervisor').disabled = false;
        } else if (role === 'PML') {
            document.getElementById('supervisor').disabled = true;
        }
    }

    onChangeRole('{{count($user->roles) > 0 ? $user->roles[0]->name : "abscdefghi"}} ')
</script>
@endsection