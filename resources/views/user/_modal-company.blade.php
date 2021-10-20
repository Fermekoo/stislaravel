<div class="modal fade" tabindex="-1" role="dialog" id="form_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_action">
					@csrf
					<input type="hidden" id="modelId" name="modelId">
					<input type="hidden" id="roleId" name="roleId">
					<div class="form-group">
						<label>KARYAWAN*</label>
						<select class="form-control select2" id="userId" name="userId" required>
							<option value="">-PILIH-</option>
						</select>
					</div>
					<div class="form-group">
						<label>ROLE*</label>
						<select class="form-control select2" id="role" name="role" required>
							<option value="">-PILIH-</option>
							@foreach($roles as $role)
							<option value="{{ $role->name }}"> {{ $role->display }}</option>
							@endforeach
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				@canany(['user-create','user-update'])
				<button type="button" class="btn btn-primary" id="btn-submit"></button>
				@endcan
			</div>
		</div>
	</div>
</div>
@push('js')
<script>
	$(document).ready(function(){

		$('#add-data').click(function(){
            titleCaption('Tambah Data', 'Simpan');
			$('#pw_caption').hide();

			getEmployee()

            $('#form_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#index-table').on('click', '.btndelete', function(){
            let model_id = $(this).data('id');
            let role_id = $(this).data('roleid');

            let action = `{{ route('user-employee.delete',[':model_id',':role_id']) }}`;
				action = action.replace(':model_id', model_id);
				action = action.replace(':role_id', role_id);

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
							url: action,
								success: function(res){
										swal({
											title: 'Hapus Data',
											text: 'Hapus Data Sukses',
											icon: 'success'
										});
										$('#index-table').DataTable().ajax.reload();
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
                url: `{{ route('user-employee.submit') }}`,
                data: form.serialize(),
                dataType: `json`,
                success: function(res) {
					$('#index-table').DataTable().ajax.reload();
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
                            if(msg.username) {
                                $('#username').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.username}</div>`)
                            }
                            if(msg.password) {
                                $('#password').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.password}</div>`)
                            }
                            if(msg.email) {
                                $('#email').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.email}</div>`)
                            }
                            if(msg.role) {
                                $('#role').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.role}</div>`)
                            }
                            return
                        }
                    }
                }
            })
        })

        $('#form_modal').on('hidden.bs.modal', function(){
            $('#id').val('');
            $('#modelId').val('');
            $('#roleId').val('');
            $('#userId').val('').trigger('change');
            $('#role').val('').trigger('change');
            clearError();
        });
	});

	function getEmployee()
	{
		$.ajax({
			type: `GET`,
			url: `{{ route('data.employee') }}`,
			success: function(res){
				let data = res.data;
				let html = `<option value="">-PILIH-</option>`;
				data.forEach((item) => {
					html+=`<option value="${item.user_id}">${item.name} - (${item.employee_code})</option>`
				});

				$('#userId').html(html);
			}
		});
	}
</script>
@endpush