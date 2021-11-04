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
					<input type="hidden" id="id" name="id">
					@if(auth()->user()->user_type == 'admin')
					<div class="form-group">
						<label>NAMA PERUSAHAAN*</label>
						<select class="form-control select2" id="perusahaan" name="perusahaan">
							<option value="">-PILIH-</option>
							@foreach($companies as $company)
							<option value="{{ $company->id }}"> {{ $company->name }} ( {{ $company->company_code }} )</option>
							@endforeach
						</select>
					</div>
					@endif
					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" id="whitelistIP" name="whitelistIP">
							<label class="form-check-label" for="whitelistIP">
								Whitelist IP
							</label>
						</div>
						<div id="customFields">
							<div class="section-title">IP</div>
							<div class="form-group baru-data">
								<div class="input-group mb-3">
									<input type="text" id="IPwhitelist" class="form-control ipv4" name="IPwhitelist[]">
									<div class="input-group-append">
										<button type="button" class="btn btn-success addCF"><i class="fa fa-plus"></i></button>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="modal-footer bg-whitesmoke br">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
				@canany(['api-key-create','api-key-update'])
				<button type="button" class="btn btn-primary" id="btn-submit"></button>
				@endcanany
			</div>
		</div>
	</div>
</div>
@push('js')
<script>
	$(document).ready(function() {
		$(".addCF").click(function() {
			$("#customFields").append(`<div class="form-group baru-data">
								<div class="input-group mb-3">
									<input type="text" class="form-control ipv4" name="IPwhitelist[]">
									<div class="input-group-append">
										<button type="button" class="btn btn-danger remCF"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>`);
		});
		$("#customFields").on('click', '.remCF', function() {
			$(this).parent().parent().remove();
		});

		$('#whitelistIP').change(function(){
			if(this.checked){
				$('#customFields').show()
			} else {
				$('#customFields').hide()
				$('#customFields .ipv4').val('')
			}
		})
	});
</script>
@endpush