@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>API KEY</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data API Key</h4>
                <div class="d-flex flex-row">
                    @can('api-key-create')
                    <button class="btn btn-success add-satuan" id="add-data">Tambah Data</button>
                    @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Perusahaan</th>
                                <th>Key</th>
                                <th width="20%">IP Whitelist</th>
                                <th>Status</th>
                                <th>Created By</th>
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
@include('apikey._modal')
@include('apikey._modal-ip')
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
                url: '{{ route("apikey.json") }}'
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'company',
                    name: 'company'
                },
                {
                    data: 'api_key',
                    name: 'api_key'
                },
                {
                    data: 'whitelist_ip',
                    render: function(data, type, row) {
                        let list_ip = row.whitelist_ip;
                        let list = '';
                        if(list_ip){
                            list_ip.forEach((ip) => {
                                list += `<li>${ip}</li>`
                        })
                        }
                        
                        return list;
                    }
                },
                {
                    data: 'status',
                    render: function(data, type, row) {
                        let status = (row.is_active) ? 'Aktif' : 'Nonaktif'
                        let btnColor = (row.is_active) ? 'info' : 'warning';
                        return `@can('api-key-update')<button data-id="${row.id}" data-status="${status}" class="btn btn-${btnColor} btn-xs btnstatus" data-toggle="tooltip" data-original-title="Ubah Status"  title ="Ubah Status" >${status}</button>@endcan`
                    }
                },
                {
                    data: 'creator',
                    name: 'creator'
                },
                {
                    data: 'action',
                    render: function(data, type, row) {
                        return `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}" data-key="${row.api_key}" data-ip_strict="${row.is_strict_ip}" data-whitelist="${row.whitelist_ip}"  title ="Edit"><i class="fa fa-edit"></i></button>         @can('api-key-delete')<button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>@endcan`
                    }
                },
            ]
        })

        $('#add-data').click(function() {
            titleCaption('Tambah Data', 'Simpan');

            $('#customFields .ipv4').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });

            $('#customFields').hide();

            $('#company_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#index-table').on('click', '.btnedit', function(){

            $('#customFieldsIP .ipv4').mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });

            let strict_ip = $(this).data('ip_strict');

            $('#id').val($(this).data('id'));
            $('#apikey').val($(this).data('key'));
            $('#whitelistIPx').prop('checked', strict_ip);
            $('#modal_ip').modal('show');

            let whitelist_ip = $(this).data('whitelist');
    
            if (strict_ip) {
                $('#customFieldsIP').show();
                let list_ip = ``;
                whitelist_ip.split(',').forEach((ip, key) => {
                    let btn = (key == 0 ) ? `<button type="button" class="btn btn-success addCF"><i class="fa fa-plus"></i></button>` : `<button type="button" class="btn btn-danger remCF"><i class="fa fa-trash"></i></button>`
                    list_ip += `<div class="form-group baru-data">
								<div class="input-group mb-3">
									<input type="text" class="form-control ipv4" value="${ip}" name="IPwhitelist[]">
									<div class="input-group-append">
										${btn}
									</div>
								</div>
							</div>`
                });

                $('#customFieldsIP').html(list_ip)

            } else {
                $('#customFieldsIP').hide();
            }

        });

        $('#index-table').on('click', '.btndelete', function() {
            let id = $(this).data('id');
            let action = `{{ route('apikey.delete',':id') }}`;

            swal({
                    title: 'Apakah anda yakin ingin menghapus data ini?',
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
            let id = $(this).data('id');
            let action = `{{ route('apikey.change-status',':id') }}`;
            let status = $(this).data('status');
            status = (status == 'Aktif') ? 'Nonaktif' : 'Aktif'

            swal({
                    title: 'Apakah anda yakin ingin mengubah status key ini?',
                    text: `Seluruh penggunaan key ini akan ${status}`,
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
            $('#btn-submit').addClass('disabled btn-progress');
            let form = $('#form_action');
            $.ajax({
                type: `POST`,
                url: `{{ route('apikey.create') }}`,
                data: form.serialize(),
                dataType: `json`,
                success: function(res) {
                    table.ajax.reload();
                    $('#btn-submit').removeClass('disabled btn-progress');
                    $('#company_modal').modal('hide');
                    iziToast.success({
                        title: res.message,
                        position: 'topRight'
                    });
                },
                error: function(xhr) {
                    clearError();
                    $('#btn-submit').removeClass('disabled btn-progress');
                    let res = xhr.responseJSON;
                    if ($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if (msg instanceof Object) {
                            if (msg.perusahaan) {
                                $('#perusahaan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.perusahaan}</div>`)
                            }
                            if (msg.IPwhitelist) {
                                $('#IPwhitelist').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.IPwhitelist}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        });

        $('#btn-submit-ip').click(function(e) {
            e.preventDefault();
            $('#btn-submit-ip').addClass('disabled btn-progress');
            let form = $('#form_ip_action');
            $.ajax({
                type: `PUT`,
                url: `{{ route('apikey.update') }}`,
                data: form.serialize(),
                dataType: `json`,
                success: function(res) {
                    $('#btn-submit-ip').removeClass('disabled btn-progress');
                    table.ajax.reload();
                    $('#modal_ip').modal('hide');
                    iziToast.success({
                        title: res.message,
                        position: 'topRight'
                    });
                },
                error: function(xhr) {
                    clearError();
                    $('#btn-submit-ip').removeClass('disabled btn-progress');
                    let res = xhr.responseJSON;
                    if ($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if (msg instanceof Object) {
                            if (msg.perusahaan) {
                                $('#perusahaan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.perusahaan}</div>`)
                            }
                            if (msg.IPwhitelist) {
                                $('#IPwhitelist').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.IPwhitelist}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        });

        $('#company_modal').on('hidden.bs.modal', function() {
            $('#id').val('');
            $('#perusahaan').val('').trigger('change');
            $('#customFields').hide()
            $('#customFields .ipv4').val('')
            $('#whitelistIP').prop('checked', false);
            clearError();
        });

        $('#modal_ip').on('hidden.bs.modal', function() {
            $('#id').val('');
            $('#perusahaan').val('').trigger('change');
            $('#customFieldsIP').hide()
            $('#customFieldsIP .ipv4').val('')
            $('#whitelistIPx').prop('checked', false);
            clearError();
        });

        $('#customFields').on('keyup', '.ipv4', function() {
            $(this).mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });
        })

        $('#customFieldsIP').on('keyup', '.ipv4', function() {
            $(this).mask('0ZZ.0ZZ.0ZZ.0ZZ', {
                translation: {
                    'Z': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });
        })

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