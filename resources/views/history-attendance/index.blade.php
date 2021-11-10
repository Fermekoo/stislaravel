@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Absensi Karyawan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <form action="{{ route('al.history.excel') }}" method="GET">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Absensi Karyawan</h4>
                <div class="d-flex flex-row">
                    <button type="button" class="btn btn-primary" data-toggle="collapse" data-target="#filter">Filter</button>&nbsp;<button type="submit" class="btn btn-primary" id="exportBtn">Export to Excel</button>
                </div>
            </div>
            <div class="card-body">
                <div class="collapse" id="filter">
                    <div class="form-group row">
                        @if(auth()->user()->user_type == 'admin')
                        <div class="col-md-6 mb-3">
                            <label>Nama Perusahaan</label>
                            <select class="form-control select2" name="perusahaan" id="perusahaan">
                                <option value="all">Semua</option>
                                @foreach($companies as $company)
                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <label>Nama Karyawan</label>
                            <select class="form-control select2" name="karyawan" id="karyawan">
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>Tanggal Absensi</label>
                            <input type="text" id="tanggal" name="tanggal" class="form-control">
                            </select>
                        </div>
                        <input type="hidden" id="start_date" name="start_date">
                        <input type="hidden" id="end_date" name="end_date">
                    </div>
                </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Perusahaan</th>
                                <th>Tanggal Absensi</th>
                                <th>Jam Masuk</th>
                                <th>Status Kehadiran</th>
                                <th>Jam Pulang</th>
                                <th>Status Pulang</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
            </div>
        </div>
    </div>
</section>
@endsection
@push('js')
<script>
    $(document).ready(function() {

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        `@if(auth()->user()->user_type != 'admin')`
        getEmployee();
        `@endif`

        var table = $('#index-table').DataTable({
            paging: true,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: '{{ route("al.history.json") }}',
                data: function(d) {
                    d.perusahaan = $('#perusahaan').val();
                    d.karyawan = $('#karyawan').val();
                    d.start_date = $('#start_date').val();
                    d.end_date = $('#end_date').val();
                }
            },
            columns: [{
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'check_in',
                    name: 'check_in'
                },
                {
                    data: 'check_in_status',
                    name: 'check_in_status'
                },
                {
                    data: 'check_out',
                    name: 'check_out'
                },
                {
                    data: 'check_out_status',
                    name: 'check_out_status'
                }
            ]
        });

        $('#tanggal').daterangepicker({
            locale: {
                format: 'DD-MM-YYYY',
                separator: " sampai "

            },
            autoApply: true
        });

        $('#tanggal').val('');

        $('#tanggal').on('apply.daterangepicker', function(ev, picker) {
            $('#start_date').val(picker.startDate.format('YYYY-MM-DD'));
            $('#end_date').val(picker.endDate.format('YYYY-MM-DD'));
            table.draw();
        });


        $('#perusahaan, #karyawan, #start_date, #end_date').change(function() {
            table.draw();
        });

        $('#perusahaan').change(function() {
            getEmployee();
        });

        // $('#exportBtn').click(function() {
        //     $.ajax({
        //         type: `POST`,
        //         url: `{{ route('al.history.excel') }}`,
        //         success: function(res) {
        //             iziToast.success({
        //                 title: 'Download berhasil',
        //                 position: 'topRight'
        //             });
        //         }
        //     })
        // })
    });

    function getEmployee() {
        let company_id = $('#perusahaan').val();
        let action = `{{ route('data.employees',':id') }}`;

        $.ajax({
            type: `GET`,
            url: action.replace(':id', company_id),
            success: function(res) {
                let employees = res.data;
                let employee_options = '<option value="all">Semua</option>';
                employees.forEach((employee) => {
                    employee_options += `<option value="${employee.id}">${employee.name}</option>`
                });

                $('#karyawan').html(employee_options);
            }
        });
    }
</script>
@endpush