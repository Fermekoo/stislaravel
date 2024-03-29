<div class="modal fade" tabindex="-1" role="dialog" id="company_modal">
	<div class="modal-dialog modal-lg" role="document">
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
					<input type="hidden" id="id" name="companyId">
					<div class="form-group">
						<label>NAMA PERUSAHAAN</label>
						<input type="text" class="form-control" id="namaPerusahaan" name="namaPerusahaan">
					</div>
					<div class="form-group">
						<label>NOMOR TELEPON</label>
						<input type="text" class="form-control" id="nomorTelpon" name="nomorTelpon">
					</div>
					<div class="form-group">
						<label>ALAMAT</label>
						<textarea class="form-control" id="alamat" name="alamat"></textarea>
					</div>

					<div class="form-group">
						<label>USERNAME</label>
						<input type="text" class="form-control" id="username" name="username">
					</div>
					<div class="form-group">
						<label>PASSWORD</label>
						<input type="password" class="form-control" id="password" name="password">
						<code id="pw_caption">Kosongkan jika tidak ingin merubah password</code>
					</div>
					
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				@canany(['mst-perusahaan-create','mst-perusahaan-update'])
				<button type="button" class="btn btn-primary" id="btn-submit"></button>
				@endcanany
			</div>
		</div>
	</div>
</div>