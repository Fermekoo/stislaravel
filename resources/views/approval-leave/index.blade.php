@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Cuti Karyawan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Cuti Karyawan</h4>
                <div class="d-flex flex-row">

                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>Kode Karyawan</th>
                                <th>Nama Karyawan</th>
                                <th>Nama Perusahaan</th>
                                <th>Cuti</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Durasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
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
@include('approval-leave._modal')
@endsection
@push('js')
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var table = $('#index-table').DataTable({
            paging: true,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: '{{ route("al.approval.json") }}'
            },
            columns: [{
                    data: 'employee_code',
                    name: 'employee_code'
                },
                {
                    data: 'employee',
                    name: 'employee'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'leave',
                    name: 'leave'
                },
                {
                    data: 'start_leave',
                    name: 'start_leave'
                },
                {
                    data: 'end_leave',
                    name: 'end_leave'
                },
                {
                    data: 'duration',
                    name: 'duration'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    render: function(data, type, row) {
                        return (row.status == 'Request') ? `@can('cuti-update')<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Ubah Status" data-id="${row.id}" title ="Ubah Status"><i class="fa fa-edit"></i></button>@endcan` : ''
                    }
                },
            ]
        })

        $('#index-table').on('click', '.btnedit', function() {

            let id = $(this).data('id');
            $('#id').val(id);
            $('#form_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#btn-submit').click(function(e) {
            e.preventDefault();

            let form = $('#form_action');

            $.ajax({
                type: `POST`,
                url: `{{ route('al.approval.submit') }}`,
                data: form.serialize(),
                dataType: `json`,
                success: function(res) {
                    table.ajax.reload();
                    $('#form_modal').modal('hide');
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
                            if (msg.status) {
                                $('#status').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.status}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#company_modal').on('hidden.bs.modal', function() {
            $('#id').val(id);
            $('#status').val('').trigger('change');
            clearError();
        });
    });

    function clearError() {
        let form = $('#form_action');
        form.find('.invalid-feedback').remove();
        form.find('.form-control').removeClass('is-invalid');
    }
</script>
@endpush