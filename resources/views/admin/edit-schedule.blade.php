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
                            <li class="breadcrumb-item"><a href="/jadwal-panen">Jadwal Panen</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Ubah Jadwal Panen Bulanan</li>
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
                        <h3 class="mb-0">Ubah Jadwal Panen Bulanan</h3>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <form id="formupdate" autocomplete="off" method="post" action="/jadwal-panen/{{$schedule->id}}" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Kecamatan <span class="text-danger">*</span></label>
                                    <select id="subdistrict" name="subdistrict" class="form-control" data-toggle="select" name="subdistrict" required>
                                        <option value="0" disabled selected> -- Pilih Kecamatan -- </option>
                                        @foreach ($subdistricts as $subdistrict)
                                        <option value="{{ $subdistrict->id }}" {{ old('subdistrict', $schedule->bs->subdistrict->id) == $subdistrict->id ? 'selected' : '' }}>
                                            [{{ $subdistrict->code}}] {{ $subdistrict->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('subdistrict')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Desa <span class="text-danger">*</span></label>
                                    <select id="village" name="village" class="form-control" data-toggle="select" name="village">
                                    </select>
                                    @error('village')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Blok Sensus <span class="text-danger">*</span></label>
                                    <select id="bs" name="bs" class="form-control" data-toggle="select" name="bs">
                                    </select>
                                    @error('bs')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label" for="name">Nama Responden <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="validationCustom03" value="{{ @old('name', $schedule->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mt-3">
                                    <div class="form-group">
                                        <label class="form-control-label" for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="4">{{old('address', $schedule->address)}}</textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Komoditas <span class="text-danger">*</span></label>
                                    <select id="commodity" name="commodity" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Komoditas -- </option>
                                        @foreach ($commodities as $commodity)
                                        <option value="{{ $commodity->id }}" {{ old('commodity', $schedule->commodity->id) == $commodity->id ? 'selected' : '' }}>
                                            {{ $commodity->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('commodity')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Jenis Sampel <span class="text-danger">*</span></label>
                                    <select id="sample-type" name="sample-type" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Jenis Sampel -- </option>
                                        @foreach ($sampleTypes as $sampleType)
                                        <option value="{{ $sampleType->id }}" {{ old('sample-type', $schedule->sampleType->id) == $sampleType->id ? 'selected' : '' }}>
                                            {{ $sampleType->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('sampleType')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Bulan Panen <span class="text-danger">*</span></label>
                                    <select id="month" name="month" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Bulan Panen -- </option>
                                        @foreach ($months as $month)
                                        <option value="{{ $month->id }}" {{ old('month', $schedule->month->id) == $month->id ? 'selected' : '' }}>
                                            {{ $month->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('month')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4 mt-3">
                                    <label class="form-control-label">Petugas <span class="text-danger">*</span></label>
                                    <select id="user" name="user" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Petugas -- </option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}" {{ old('user', $schedule->user->id) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('user')
                                    <div class="text-valid mt-2">
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
    $(document).ready(function() {
        $('#subdistrict').on('change', function() {
            loadVillage('0');
        });
        $('#village').on('change', function() {
            loadbs('0');
        });
        // village = '{{ old("village", $schedule->bs->subdistrict->id)}}'
        // bs = '{{ old("bs", $schedule->bs->village->id)}}'
        // document.getElementById('village').value = village
        // document.getElementById('bs').value = bs

        // loadVillage(village)
        // loadbs(bs)
    });

    function loadVillage(selectedvillage) {
        let id = $('#subdistrict').val();
        $('#village').empty();
        $('#village').append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/jadwal-panen/village/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                $('#village').empty();
                $('#village').append(`<option value="0" disabled selected>Pilih Desa</option>`);
                $('#bs').empty();
                $('#bs').append(`<option value="0" disabled selected>Pilih Blok Sensus</option>`);
                response.forEach(element => {
                    if (selectedvillage == String(element.id)) {
                        $('#village').append('<option value=\"' + element.id + '\" selected>' +
                            '[' + element.short_code + ']' + element.name + '</option>');
                    } else {
                        $('#village').append('<option value=\"' + element.id + '\">' + '[' +
                            element.short_code + '] ' + element.name + '</option>');
                    }
                });
            }
        });
    }

    function loadbs(selectedbs) {
        let id = $('#village').val();
        $('#bs').empty();
        $('#bs').append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/jadwal-panen/bs/' + id,
            success: function(response) {
                var response = JSON.parse(response);
                console.log('/jadwal-panen/bs/' + id)
                console.log(selectedbs)
                console.log(response)
                $('#bs').empty();
                $('#bs').append(`<option value="0" disabled selected>Pilih Blok Sensus</option>`);
                response.forEach(element => {
                    if (selectedbs == String(element.id)) {
                        $('#bs').append('<option value=\"' + element.id + '\" selected>' +
                            element.name + '</option>');
                    } else {
                        $('#bs').append('<option value=\"' + element.id + '\">' +
                            element.name + '</option>');
                    }
                });
            }
        });
    }
</script>

@endsection