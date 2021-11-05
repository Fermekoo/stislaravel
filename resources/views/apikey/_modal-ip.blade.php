<div class="modal fade" tabindex="-1" role="dialog" id="modal_ip">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modal_ip_title"></h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<form id="form_ip_action">
					@csrf
					<input type="hidden" id="id" name="id">
					<div class="form-group">
						<label>API KEY</label>
						<input type="text" class="form-control" id="apikey" disabled>
					</div>
					<div class="form-group">
						<div class="form-check">
							<input class="form-check-input" type="checkbox" value="1" id="whitelistIPx" name="whitelistIP">
							<label class="form-check-label" for="whitelistIP">
								Whitelist IP
							</label>
						</div>
						<div id="customFieldsIP">
							<div class="section-title">IP</div>
							<div class="form-group baru-data">
								<div class="input-group mb-3">
									<input type="text" id="IPwhitelistx" class="form-control ipv4" name="IPwhitelist[]">
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
				@canany(['api-key-update'])
				<button type="button" class="btn btn-primary" id="btn-submit-ip">Simpan</button>
				@endcanany
			</div>
		</div>
	</div>
</div>
@push('js')
<script>
	$(document).ready(function() {
		$("#customFieldsIP").on('click','.addCF',function() {
			$("#customFieldsIP").append(`<div class="form-group baru-data">
								<div class="input-group mb-3">
									<input type="text" class="form-control ipv4" name="IPwhitelist[]">
									<div class="input-group-append">
										<button type="button" class="btn btn-danger remCF"><i class="fa fa-trash"></i></button>
									</div>
								</div>
							</div>`);
		});
		$("#customFieldsIP").on('click', '.remCF', function() {
			$(this).parent().parent().remove();
		});

		$('#whitelistIPx').change(function(){
			if(this.checked){
				$('#customFieldsIP').show()
			} else {
				$('#customFieldsIP').hide()
				// $('#customFieldsIP .ipv4').val('')
			}
		})
	});
</script>
@endpush