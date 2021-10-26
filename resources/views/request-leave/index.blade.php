@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Pengajuan Cuti</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Pengajuan Cuti</h4>
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
                                <th>Jenis Cuti</th>
                                <th>Tanggal Mulai</th>
                                <th>Tanggal Selesai</th>
                                <th>Durasi</th>
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
@include('request-leave._modal')
@endsection
@push('js')
<script>
    $(document).ready(function(){

        $( ".datepicker" ).datepicker({
            onSelect: function(){
                calculateLeave();
            },
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
                url: '{{ route("al.request-leave.json") }}'
            },
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'leave', name: 'leave' },
				{ data: 'start_leave', name: 'start_leave' },
				{ data: 'end_leave', name: 'end_leave' },
				{ data: 'duration', name: 'duration' },
				{ data: 'status', name: 'status' },
				{ data: 'action', render: function(data, type, row){
                    let act = (row.status == 'Request') ? `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}"  title ="Edit"><i class="fa fa-edit"></i></button> <button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>` : ''
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
            let action = `{{ route('al.request-leave.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res){
                    let data = res.data;
                    $('#id').val(data.id);
                    $('#tanggalMulaiCuti').val(data.start_leave);
                    $('#tanggalSelesaiCuti').val(data.end_leave);
                    $('#durasi').val(data.duration);
                    $('#keterangan').val(data.description);
                    $('#jenisCuti').val(data.leave_type_id).trigger('change');

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
            let action = `{{ route('al.request-leave.delete',':id') }}`;

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
            e.preventDefault();

            let form = $('#form_action');

            $.ajax({
                type: `POST`,
                url: `{{ route('al.request-leave.submit') }}`,
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
                error: function(xhr){
                    clearError();
                    let res = xhr.responseJSON;
                    if($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if(msg instanceof Object) {
                            if(msg.jenisCuti) {
                                $('#jenisCuti').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.jenisCuti}</div>`)
                            }
                            if(msg.durasi) {
                                $('#durasi').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.durasi}</div>`)
                            }
                            if(msg.tanggalMulaiCuti) {
                                $('#tanggalMulaiCuti').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.tanggalMulaiCuti}</div>`)
                            }
                            if(msg.tanggalSelesaiCuti) {
                                $('#tanggalSelesaiCuti').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.tanggalSelesaiCuti}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#form_modal').on('hidden.bs.modal', function(){
            $('#id').val('');
            $('#tanggalMulaiCuti').val('');
            $('#tanggalSelesaiCuti').val('');
            $('#durasi').val('');
            $('#keterangan').val('');
            $('#jenisCuti').val('').trigger('change');
            clearError();
        });

        $("input[type='date']").on('change', function(){
            calculateLeave();
        })
    });

    function calculateLeave()
    {
        let start = $('#tanggalMulaiCuti').val();
        let end = $('#tanggalSelesaiCuti').val();
        if(start && end) {
            start = new Date(start);
            end = new Date(end);
            let diff_in_time = end.getTime() - start.getTime();

            let diff_in_day = diff_in_time / (1000 * 3600 * 24);
            $('#durasi').val(diff_in_day + 1);
        }
        
    }

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