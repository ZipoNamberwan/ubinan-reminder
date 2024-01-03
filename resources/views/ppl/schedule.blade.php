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
                <div class="col">
                    <h6 class="h2 text-white d-inline-block mb-0">Automatic Ubinan Reminder System</h6>
                    <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                        <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                            <li class="breadcrumb-item"><a href="/"><i class="fas fa-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="/">Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Jadwal Survei Ubinan</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Card stats -->
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Sampel Bulan {{$currentmonth->name}}</h5>
                                    <span class="h2 font-weight-bold mb-0">{{$total_sample}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-red text-white rounded-circle shadow">
                                        <i class="fas fa-list"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <!-- Card body -->
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Total Sampel Bulan {{$currentmonth->name}} Yang Belum Dientri Perkiraan Tanggal Panen</h5>
                                    <span class="h2 font-weight-bold mb-0">{{$total_sample_not_entry}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-orange text-white rounded-circle shadow">
                                        <i class="ni ni-chart-pie-35"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-3 mb-0 text-sm">
                                <span class="text-danger mr-2"><i class="ni ni-chart-pie-35"></i> @if($total_sample > 0) {{round(($total_sample-$total_sample_not_entry)/$total_sample*100, 0)}}% sudah terisi @endif</span>
                            </p>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-xl-3 col-md-6">
                    <div class="card card-stats">
                        <div class="card-body">
                            <div class="row">
                                <div class="col">
                                    <h5 class="card-title text-uppercase text-muted mb-0">Jenis Sampel Bulan {{$currentmonth->name}}</h5>
                                    <span class="h4 font-weight-bold mb-0">{{$sample_types_string}}</span>
                                </div>
                                <div class="col-auto">
                                    <div class="icon icon-shape bg-gradient-info text-white rounded-circle shadow">
                                        <i class="ni ni-chart-bar-32"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> -->
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
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <h3>Jadwal Survei Ubinan</h3>
                                <p>Scroll ke bawah untuk melihat sampel</p>
                            </div>
                        </div>
                    </div>
                    <!-- Card body -->
                    <div class="card-body">
                        <div class="form-row">
                            <div class="col-md-4">
                                <label class="form-control-label mb-3" for="month">Bulan</label>
                                <select class="form-control d-inline" data-toggle="select" name="month" id="month">
                                    @foreach($months as $month)
                                    <option value="{{$month->id}}" @if($month->id==$currentmonth->id) selected @endif> {{$month->name}} @if ($month->sample_num > 0) ({{$month->sample_num}}) @endif</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <button onclick="getDataByMonth()" class="btn btn-primary mt-3 d-inline" type="button">
                                <span class="btn-inner--icon"><i class="fas fa-eye"></i></span>
                                <span class="btn-inner--text">Tampilkan</span>
                            </button>
                        </div>
                    </div>
                </div>
                @if (count($schedules) == 0)
                <div class="card">
                    <div class="card-body">
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <span class="alert-icon"><i class="fas fa-check-circle"></i></span>
                            <span class="alert-text"><strong>Belum ada jadwal bulan ini</strong></span>
                        </div>
                    </div>
                </div>
                @endif

                @foreach($schedules as $schedule)
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Identitas Sampel
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                <h2 class="mb-0"><span class="badge badge-primary">{{$schedule->bs->fullcode()}}</span></h2>
                                <h2 class="mb-0"><span class="badge badge-primary"> {{$schedule->bs->fullname()}} </span></h2>
                                <h2 class="mb-0"><span class="badge badge-primary"> {{$schedule->nks}} </span></h2>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Responden
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                <p class="mb-0">{{ucfirst(strtolower($schedule->name))}} ({{$schedule->sample_number}})</p>
                                <p>{{ucfirst(strtolower($schedule->address))}} </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Komoditas
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                <p>{{$schedule->commodity->name}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Jenis Sampel
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                <p>{{$schedule->sampleType->name}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Bulan Panen
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                <p>{{$schedule->month->name}}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">
                                    Tanggal Perkiraan Panen
                                </p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                @if($schedule->harvestSchedule != null)
                                <h1>
                                    <span class="badge badge-success" id="{{$schedule->id}}_date" class="card-title text-white m-0">
                                    </span>
                                </h1>
                                @else
                                <h1>
                                    <span class="badge badge-danger">Belum Diisi</span>
                                </h1>
                                @endif
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6 col-md-3">
                                <p class="h4">Aksi</p>
                            </div>
                            <div class="col-sm-6 col-md-9">
                                @if($schedule->harvestSchedule != null)
                                <a href="{{url('/perkiraan-jadwal-panen/'.$schedule->id)}}" class="btn btn-info btn-round btn-icon" data-toggle="tooltip" data-original-title="Ubah Tanggal">
                                    <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                                    <span class="btn-inner--text">Ubah Tanggal Perkiraan Panen</span>
                                </a>
                                @else
                                <a href="{{url('/perkiraan-jadwal-panen/'.$schedule->id)}}" class="btn btn-primary btn-round btn-icon" data-toggle="tooltip" data-original-title="Isi Tanggal">
                                    <span class="btn-inner--icon"><i class="fas fa-edit"></i></span>
                                    <span class="btn-inner--text">Isi Tanggal Perkiraan Panen</span>
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
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
<script src="/assets/vendor/momentjs/moment-with-locales.js"></script>

<script>
    function dateChange(id, date) {
        let localLocale = moment(date);
        localLocale.locale('id');
        var date_text = localLocale.format('LL')
        document.getElementById(id).innerHTML = date_text
    }
</script>

<script>
    function getDataByMonth() {
        window.location.href = "{{url('/jadwal-ubinan?')}}&month=" + document.getElementById('month').value;
    }
</script>

@foreach($schedules as $schedule)
@if($schedule->harvestSchedule != null)
<script>
    dateChange('{{$schedule->id}}_date', '{{$schedule->harvestSchedule->date}}')
</script>
@endif
@endforeach
@endsection