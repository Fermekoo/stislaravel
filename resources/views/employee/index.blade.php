@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Karyawan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Karyawan</h4>
                <div class="d-flex flex-row">
                    <button class="btn btn-success add-satuan" id="add-data">Tambah Data</button>
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
                                <th>Gender</th>
                                <th>No HP</th>
                                <th>Status</th>
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
@include('employee._modal')
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
                url: '{{ route("employee.json") }}'
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
                    data: 'gender',
                    name: 'gender'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'action',
                    render: function(data, type, row) {
                        return `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}"  title ="Edit"><i class="fa fa-edit"></i></button> <button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>`
                    }
                },
            ]
        })

        $('#add-data').click(function() {

            titleCaption('Tambah Data', 'Simpan');
            $('#pw_caption').hide();
            $('#state').val('create');
            
            let company_id = `{{ auth()->user()->company_id }}`;

            getDivision(company_id);
            getPosition(company_id);
            getLevel(company_id);
            getType(company_id);

            $('#company_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

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
            let action = `{{ route('employee.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res) {
                    let data = res.data;
                    let img  = `{{ asset('storage/foto-karyawan/${data.avatar}') }}`;

                    getDivision(data.company_id, data.division_id);
                    getPosition(data.company_id, data.position_id);
                    getLevel(data.company_id, data.level_id);
                    getType(data.company_id, data.employee_type_id);
                   
                    $('#id').val(data.id);
                    $('#img-upload').attr('src', img);
                    $('#perusahaan').val(data.company_id).trigger('change');
                    $('#jenisKelamin').val(data.gender).trigger('change');
                    $('#namaLengkap').val(data.name);
                    $('#alamat').val(data.address);
                    $('#nomorHp').val(data.phone);
                    $('#username').val(data.user.username);

                    titleCaption('Edit Data', 'Ubah');

                    $('#company_modal').modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    });
                }
            });
        });

        $('#index-table').on('click', '.btndelete', function() {
            let id = $(this).data('id');
            let action = `{{ route('employee.delete',':id') }}`;

            swal({
                    title: 'Apakah Anda Yakin Ingin Menghapus ?',
                    text: 'Seluruh Data Yang Berkaitan Dengan Data Ini Akan Dihapus',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            type: 'DELETE',
                            url: action.replace(':id', id),
                            success: function(res) {
                                swal({
                                    title: 'Hapus Data',
                                    text: 'Hapus Data Sukses',
                                    icon: 'success'
                                });
                                table.ajax.reload();
                            },
                            error: function(xhr) {
                                var res = xhr.responseJSON;
                                if ($.isEmptyObject(res) == false) {
                                    var errMsg = res.message;
                                    iziToast.error({
                                        title: errMsg,
                                        position: 'topRight'
                                    });
                                }
                            }

                        })
                    }
                });
        });

        $('#btn-submit').click(function(e) {
            e.preventDefault();

            let form = new FormData($('#form_action')[0]);

            $.ajax({
                type: `POST`,
                url: `{{ route('employee.submit') }}`,
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                success: function(res) {
                    table.ajax.reload();
                    $('#company_modal').modal('hide');
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
                            if (msg.alamat) {
                                $('#alamat').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.alamat}</div>`)
                            }
                            if (msg.nomorHp) {
                                $('#nomorHp').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.nomorHp}</div>`)
                            }
                            if (msg.username) {
                                $('#username').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.username}</div>`)
                            }
                            if (msg.password) {
                                $('#password').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.password}</div>`)
                            }
                            if (msg.fotoKaryawan) {
                                $('#avatar').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.fotoKaryawan}</div>`)
                            }
                            if (msg.divisi) {
                                $('#divisi').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.divisi}</div>`)
                            }
                            if (msg.jabatan) {
                                $('#jabatan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jabatan}</div>`)
                            }
                            if (msg.golongan) {
                                $('#golongan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.golongan}</div>`)
                            }
                            if (msg.status) {
                                $('#status').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.status}</div>`)
                            }
                            if (msg.namaLengkap) {
                                $('#namaLengkap').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.namaLengkap}</div>`)
                            }
                            if (msg.jenisKelamin) {
                                $('#jenisKelamin').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jenisKelamin}</div>`)
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

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#img-upload').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#avatar").change(function() {
            $('#img-upload').css({
                width: 150,
                heigth: 150
            })
            readURL(this);
        });
    }); // end document ready

    function getDivision(company_id, div_id) {
        let action = `{{ route('data.division',':id') }}`;
        $.ajax({
            type: `GET`,
            url: action.replace(':id', company_id),
            success: function(res) {
                let data = res.data;
                let html = `<option value="">-PILIH-</option>`;
                data.forEach((item) => {
                    let selected = (div_id == item.id) ? 'selected' : '';
                    html += `<option value="${item.id}" ${selected}>${item.name}</option>`
                });

                $('#divisi').html(html);
            }
        });
    }

    function getPosition(company_id, pos_id) {
        let action = `{{ route('data.position',':id') }}`;
        $.ajax({
            type: `GET`,
            url: action.replace(':id', company_id),
            success: function(res) {
                let data = res.data;
                let html = `<option value="">-PILIH-</option>`;
                data.forEach((item) => {
                    let selected = (pos_id == item.id) ? 'selected' : '';
                    html += `<option value="${item.id}" ${selected}>${item.name}</option>`
                });

                $('#jabatan').html(html);
            }
        });
    }

    function getLevel(company_id, level_id) {
        let action = `{{ route('data.employee-level',':id') }}`;
        $.ajax({
            type: `GET`,
            url: action.replace(':id', company_id),
            success: function(res) {
                let data = res.data;
                let html = `<option value="">-PILIH-</option>`;
                data.forEach((item) => {
                    let selected = (level_id == item.id) ? 'selected' : '';
                    html += `<option value="${item.id}" ${selected}>${item.name}</option>`
                });

                $('#golongan').html(html);
            }
        });
    }

    function getType(company_id, type_id) {
        let action = `{{ route('data.employee-type',':id') }}`;
        $.ajax({
            type: `GET`,
            url: action.replace(':id', company_id),
            success: function(res) {
                let data = res.data;
                let html = `<option value="">-PILIH-</option>`;
                data.forEach((item) => {
                    let selected = (type_id == item.id) ? 'selected' : ''
                    html += `<option value="${item.id}" ${selected}>${item.name}</option>`
                });

                $('#status').html(html);
            }
        });
    }

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