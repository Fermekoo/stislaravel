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
					<input type="hidden" id="id" name="izinId">

					<div class="form-group">
						<label>JENIS IZIN*</label>
						<select class="form-control select2" id="jenisIzin" name="jenisIzin" required>
							<option value="">-PILIH-</option>
							@foreach(Constants::LEAVE_REQUEST as $lr)
							<option value="{{ $lr }}"> {{ $lr }}</option>
							@endforeach
						</select>
					</div>
					<div class="form-group">
						<label>TANGGAL MULAI IZIN*</label>
						<input type="text" class="form-control datepicker" id="tanggalMulai" name="tanggalMulai">
					</div>
					<div class="form-group">
						<label>TANGGAL SELESAI IZIN*</label>
						<input type="text" class="form-control datepicker" id="tanggalSelesai" name="tanggalSelesai">
					</div>
					<div class="form-group">
						<label>KETERANGAN</label>
						<textarea class="form-control" id="keterangan" name="keterangan"></textarea>
					</div>
					<div class="form-group">
						<label>DOKUMEN LAMPIRAN </label>
						<input type="file" class="form-control" id="document" name="document">
						<a href="" style="display:none" id="link_lampiran" download>Lihat Lampiran</a>
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