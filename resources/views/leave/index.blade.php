@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Pengajuan Izin</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Pengajuan Izin</h4>
                <div class="d-flex flex-row">
                    <button class="btn btn-primary add-satuan" id="add-data">Tambah Data</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th>Jenis Izin</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Keterangan</th>
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
@include('leave._modal')
@endsection
@push('js')
<script>
    $(document).ready(function(){

        $( ".datepicker" ).datepicker({
            changeMonth: true,
            dateFormat: 'yy-mm-dd'
        });

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
                url: '{{ route("al.leave-request.json") }}'
            },
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'request_type', name: 'request_type' },
				{ data: 'start_date', name: 'start_date' },
				{ data: 'end_date', name: 'end_date' },
				{ data: 'description', name: 'description' },
				{ data: 'status', name: 'status' },
				{ data: 'action', render: function(data, type, row){
                    let act = (row.status == 'Pending') ? `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}"  title ="Edit"><i class="fa fa-edit"></i></button> <button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>` : ''
                    return act;
                } },
			]
		})

        $('#add-data').click(function(){
            titleCaption('Tambah Data', 'Simpan');
            
            $('#form_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#index-table').on('click','.btnedit', function(){

            let id = $(this).data('id');
            let action = `{{ route('al.leave-request.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res){
                    let data = res.data;
                    let attachment = `{{ asset('storage/document-izin/${data.document}') }}`;
                    $('#id').val(data.id);
                    $('#tanggalMulai').val(data.start_date);
                    $('#tanggalSelesai').val(data.end_date);
                    $('#keterangan').val(data.description);
                    $('#jenisIzin').val(data.request_type).trigger('change');

                    if(data.document) {
                        $('#link_lampiran').attr('href', attachment);
                        $('#link_lampiran').show();
                    }
                    

                    titleCaption('Edit Data', 'Ubah');

                    $('#form_modal').modal({
                        show: true,
                        backdrop: 'static',
                        keyboard: false
                    })
                }

            })
        });

        $('#index-table').on('click', '.btndelete', function(){
            let id = $(this).data('id');
            let action = `{{ route('al.leave-request.delete',':id') }}`;

            swal({
				title: 'Apakah Anda Yakin Ingin Menghapus izin ini ?',
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
								success: function(res){
										swal({
											title: 'Hapus Data',
											text: 'Hapus Data Sukses',
											icon: 'success'
										});
									table.ajax.reload();
								},
								error: function(xhr){
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

        $('#btn-submit').click(function(e){

            $('#btn-submit').addClass('disabled btn-progress');
            e.preventDefault();
            let form = new FormData($('#form_action')[0]);

            $.ajax({
                type: `POST`,
                url: `{{ route('al.leave-request.submit') }}`,
                data: form,
                contentType: false,
                cache: false,
                processData: false,
                success: function(res) {
                    $('#btn-submit').removeClass('disabled btn-progress');
                    table.ajax.reload();
                    $('#form_modal').modal('hide');
                    iziToast.success({
						title: res.message,
						position: 'topRight'
					});
                },
                error: function(xhr){
                    clearError();
                    $('#btn-submit').removeClass('disabled btn-progress');
                    let res = xhr.responseJSON;
                    if($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if(msg instanceof Object) {
                            if(msg.jenisIzin) {
                                $('#jenisIzin').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jenisIzin}</div>`)
                            }
                            
                            if(msg.tanggalMulai) {
                                $('#tanggalMulai').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.tanggalMulai}</div>`)
                            }
                            if(msg.tanggalSelesai) {
                                $('#tanggalSelesai').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.tanggalSelesai}</div>`)
                            }
                            if(msg.keterangan) {
                                $('#keterangan').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.keterangan}</div>`)
                            }
                            if(msg.document) {
                                $('#document').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.document}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#form_modal').on('hidden.bs.modal', function(){
            $('#id').val('');
            $('#tanggalMulai').val('');
            $('#tanggalSelesai').val('');
            $('#keterangan').val('');
            $('#document').val('');
            $('#jenisIzin').val('').trigger('change');
            $('#link_lampiran').hide();
            clearError();
        });

        $("input[type='date']").on('change', function(){
            calculateLeave();
        })
    });

    function titleCaption(title, button){
        $('#modal_title').text(title);
        $('#btn-submit').text(button);
    }

    function clearError(){
		let form = $('#form_action');
		form.find('.invalid-feedback').remove();
		form.find('.form-control').removeClass('is-invalid');
	}
</script>
@endpush