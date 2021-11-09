<div class="modal fade" tabindex="-1" role="dialog" id="form_modal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_title">Konfirmasi Izin Karyawan</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_action">
					@csrf
					<input type="hidden" id="id" name="leaveId">
					<div class="form-group">
						<label>STATUS IZIN</label>
						<select class="form-control select2" id="status" name="status" required>
							<option value="">-PILIH-</option>
							<option value="Reject">Tolak</option>
							<option value="Approve">Terima</option>
						</select>
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				<button type="button" class="btn btn-primary" id="btn-submit">Simpan</button>
			</div>
		</div>
	</div>
</div>