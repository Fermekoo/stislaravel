<table>
    <thead>
    <tr>
        <th>KODE KARYAWAN</th>
        <th>NAMA</th>
        <th>PERUSAHAAN</th>
        <th>TANGGAL</th>
        <th>JAM MASUK</th>
        <th>STATUS KEHADIRAN</th>
        <th>JAM PULANG</th>
        <th>STATUS PULANG</th>
    </tr>
    </thead>
    <tbody>
        @foreach($attendances as $att)
        <tr>
            <td>{{ $att->employee_code }}</td>
            <td>{{ $att->name }}</td>
            <td>{{ $att->company }}</td>
            <td>{{ $att->date }}</td>
            <td>{{ $att->check_in }}</td>
            <td>{{ $att->checkin_status }}</td>
            <td>{{ $att->check_out }}</td>
            <td>{{ $att->checkout_status }}</td>
        </tr>
        @endforeach
    </tbody>
</table>