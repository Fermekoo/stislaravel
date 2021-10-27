@extends('base')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>Absensi Karyawan</h1>
    </div>

    <div class="section-body">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h4>Data Absensi Karyawan</h4>
                <div class="d-flex flex-row">
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-md" id="index-table" width='100%'>
                        <thead>
                            <tr>
                                <th>Nama Karyawan</th>
                                <th>Perusahaan</th>
                                <th>Tanggal Absensi</th>
                                <th>Jam Masuk</th>
                                <th>Status Kehadiran</th>
                                <th>Jam Pulang</th>
                                <th>Status Pulang</th>
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
                url: '{{ route("al.history.json") }}'
            },
			columns: [
				{ data: 'employee', name: 'employee' },
				{ data: 'company', name: 'company' },
				{ data: 'date', name: 'date' },
				{ data: 'check_in', name: 'check_in' },
				{ data: 'check_in_status', name: 'check_in_status' },
				{ data: 'check_out', name: 'check_out' },
				{ data: 'check_out_status', name: 'check_out_status' }
			]
		})
    });
</script>
@endpush