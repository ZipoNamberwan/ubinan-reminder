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
                            <li class="breadcrumb-item"><a href="/jadwal-ubinan">Jadwal Survei Ubinan</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{$schedule->id}}</li>
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
                    <div class="card-header pb-0">
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <h3>Perkiraan Jadwal Panen</h3>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="formupdate" autocomplete="off" method="post" action="/perkiraan-jadwal-panen/{{$schedule->id}}" class="needs-validation" enctype="multipart/form-data" novalidate>
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h4">
                                        Identitas Sampel
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-0"><span class="badge badge-primary">{{$schedule->commodity->id != 1 ? $schedule->bs->fullcode() : $schedule->bs->fullcodesegment() . sprintf('%02d', $schedule->segment)}}</span></p>
                                    <p class="mb-0"><span class="badge badge-success"> {{$schedule->commodity->id != 1 ? $schedule->bs->fullname() : $schedule->bs->fullnamesegment()}} </span></p>
                                    <p class="mb-0"><span class="badge badge-warning"> {{$schedule->commodity->id != 1 ? $schedule->nks : $schedule->subSegment->code}} </span></p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p class="h4">
                                        @if($schedule->commodity->id != 1)
                                        Responden
                                        @else
                                        Subsegmen
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if($schedule->commodity->id != 1)
                                    <p class="mb-0">{{ucfirst(strtolower($schedule->name))}} ({{$schedule->sample_number}})</p>
                                    <p>{{ucfirst(strtolower($schedule->address))}} </p>
                                    @else
                                    <p>{{$schedule->subSegment->code}}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h4">
                                        Komoditas
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>{{$schedule->commodity->name}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h4">
                                        Jenis Sampel
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>{{$schedule->sampleType->name}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h4">
                                        Bulan Panen
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p>{{$schedule->month->name}}</p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="h4">
                                        Tanggal Perkiraan Panen
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <div>
                                        <input onchange="dateChange()" min="{{$mindate}}" max="{{$maxdate}}" name="date" id="date" class="form-control @error('date') is-invalid @enderror" placeholder="Select date" type="date" value="{{ @old('date', $schedule->harvestSchedule->date) }}">
                                        @error('date')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="mt-4"><strong id="date_text" class="p-2" style="border: 3px solid red;"></strong></div>
                                </div>

                            </div>
                            <div class="row">
                                <div class="col">
                                    <button onclick="submitClick()" class="btn btn-primary mt-3" id="submitBtn" type="submit">Simpan</button>
                                </div>
                            </div>
                        </form>
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
<script src="/assets/vendor/momentjs/moment-with-locales.js"></script>

<script>
    function dateChange() {
        if (document.getElementById('date').value != null && document.getElementById('date').value != '') {
            let localLocale = moment(document.getElementById('date').value);
            localLocale.locale('id');
            var date_text = localLocale.format('LL')
            document.getElementById('date_text').innerHTML = date_text
            document.getElementById('date_text').style.display = 'block'
        } else {
            document.getElementById('date_text').style.display = 'none'
        }
    }

    function submitClick() {
        event.preventDefault();
        let date_text = document.getElementById('date_text').innerHTML
        if (date_text != null && date_text != '') {
            Swal.fire({
                title: 'Simpan Perkiraan tanggal panen?',
                text: date_text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('formupdate').submit();
                }
            })
        } else {
            Swal.fire({
                title: 'Perkiraan Tanggal Panen Kosong',
                icon: 'error',
            })
        }

    }

    dateChange()
</script>
@endsection