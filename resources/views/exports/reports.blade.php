<table border="1">
    <thead>
        <tr>
            <th>Staff ID</th>
            <th>Name</th>
            <th>Area</th>
            <th>Outlet</th>
            <th>250ml</th>
            <th>350ml</th>
            <th>600ml</th>
            <th>1500ml</th>
            <th>Other</th>
            <th>Location</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row->user->staff_id_card ?? 'N/A' }}</td>
                <td>{{ optional($row->user)->family_name . ' ' . optional($row->user)->name ?? 'N/A' }}</td>
                <td>{{ $row->area ?? 'N/A' }}</td>
                <td>{{ $row->outlet ?? 'N/A' }}</td>
                <td>{{ $row->{'250_ml'} ?? 0 }}</td>
                <td>{{ $row->{'350_ml'} ?? 0 }}</td>
                <td>{{ $row->{'600_ml'} ?? 0 }}</td>
                <td>{{ $row->{'1500_ml'} ?? 0 }}</td>
                <td>{{ $row->other ?? 'N/A' }}</td>
                <td>{{ ($row->city ?? '') . ', ' . ($row->country ?? '') }}</td>
                <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-M-Y h:i A') : 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
