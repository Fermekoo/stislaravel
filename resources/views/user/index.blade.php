@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Akun User</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Akun User</h4>
                <div class="d-flex flex-row">
                    <button class="btn btn-primary add-satuan" id="add-data">Tambah Data</button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
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
@if(auth()->user()->user_type == 'admin')
    @include('user._modal')
@elseif(auth()->user()->user_type == 'company')
    @include('user._modal-company')
@endif
@endsection
@push('js')
<script>
    $(document).ready(function(){
        $.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

       var table = $('#index-table').DataTable({
			paging: true,
			processing: true,
			serverSide: true,
			ajax: {
                type: 'POST',
                url: '{{ route("user.json") }}'
            },
			columns: [
				{ data: 'DT_RowIndex', name: 'DT_RowIndex' },
				{ data: 'username', name: 'username' },
				{ data: 'email', name: 'email' },
				{ data: 'role', name: 'role' },
				{ data: 'action', render: function(data, type, row){
                    return `@if(auth()->user()->user_type == 'admin')<button class="btn btn-success btn-xs btnedit" data-toggle="tooltip" data-original-title="Edit" data-id="${row.id}" data-roleid="${row.roleid}"  title ="Edit"><i class="fa fa-edit"></i></button>@endif <button data-id="${row.id}" data-roleid="${row.roleid}" class="btn btn-danger btn-xs btndelete" data-toggle="tooltip" data-original-title="Hapus" title ="Hapus" ><i class="fa fa-trash"></i></button>`
                } },
			]
		})
    });

    function titleCaption(title, button){
        $('#modal_title').text(title);
        $('#btn-submit').text(button);
    }

    function clearError(){
		let form = $('#form_action');
		form.find('.invalid-feedback').remove();
		form.find('.form-control').removeClass('is-invalid');
	}
</script>
@endpush