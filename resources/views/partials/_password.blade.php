<div class="modal fade" tabindex="-1" role="dialog" id="password_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Ganti Password</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_password">
					@csrf
					<div class="form-group">
						<label>PASSWORD LAMA</label>
						<input type="password" class="form-control" id="passwordLama" name="passwordLama">
					</div>
					<div class="form-group">
						<label>PASSWORD BARU</label>
						<input type="password" class="form-control" id="passwordBaru" name="passwordBaru">
					</div>
					<div class="form-group">
						<label>PASSWORD LAMA</label>
						<input type="password" class="form-control" id="confirmPasswordBaru" name="confirmPasswordBaru">
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btn-password">Simpan</button>
			</div>
		</div>
	</div>
</div>
@push('js')
<script>
    $(document).ready(function(){
        $('#btn-password').on('click', function(){

            var form_pw = $('#form_password');
            $.ajax({
                type: `POST`,
                url: `{{ route('change-password') }}`,
                dataType: `JSON`,
                data: form_pw.serialize(),
                success: function(res){
                    form_pw.find('.invalid-feedback').remove();
                    form_pw.find('.form-control').removeClass('is-invalid');
                    $('#password_modal').modal('hide');
                    iziToast.success({
						title: res.message,
						position: 'topRight'
					});
                },
                error: function(xhr){
                    form_pw.find('.invalid-feedback').remove();
                    form_pw.find('.form-control').removeClass('is-invalid');
                    let res = xhr.responseJSON;
                    if($.isEmptyObject(res) == false) {
                        let msg = res.message;
                        if(msg instanceof Object) {
                            if(msg.passwordLama) {
                                $('#passwordLama').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.passwordLama}</div>`)
                            }
                            if(msg.passwordBaru) {
                                $('#passwordBaru').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.passwordBaru}</div>`)
                            }
                            if(msg.confirmPasswordBaru) {
                                $('#confirmPasswordBaru').addClass('is-invalid').after(`<div class="invalid-feedback">${msg.confirmPasswordBaru}</div>`)
                            }
                            return
                        }
                    }
                }
            });
        });
    });
</script>
@endpush