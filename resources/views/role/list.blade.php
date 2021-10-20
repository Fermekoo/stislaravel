@extends('base')

@section('content')
<section class="section">
	<div class="section-header">
		<h1>Master Role</h1>
	</div>

	<div class="section-body">
		<div class="card">
			<div class="card-header d-flex justify-content-between">
				<h4>Data Role</h4>
				<div class="d-flex flex-row">
					@can('role-create')
					<a href="{{ route('roles.create') }}" class="btn btn-primary tambah-role" id="tambah-role">Tambah Role</a>
					@endcan
				</div>
			</div>
			
			<div class="card-body">
			@include('partials._alert')
				<div class="table-responsive">
					<table class="table table-striped table-md" id="table-role" width ='100%'>
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Role</th>
								<th>Jumlah Permission</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						</tbody>
					</table>
				</div>
			</div>
			<div class="card-footer">
			</div>
		</div>
	</div>
</section>
@endsection

@push('js')
<script type="text/javascript">
	$(document).ready(function() {
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});
		$('#table-role').DataTable({
			paging: true,
			processing: true,
			serverSide: true,
			ajax: '{{ route("roles.json") }}',
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'name', name: 'name' },
				{ data: 'permissions_count', name: 'permissions_count' },
				{
					data: 'action',
					render: function(data, type, row){
						let btnDelete = (row.is_deleted) ? ` @can('role-delete')<a href="#" class="delete btn btn-danger btn-sm hapus-role" data-id="${row.id}" data-role="${row.name}">Hapus</a>@endcan` : ''
						return `<a href="${row.action}" class="edit btn btn-info btn-sm edit-role">Edit</a>`+ btnDelete
						
					},
					orderable: false,
					searchable: false,
				},
			]
		})

		$('body').on('click', '.hapus-role', function (e) {
			e.preventDefault();
			var role_id = $(this).data('id');
			var role = $(this).data('role')
			swal({
				title: `Apakah anda yakin ingin menghapus role ${role}?`,
				text: 'Seluruh data yang berkaitan dengan data ini akan dihapus',
				icon: 'warning',
				buttons: true,
				dangerMode: true,
				})
				.then((willDelete) => {
					if (willDelete) {
						$.ajax({
							type: 'DELETE',
							url: `{{ url('/roles/delete/${role_id}') }}`,
								success: function(res){
										swal({
											title: 'Hapus Data',
											text: 'Hapus Data Sukses',
											icon: 'success'
										});
									$('#table-role').DataTable().ajax.reload();
								},
								error: function(xhr){
									var res = xhr.responseJSON;
									if ($.isEmptyObject(res) == false) {
										var errMsg = res.message;
										iziToast.error({
											title: errMsg,
											position: 'topRight'
										});
									}
								}
			
						}) 
					}
				});
	
		});
	});
</script>
@endpush
