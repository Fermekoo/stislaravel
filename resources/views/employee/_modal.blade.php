<div class="modal fade" tabindex="-1" role="dialog" id="company_modal">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<form id="form_action">
				<div class="modal-header">
					<h5 class="modal-title" id="modal_title"></h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					@csrf
					<input type="hidden" id="id" name="employeeId">
					<input type="hidden" id="state" name="state">
					<div class="form-group row">
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
							<label>NAMA LENGKAP*</label>
							<input type="text" class="form-control" id="namaLengkap" name="namaLengkap">
						</div>
						<div class="form-group col-md-6">
							<label>NIP*</label>
							<input type="text" class="form-control" id="nip" name="nip">
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
							<label>TANGGAL LAHIR*</label>
							<input type="text" class="form-control datepicker" id="tanggalLahir" name="tanggalLahir">
						</div>
						<div class="form-group col-md-6">
							<label>STATUS NIKAH*</label>
							<select class="form-control select2" id="statusNikah" name="statusNikah">
								<option value="">-PILIH-</option>
								<option value="Lajang">Lajang</option>
								<option value="Menikah">Menikah</option>
								<option value="Duda">Duda</option>
								<option value="Janda">Janda</option>
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
							<label>TANGGAL BERGABUNG*</label>
							<input type="text" class="form-control datepicker" id="tanggalBergabung" name="tanggalBergabung">
						</div>
						<div class="form-group col-md-6">
							<label>USERNAME*</label>
							<input type="text" class="form-control" id="username" name="username">
						</div>
						<div class="form-group col-md-6">
							<label>PASSWORD*</label>
							<input type="password" class="form-control" id="password" name="password">
							<code id="pw_caption">Kosongkan jika tidak ingin merubah password</code>
						</div>
					</div>
				</div>
				<div class="modal-header" style="margin-top:-10px">
					<h5 class="modal-title" id="modal_title">Dokumen Pendukung (Optional)</h5>
				</div>
				<div class="modal-body">
					<div class="form-group row">
						<div class="form-group col-md-6">
							<img id='img-upload' src="{{ asset('assets/img/news/img07.jpg') }}" width="100" height="100" class="img-fluid"><br>
							<label for="fotoKaryawan">FOTO KARYAWAN</label>
							<input type="file" id="fotoKaryawan" data-img="img-upload" accept="image/*" class="form-control" name="fotoKaryawan">
						</div>
						<div class="form-group col-md-6">
							<img id='img-upload-ktp' src="{{ asset('assets/img/news/img07.jpg') }}" width="100" height="100" class="img-fluid"><br>
							<label for="logo">FOTO KTP</label>
							<input type="file" id="fotoKtp" data-img="img-upload-ktp" accept="image/*" class="form-control" name="fotoKtp">
						</div>
						<div class="form-group col-md-6">
							<img id='img-upload-skck' src="{{ asset('assets/img/news/img07.jpg') }}" width="100" height="100" class="img-fluid"><br>
							<label for="logo">FOTO SKCK</label>
							<input type="file" id="fotoSkck" data-img="img-upload-skck" accept="image/*" class="form-control" name="fotoSkck">
						</div>
						<div class="form-group col-md-6">
						<img src="{{ asset('assets/img//pdf.png') }}" width="100" height="100" class="img-fluid"><br>
							<label for="logo">KONTRAK KERJA <a  id="download_contract" href="" style="display:none" download><strong>- DOWNLOAD DOKUMEN</strong></a></label>
							<input type="file" id="kontrakKerja" class="form-control" accept="application/msword, application/pdf" name="kontrakKerja">
						</div>
					</div>
				</div>
				<div class="modal-footer bg-whitesmoke br">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Tutup</button>
					@canany(['data-karyawan-create','data-karyawan-update'])
					<button type="button" class="btn btn-primary" id="btn-submit"></button>
					@endcan
				</div>
			</form>
		</div>
	</div>
</div>