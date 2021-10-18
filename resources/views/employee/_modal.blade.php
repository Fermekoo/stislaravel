<div class="modal fade" tabindex="-1" role="dialog" id="company_modal">
	<div class="modal-dialog modal-xl" role="document">
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
					<input type="hidden" id="id" name="employeeId">
					<input type="hidden" id="state" name="state">
					<div class="form-group col-md-4 mx-auto">
						<img id='img-upload' src="{{ asset('assets/img/avatar/avatar-1.png') }}" width="200" height="200" class="img-fluid">
					</div>
					<div class="form-group row">
						<div class="form-group col-md-6">
							<label for="logo">FOTO KARYAWAN*</label>
							<input type="file" id="avatar" class="form-control" name="fotoKaryawan">
						</div>
						@if(auth()->user()->user_type == 'admin')
						<div class="form-group col-md-6">
							<label>NAMA PERUSAHAAN*</label>
							<select class="form-control select2" id="perusahaan" name="perusahaan">
								<option value="">-PILIH-</option>
								@foreach($companies as $company)
								<option value="{{ $company->id }}"> {{ $company->name }} ( {{ $company->company_code }} )</option>
								@endforeach
							</select>
						</div>
						@endif
						<div class="form-group col-md-6">
							<label>DIVISI*</label>
							<select class="form-control select2" id="divisi" name="divisi">
								<option value="">-PILIH-</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>JABATAN*</label>
							<select class="form-control select2" id="jabatan" name="jabatan">
							<option value="">-PILIH-</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>GOLONGAN*</label>
							<select class="form-control select2" id="golongan" name="golongan">
								<option value="">-PILIH-</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>STATUS KARYAWAN*</label>
							<select class="form-control select2" id="status" name="status">
								<option value="">-PILIH-</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>NAMA LENGKAP</label>
							<input type="text" class="form-control" id="namaLengkap" name="namaLengkap">
						</div>
						<div class="form-group col-md-6">
							<label>JENIS KELAMIN*</label>
							<select class="form-control select2" id="jenisKelamin" name="jenisKelamin">
								<option value="">-PILIH-</option>
								<option value="Laki-Laki">Laki-Laki</option>
								<option value="Perempuan">Perempuan</option>
							</select>
						</div>
						<div class="form-group col-md-6">
							<label>NOMOR HP*</label>
							<input type="number" class="form-control" id="nomorHp" name="nomorHp">
						</div>
						<div class="form-group col-md-6">
							<label>ALAMAT*</label>
							<textarea class="form-control" id="alamat" name="alamat"></textarea>
						</div>

						<div class="form-group col-md-6">
							<label>USERNAME</label>
							<input type="text" class="form-control" id="username" name="username">
						</div>
						<div class="form-group col-md-6">
							<label>PASSWORD</label>
							<input type="password" class="form-control" id="password" name="password">
							<code id="pw_caption">Kosongkan jika tidak ingin merubah password</code>
						</div>
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