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
					<input type="hidden" id="id" name="cutiId">

					<div class="form-group">
						<label>JENIS CUTI*</label>
						<select class="form-control select2" id="jenisCuti" name="jenisCuti" required>
							<option value="">-PILIH-</option>
							@foreach($leave_quota as $quota)
							<option value="{{ $quota['leave_type_id'] }}"> {{ $quota['leave_type'] }} ( {{ $quota['available_quota'] }} )</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>TANGGAL MULAI CUTI*</label>
						<input type="date" class="form-control" id="tanggalMulaiCuti" name="tanggalMulaiCuti">
					</div>
					<div class="form-group">
						<label>TANGGAL SELESAI CUTI*</label>
						<input type="date" class="form-control" id="tanggalSelesaiCuti" name="tanggalSelesaiCuti">
					</div>
					<div class="form-group">
						<label>DURASI CUTI </label>
						<input type="number" class="form-control" id="durasi" name="durasi" disabled>
					</div>
					<div class="form-group">
							<label>KETERANGAN</label>
							<textarea class="form-control" id="keterangan" name="keterangan"></textarea>
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