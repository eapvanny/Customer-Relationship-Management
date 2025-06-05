@php

    use Illuminate\Support\Facades\Request;
    // $hostname = Request::getHost();
    $fullDomain = url('/');


@endphp
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

            <th>{{__('FOC qty')}}</th>
            <th>{{__('POSM')}}</th>
            <th>{{__('POSM qty')}}</th>

            <th>{{__('FOC Img (link)')}}</th>
            <th>{{__('POSM Img (link)')}}</th>

            <th>{{__('Other')}}</th>
            <th>{{__('Latitude')}}</th>
            <th>{{__('Longitude')}}</th>
            <th>{{__('Address')}}</th>
            <th>{{__('Date')}}</th>
            {{-- <th>{{__('Material Type')}}</th>
            <th>{{__('Quantity')}}</th> --}}
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ $row->user->staff_id_card ?? 'N/A' }}</td>
                <td>{{ (optional($row->user)->family_name ? optional($row->user)->family_name : 'N/A') . ' ' . (optional($row->user)->name ? optional($row->user)->name : 'N/A') }}
                </td>
                <td>{{ __(\App\Http\Helpers\AppHelper::getAreaName($row->area_id)) ?? 'N/A' }}</td>
                <td>{{ $row->customer->outlet ?? 'N/A' }}</td>
                <td>{{ $row->customer->name ?? 'N/A' }}</td>
                <td>{{ isset(\App\Http\Helpers\AppHelper::CUSTOMER_TYPE[$row->customer_type]) ? __(\App\Http\Helpers\AppHelper::CUSTOMER_TYPE[$row->customer_type]) : __('N/A') }}</td>
                <td>{{ $row->{'250_ml'} ?? 0 }}</td>
                <td>{{ $row->{'350_ml'} ?? 0 }}</td>
                <td>{{ $row->{'600_ml'} ?? 0 }}</td>
                <td>{{ $row->{'1500_ml'} ?? 0 }}</td>
                <td>{{ $row->customer->phone ?? 0 }}</td>

                <td>{{ $row->foc_qty ?? 'N/A' }}</td>
                {{-- <td>{{ $row->posm ? __(\App\Http\Helpers\AppHelper::getMaterialName($row->posm)) : 'N/A' }}</td> --}}
                <td>{{ isset(\App\Http\Helpers\AppHelper::MATERIAL[$row->posm]) ? __(\App\Http\Helpers\AppHelper::MATERIAL[$row->posm]) : __('N/A') }}</td>
                <td>{{ $row->qty ?? 'N/A' }}</td>

                <td>
                    {{-- @if ($row->photo_foc)
                        <img src="{{ asset('storage/' . $row->photo_foc) }}" alt="FOC Image" style="width: 100px; height: auto;">
                    @else
                        N/A
                    @endif --}}
                    {{ $fullDomain . '/storage/' . $row->photo_foc ?? 'N/A' }}
                </td>

                <td>
                    {{-- @if ($row->photo)
                        <img src="{{ asset('storage/' . $row->photo) }}" alt="POSM Image" style="width: 100px; height: auto;">
                    @else
                        N/A
                    @endif --}}
                    {{ $fullDomain . '/storage/' . $row->photo ?? 'N/A' }}
                </td>

                <td>{{ $row->other ?? 'N/A' }}</td>
                <td>{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>{{ ($row->city ?? '') . ', ' . ($row->country ?? '') ?: 'N/A' }}</td>
                <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-M-Y h:i A') : 'N/A' }}</td>
                {{-- <td>{{ isset(App\Http\Helpers\AppHelper::MATERIAL[$row->posm])
                    ? __(App\Http\Helpers\AppHelper::MATERIAL[$row->posm])
                    : __('N/A') }}
                </td>
                <td>{{ $row->qty ?? 'N/A' }}</td> --}}
            </tr>
        @endforeach
    </tbody>
</table>
