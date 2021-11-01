@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Master Perusahaan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Perusahaan</h4>
                <div class="d-flex flex-row">
                    @can('mst-perusahaan-create')
                    <button class="btn btn-success add-satuan" id="add-data">Tambah Data</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>Kode Perusahaan</th>
                                <th>Nama Perusahaan</th>
                                <th>Nomor Telpon</th>
                                <th>Alamat</th>
                                <th width="15%">Status</th>
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
@include('master.company._modal')
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
                url: '{{ route("master.company.json") }}'
            },
            columns: [{
                    data: 'company_code',
                    name: 'company_code'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'phone',
                    name: 'phone'
                },
                {
                    data: 'address',
                    name: 'address'
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        let btnColor = (row.status == 'Active') ? 'info' : 'warning'
                        return `@can('mst-perusahaan-update')<button class="btn btn-${btnColor} btn-xs btnstatus" data-toggle="tooltip" data-original-title="Ubah Status" data-id="${row.id}" data-status="${row.status}" title ="Edit">${row.status}</button>@else ${row.status} @endif`
                    }
                },
                {
                    data: 'action',
                    render: function(data, type, row) {
                        return `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}"  title ="Edit"><i class="fa fa-edit"></i></button> 
                    @can('mst-perusahaan-delete')<button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>@endcan`
                    }
                },
            ]
        })

        $('#add-data').click(function() {
            titleCaption('Tambah Data', 'Simpan');
            $('#pw_caption').hide();
            $('#company_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#index-table').on('click', '.btnedit', function() {
            $('#pw_caption').show();
            let id = $(this).data('id');
            let action = `{{ route('master.company.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res) {
                    let data = res.data;
                    $('#id').val(data.id);
                    $('#namaPerusahaan').val(data.name);
                    $('#nomorTelpon').val(data.phone);
                    $('#alamat').val(data.address);
                    $('#username').val(data.admin.username);

                    titleCaption('Edit Data', 'Ubah');

                    $('#company_modal').modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    })
                }

            })
        });

        $('#index-table').on('click', '.btndelete', function() {
            let id = $(this).data('id');
            let action = `{{ route('master.company.delete',':id') }}`;

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

        $('#index-table').on('click', '.btnstatus', function() {
            let id      = $(this).data('id');
            let action  = `{{ route('master.company.status',':id') }}`;
            let status  = $(this).data('status');
                status  = (status == 'Active') ? 'Nonaktif' : 'Aktif'

            swal({
                title: 'Apakah anda yakin ingin mengubah status perusahaan ini?',
                text: `Seluruh data user dari perusahaan ini akan ${status}`,
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            })
            .then((willUpdate) => {
                if (willUpdate) {
                    $.ajax({
                        type: 'POST',
                        url: action.replace(':id', id),
                        success: function(res) {
                            swal({
                                title: 'Ubah Status',
                                text: res.message,
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

            let form = $('#form_action');

            $.ajax({
                type: `POST`,
                url: `{{ route('master.company.submit') }}`,
                data: form.serialize(),
                dataType: `json`,
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
                            if (msg.namaPerusahaan) {
                                $('#namaPerusahaan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.namaPerusahaan}</div>`)
                            }
                            if (msg.alamat) {
                                $('#alamat').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.alamat}</div>`)
                            }
                            if (msg.nomorTelpon) {
                                $('#nomorTelpon').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.nomorTelpon}</div>`)
                            }
                            if (msg.username) {
                                $('#username').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.username}</div>`)
                            }
                            if (msg.password) {
                                $('#password').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.password}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#company_modal').on('hidden.bs.modal', function() {
            $('#id').val('');
            $('#namaPerusahaan').val('');
            $('#nomorTelpon').val('');
            $('#alamat').val('');
            $('#username').val('');
            $('#password').val('');
            clearError();
        });
    });

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