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
					<input type="hidden" id="id" name="userId">
					<div class="form-group">
						<label>USERNAME*</label>
						<input type="text" class="form-control" id="username" name="username">
					</div>
					<div class="form-group">
						<label>EMAIL</label>
						<input type="email" class="form-control" id="email" name="email">
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
					<div class="form-group">
						<label>PASSWORD*</label>
						<input type="password" class="form-control" id="password" name="password">
						<code id="pw_caption">Kosongkan jika tidak ingin merubah password</code>
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btn-submit"></button>
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
            $('#form_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false
            })
        });

        $('#index-table').on('click','.btnedit', function(){
			$('#pw_caption').show();
            let id = $(this).data('id');
            let action = `{{ route('user.detail',':id') }}`;
            $.ajax({
                type: `GET`,
                url: action.replace(':id', id),
                success: function(res){
                    let data = res.data;
                    $('#id').val(data.id);
                    $('#username').val(data.username);
                    $('#email').val(data.email);
                    $('#role').val(data.rolename).trigger('change');

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
            let action = `{{ route('user.delete',':id') }}`;

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
                url: `{{ route('user.submit') }}`,
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
            $('#username').val('');
            $('#password').val('');
            $('#email').val('');
            $('#pw_caption').hide();
            $('#role').val('').trigger('change');
            clearError();
        });
	});
</script>
@endpush