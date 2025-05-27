@php
    use App\Http\Helpers\AppHelper;
    use App\Models\Customer;
@endphp
<h5>ទាញយករបាយការណ៍របស់ផ្នែក SE (Retail)</h5>
<table border="1">
    <thead>
        <tr>
            <th>{{__('Staff ID')}}</th>
            <th>{{__('Name')}}</th>
            <th>{{__('Area')}}</th>
            <th>{{__('Outlet')}}</th>
            <th>{{__('Customer')}}</th>
            <th>{{__('Customer Type')}}</th>
            <th>{{__('250ml')}}</th>
            <th>{{__('350ml')}}</th>
            <th>{{__('600ml')}}</th>
            <th>{{__('1500ml')}}</th>
            <th>{{__('Phone number')}}</th>
            <th>{{__('Other')}}</th>
            <th>{{__('Latitude')}}</th>
            <th>{{__('Longitude')}}</th>
            <th>{{__('Address')}}</th>
            <th>{{__('Date')}}</th>
            <th>{{__('Material Type')}}</th>
            <th>{{__('Quantity')}}</th>
            <th>{{__('FOC Quantity')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row->user->staff_id_card ?? 'N/A' }}</td>
                <td>{{ (optional($row->user)->family_name ? optional($row->user)->family_name : 'N/A') . ' ' . (optional($row->user)->name ? optional($row->user)->name : 'N/A') }}
                </td>
                <td>{{ __($row->area_id) ?? 'N/A' }}</td>
                <td>{{ $row->outlet_id ? $row->outlet_id : 'N/A'; }}</td>
                <td>{{ $row->customer_id ? $row->customer_id : 'N/A' }}</td>
                {{-- <td>{{ isset(AppHelper::CUSTOMER_TYPE[$row->customer_type]) ? __(AppHelper::CUSTOMER_TYPE[$row->customer_type]) : __('N/A') }}</td>
                 --}}
                <td>
                    {{ __($row->customer_type ?? 'N/A') }}
                </td>
                <td>{{ $row->{'250_ml'} ?? 0 }}</td>
                <td>{{ $row->{'350_ml'} ?? 0 }}</td>
                <td>{{ $row->{'600_ml'} ?? 0 }}</td>
                <td>{{ $row->{'1500_ml'} ?? 0 }}</td>
                <td>{{ $row->phone ?? 'N/A' }}</td>
                <td>{{ $row->other ?? 'N/A' }}</td>
                <td>{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>{{ ($row->city ?? '') . ', ' . ($row->country ?? '') ?: 'N/A' }}</td>
                <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-M-Y') : 'N/A' }}</td>
                <td>{{ isset(App\Http\Helpers\AppHelper::MATERIAL[$row->posm])
                    ? __(App\Http\Helpers\AppHelper::MATERIAL[$row->posm])
                    : __('N/A') }}
                </td>
                <td>{{ $row->qty ?? 'N/A' }}</td>
                <td>{{$row->foc_qty ?? 'N/A'}}</td>
            </tr>
        @endforeach
    </tbody>
</table>
