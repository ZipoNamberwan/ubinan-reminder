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
                                    <label class="form-control-label">Komoditas <span class="text-danger">*</span></label>
                                    <select readonly onchange="onCommodityChange()" id="commodity" name="commodity" class="form-control" data-toggle="select" required>
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
                            </div>
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
                                <div id="bs_div" class="col-md-4 mt-3">
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
                                <div id="no_segment" class="col-md-4 mt-3">
                                    <label class="form-control-label" for="no_segment">No Segment <span class="text-danger">*</span></label>
                                    <input type="number" name="no_segment" class="form-control @error('no_segment') is-invalid @enderror" value="{{ @old('no_segment', $schedule->segment) }}">
                                    @error('no_segment')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div id="subsegment" class="col-md-4 mt-3">
                                    <label class="form-control-label">Sub Segment <span class="text-danger">*</span></label>
                                    <select name="subsegment" class="form-control" data-toggle="select" required>
                                        <option value="0" disabled selected> -- Pilih Sub Segment -- </option>
                                        @foreach ($subsegments as $subsegment)
                                        <option value="{{ $subsegment->id }}" {{ old('subsegment', $schedule->subsegment_id) == $subsegment->id ? 'selected' : '' }}>
                                            {{ $subsegment->code}}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('subsegment')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div id="nks" class="col-md-4 mt-3">
                                    <label class="form-control-label" for="nks">NKS <span class="text-danger">*</span></label>
                                    <input type="number" name="nks" class="form-control @error('nks') is-invalid @enderror" id="validationCustom03" value="{{ @old('nks', $schedule->nks) }}">
                                    @error('nks')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div id="sample_number" class="col-md-4 mt-3">
                                    <label class="form-control-label" for="sample_number">Nomor Sampel <span class="text-danger">*</span></label>
                                    <input type="number" name="sample_number" class="form-control @error('sample_number') is-invalid @enderror" id="validationCustom03" value="{{ @old('sample_number', $schedule->sample_number) }}">
                                    @error('sample_number')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div id="name" class="col-md-4 mt-3">
                                    <label class="form-control-label" for="name">Nama Responden <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" id="validationCustom03" value="{{ @old('name', $schedule->name) }}">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                                <div id="address" class="col-md-4 mt-3">
                                    <div class="form-group">
                                        <label class="form-control-label" for="address">Alamat <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="address" name="address" rows="4">{{old('address', $schedule->address)}}</textarea>
                                    </div>
                                    @error('address')
                                    <div class="text-valid mt-2">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
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
                                    @error('sample-type')
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
            loadVillage(null, null);
        });
        $('#village').on('change', function() {
            loadBs(null, null);
        });
    });

    function loadVillage(subdistrictid = null, selectedvillage = null) {
        let id = $('#subdistrict').val();
        if (subdistrictid != null) {
            id = subdistrictid;
        }
        console.log(id);
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
                            '[' + element.short_code + '] ' + element.name + '</option>');
                    } else {
                        $('#village').append('<option value=\"' + element.id + '\">' + '[' +
                            element.short_code + '] ' + element.name + '</option>');
                    }
                });
            }
        });
    }

    function loadBs(villageid = null, selectedbs = null) {
        let id = $('#village').val();
        if (villageid != null) {
            id = villageid;
        }
        $('#bs').empty();
        $('#bs').append(`<option value="0" disabled selected>Processing...</option>`);
        $.ajax({
            type: 'GET',
            url: '/jadwal-panen/bs/' + id,
            success: function(response) {
                var response = JSON.parse(response);
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

<script>
    function onCommodityChange() {
        var e = document.getElementById("commodity");
        var value = e.value;
        if (value == 1) {
            document.getElementById("bs_div").style.display = 'none'
            document.getElementById("nks").style.display = 'none'
            document.getElementById("sample_number").style.display = 'none'
            document.getElementById("name").style.display = 'none'
            document.getElementById("address").style.display = 'none'

            document.getElementById("subsegment").style.display = 'block'
            document.getElementById("no_segment").style.display = 'block'
        } else {
            document.getElementById("bs_div").style.display = 'block'
            document.getElementById("nks").style.display = 'block'
            document.getElementById("sample_number").style.display = 'block'
            document.getElementById("name").style.display = 'block'
            document.getElementById("address").style.display = 'block'

            document.getElementById("subsegment").style.display = 'none'
            document.getElementById("no_segment").style.display = 'none'
        }

    }

    onCommodityChange()
</script>

@if(@old("subdistrict", $schedule->bs->village->subdistrict->id))
<script>
    loadVillage('{{@old("subdistrict", $schedule->bs->village->subdistrict->id)}}', '{{@old("village", $schedule->bs->village->id)}}')
</script>
@endif

@if(@old("village", $schedule->bs->village->id))
<script>
    loadBs('{{@old("village", $schedule->bs->village->id)}}', '{{@old("bs", $schedule->bs->id)}}')
</script>
@endif

@endsection