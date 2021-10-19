@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Jatah Cuti Karyawan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Jatah Cuti Karyawan</h4>
                <div class="d-flex flex-row">
                    <!-- <button class="btn btn-primary add-satuan" id="add-data">Tambah Data</button> -->
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>Kode Karyawan</th>
                                <th>Nama</th>
                                <th>Perusahaan</th>
                                <th>Divisi</th>
                                <th>Jabatan</th>
                                <th width="10%">Jatah Cuti</th>
                                <th width="15%">Aksi</th>
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
@include('leave-quota._modal')
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
                url: '{{ route("al.employee.json") }}'
            },
            columns: [{
                    data: 'employee_code',
                    name: 'employee_code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'division',
                    name: 'division'
                },
                {
                    data: 'position',
                    name: 'position'
                },
                {
                    data: 'quota',
                    name: 'quota'
                },
                {
                    data: 'action',
                    render: function(data, type, row){
                        return `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}" data-name="${row.name}" title ="Edit">Atur Cuti</button>`;
                    }
                }
            ]
        })

        $('#perusahaan').on('change', function() {
            let company_id = $(this).val();
            let state = $('#state').val();
            if(company_id && state == 'create') {
                getDivision(company_id);
                getPosition(company_id);
                getLevel(company_id);
                getType(company_id);
            }
        });

        $('#index-table').on('click', '.btnedit', function() {

            $('#pw_caption').show();
            let id = $(this).data('id');
            let name = $(this).data('name');
            let action = `{{ route('al.leave-quota-employee',':id') }}`;
            $('#id').val(id);
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res) {
                    let data = res.data;
                    let html = '';
                    data.forEach((item, key) => {
                        html += `<div class="form-group">
						<label>${item.leave_type}</label>
						<input type="hidden" name="quota[${key}][leave_type_id]" value="${item.leave_type_id}">
						<input type="text" class="form-control" name="quota[${key}][qty]" value="${item.available_quota}">
					</div>`
                    });
                    $('#cuti').html(html);
                    titleCaption(`Jatah Cuti ${name}`, 'Simpan');

                    $('#company_modal').modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            });
        });

        $('#btn-submit').click(function(e) {
            e.preventDefault();

            let id = $('#id').val();
            let action = `{{ route('al.leave-quota-employee',':id') }}`;
            let form = $('#form_action');
            $.ajax({
                type: `POST`,
                url: action.replace(':id', id),
                data: form.serialize(),
                dataType: `json`,
                success: function(res) {
                    console.log(res);
                    table.ajax.reload();
                    $('#company_modal').modal('hide');
                    iziToast.success({
                        title: res.message,
                        position: 'topRight'
                    });
                }
            })
        })

        $('#company_modal').on('hidden.bs.modal', function() {
            let default_img = `{{ asset('assets/img/avatar/avatar-1.png') }}`;
            $('#id').val('');
            $('#state').val('');
            $('#perusahaan').val('').trigger('change');
            $('#avatar').val('');
            $('#namaLengkap').val('');
            $('#nomorHp').val('');
            $('#alamat').val('');
            $('#username').val('');
            $('#password').val('');
            $('#divisi').val('').trigger('change');
            $('#jabatan').val('').trigger('change');
            $('#golongan').val('').trigger('change');
            $('#status').val('').trigger('change');
            $('#img-upload').attr('src', default_img)
            clearError();
        });

    }); // end document ready

    function titleCaption(title, button) {
        $('#modal_title').text(title);
        $('#btn-submit').text(button);
    }

    function clearError() {
        let form = $('#form_action');
        form.find('.invalid-feedback').remove();
        form.find('.form-control').removeClass('is-invalid');
    }
</script>
@endpush