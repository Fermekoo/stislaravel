@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Setting Absensi</h1>
    </div>

    <div class="section-body">
        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-content">
                        <div class="card-body">

                            <form class="form-horizontal" id="form_action">
                                <div class="form-body">
                                    @if(auth()->user()->user_type == 'admin')
                                    <div class="form-group col-md-10 row">
                                        <label class="col-md-2 label-control">PERUSAHAAN <span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <select class="form-control select2" id="perusahaan" name="perusahaan" required>
                                                <option value="">-PILIH-</option>
                                                @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    @endif

                                    <div class="form-group col-md-10 row">

                                        <label class="col-md-2 label-control">JAM MASUK <span class="required">*</span></label>

                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="time" class="form-control" id="jamMasuk" name="jamMasuk" required>
                                                <!-- <div class="input-group-append">
                                                    <button class="input-group-text btn-info" type="button" id="setTimebtn" tabindex="-1">Set Jam Sekarang</button>
                                                </div> -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-10 row">
                                        <label class="col-md-2 label-control">BATAS JAM MASUK <span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="time" class="form-control ui-timepicker-input" id="batasJamMasuk" name="batasJamMasuk" required>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-10 row">
                                        <label class="col-md-2 label-control">JAM PULANG <span class="required">*</span></label>
                                        <div class="col-md-8">
                                            <input type="time" class="form-control ui-timepicker-input" id="jamPulang" name="jamPulang" required>
                                        </div>
                                    </div>

                                    <div class="text-right col-md-8">
                                        <button type="submit" id="saveBtn" class="btn btn-success">Simpan Data <i class="la la-save position-right"></i></button>
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
    $(document).ready(function(){
        $.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

        let company_id = `{{ auth()->user()->company_id }}`;
        if(company_id > 0) {
            getTimeConfig(company_id);
        }

        $('#perusahaan').on('change', function(){
            let company_id = $(this).val();

            getTimeConfig(company_id);
        });

        $('#setTimebtn').on('click', function(){
            let time_now = `{{ date('H:i') }}`;
            $('#jamMasuk').val(time_now);
        });

        $('#saveBtn').on('click', function(e){
            e.preventDefault();

            let form = $('#form_action');
            $.ajax({
                type: `POST`,
                url: `{{ route('al.time-config.submit') }}`,
                data: form.serialize(),
                dataType: `json`,
                success: function(res){
                    clearError();
                    iziToast.success({
                        title: res.message,
                        position: 'topRight'
                    });
                },
                error: function(xhr) {
                    clearError();
                    let res = xhr.responseJSON;
                    if ($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if (msg instanceof Object) {
                            if (msg.perusahaan) {
                                $('#perusahaan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.perusahaan}</div>`)
                            }
                            if (msg.jamMasuk) {
                                $('#jamMasuk').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jamMasuk}</div>`)
                            }
                            if (msg.batasJamMasuk) {
                                $('#batasJamMasuk').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.batasJamMasuk}</div>`)
                            }
                            if (msg.jamPulang) {
                                $('#jamPulang').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jamPulang}</div>`)
                            }
                            
                            return
                        }
                        iziToast.error({
                            title: res.error_message,
                            position: 'topRight'
                        });
                    }
                }
            })
        });

    }); //end ready 

    function getTimeConfig(company_id)
    {
        let action = `{{ route('data.time-config',':company_id') }}`;
        $.ajax({
            type: `GET`,
            url: action.replace(':company_id', company_id),
            success: function(res){
                let data = res.data;
                $('#jamMasuk').val(data.check_in);
                $('#batasJamMasuk').val(data.limit_check_in);
                $('#jamPulang').val(data.check_out);
            }
        })
    }

    function clearError() {
        let form = $('#form_action');
        form.find('.invalid-feedback').remove();
        form.find('.form-control').removeClass('is-invalid');
    }
</script>
@endpush