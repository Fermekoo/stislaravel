@extends('base')
@section('content')
<style>
    .digital-clock {
        margin: auto;
        position: absolute;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        width: 200px;
        height: 60px;
        color: #ffffff;
        border: 2px solid #999;
        border-radius: 4px;
        text-align: center;
        font: 50px/60px 'DIGITAL', Helvetica;
        background: linear-gradient(90deg, #000, #555);
    }
</style>
<section class="section">
    <div class="section-header">
        <h1>Absensi</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body">
                            @include('partials._alert')
                            <form class="form-horizontal" id="form_action" method="POST" action="{{ route('al.attendance.submit') }}">
                                @csrf
                                <div class="form-body">
                                    <input type="hidden" class="form-control ui-timepicker-input" id="longitude" name="longitude">
                                    <input type="hidden" class="form-control ui-timepicker-input" id="latitude" name="latitude">
                                    <div class="form-group col-md-10 row mx-auto">
                                        <label class="col-md-4 label-control">JAM SEKARANG <span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <div class="input-group">{{ date('d-M-Y') }} &nbsp;<strong id="liveTime"></strong></div>
                                        </div>
                                    </div>
                                    @if(!$attendance)
                                    <div class="form-group col-md-10 row mx-auto">
                                        <label class="col-md-4 label-control">BATAS JAM MASUK <span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group">{{ date('d-M-Y') }} &nbsp;<strong>{{ $time_config->limit_check_in }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                    @elseif($attendance && !$attendance->check_out) 
                                    <div class="form-group col-md-10 row mx-auto">
                                        <label class="col-md-4 label-control">MINIMUM JAM PULANG <span class="required">*</span></label>
                                        <div class="col-sm-6">
                                            <div class="input-group">
                                                <div class="input-group">{{ date('d-M-Y') }} &nbsp;<strong>{{ $time_config->check_out }}</strong></div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="text-right col-md-6">
                                        @if($attendance && $attendance->check_out)
                                        <strong>Anda telah melakukan absensi untuk hari ini</strong>
                                        @else
                                        <button type="submit" id="saveBtn" class="btn btn-success">KONFIRMASI {{ ($attendance && !$attendance->check_out) ? 'KEPULANGAN' : 'KEHADIRAN' }}</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script>
    $(document).ready(function() {
        getLocation();
        getdate();
        $('.alert').delay(5000).fadeOut();
    }); //end ready 

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        }
    }

    function showPosition(position) {
        $('#longitude').val(position.coords.longitude);
        $('#latitude').val(position.coords.latitude);
    }

    function getdate() {
        var today = new Date();
        var h = today.getHours();
        var m = today.getMinutes();
        var s = today.getSeconds();
        if (s < 10) {
            s = "0" + s;
        }

        $("#liveTime").text(h + ":" + m + ":" + s);
        setTimeout(function() {
            getdate()
        }, 500);
    }
</script>
@endpush