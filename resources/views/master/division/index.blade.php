@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Master Divisi</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Divisi</h4>
                <div class="d-flex flex-row">
                @can('mst-divisi-create')
                    <button class="btn btn-primary add-satuan" id="add-data">Tambah Data</button>
                @endcan
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode Divisi</th>
                                <th>Nama Divisi</th>
                                <th>Nama Perusahaan</th>
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
@include('master.division._modal')
@endsection
@push('js')
<script>
    $(document).ready(function(){
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
                url: '{{ route("master.division.json") }}'
            },
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'division_code', name: 'division_code' },
				{ data: 'name', name: 'name' },
				{ data: 'company', name: 'company' },
				{ data: 'action', render: function(data, type, row){
                    return `<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}"  title ="Edit"><i class="fa fa-edit"></i></button> @can('mst-divisi-delete')<button data-id="${row.id}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus"  title ="Hapus" ><i class="fa fa-trash"></i></button>@endcan`
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
            let action = `{{ route('master.division.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res){
                    let data = res.data;
                    $('#id').val(data.id);
                    $('#namaDivisi').val(data.name);
                    $('#companyId').val(data.company_id).trigger('change');

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
            let action = `{{ route('master.division.delete',':id') }}`;

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
                url: `{{ route('master.division.submit') }}`,
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
                            if(msg.namaDivisi) {
                                $('#namaDivisi').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.namaDivisi}</div>`)
                            }
                            if(msg.companyId) {
                                $('#companyId').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.companyId}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#form_modal').on('hidden.bs.modal', function(){
            $('#id').val('');
            $('#namaDivisi').val('');
            $('#companyId').val('').trigger('change');
            clearError();
        });
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