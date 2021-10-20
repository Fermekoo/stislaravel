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
					<input type="hidden" id="id" name="employeeTypeId">

					@if(auth()->user()->user_type == 'admin')
					<div class="form-group">
						<label>NAMA PERUSAHAAN</label>
						<select class="form-control select2" id="companyId" name="companyId" required>
							<option value="">-PILIH-</option>
							@foreach($companies as $company)
							<option value="{{ $company->id }}"> {{ $company->name }} ( {{ $company->company_code }} )</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="form-group">
						<label>STATUS KARYAWAN</label>
						<input type="text" class="form-control" id="tipeKaryawan" name="tipeKaryawan">
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				@canany(['mst-status-karyawan-create','mst-status-karyawan-update'])
				<button type="button" class="btn btn-primary" id="btn-submit"></button>
				@endcanany
			</div>
		</div>
	</div>
</div>