@php
    use App\Http\Helpers\AppHelper;
    use Illuminate\Support\Facades\Request;
    $asm = \App\Models\User::find(auth()->user()->asm_id);
    $rsm = \App\Models\User::find(auth()->user()->rsm_id);
    $fullDomain = url('/');
@endphp

<table border="1">
    <thead>
        <tr>
            <th>{{ __('Area') }}</th>
            <th>{{ __('SPP') }}</th>
            <th>{{ __('SUP') }}</th>
            <th>{{ __('RSM') }}</th>
            <th>{{ __('Depo Name') }}</th>
            <th>{{ __('Customer Name') }}</th>
            <th>{{ __('Customer Code') }}</th>
            <th>{{ __('Customer Type') }}</th>
            <th>{{ __('Contact') }}</th>
            <th>{{ __('Address') }}</th>
            <th>{{ __('Latitude') }}</th>
            <th>{{ __('Longitude') }}</th>
            <th>{{ __('Picture') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ AppHelper::getAreaNameById($row->area_id) ?? 'N/A' }}</td>
                <td>
                    {{ auth()->user()->user_lang === 'en' ? auth()->user()->getFullNameLatinAttribute() ?? 'N/A' : (auth()->user()->user_lang === 'kh' ? auth()->user()->getFullNameAttribute() ?? 'N/A' : 'N/A') }}
                </td>
                <td>
                    {{ $asm
                        ? (auth()->user()->user_lang === 'en'
                            ? $asm->getFullNameLatinAttribute() ?? 'N/A'
                            : $asm->getFullNameAttribute() ?? 'N/A')
                        : 'N/A' }}
                </td>
                <td>
                    {{ $rsm
                        ? (auth()->user()->user_lang === 'en'
                            ? $rsm->getFullNameLatinAttribute() ?? 'N/A'
                            : $rsm->getFullNameAttribute() ?? 'N/A')
                        : 'N/A' }}
                </td>
                <td>{{ $row->outlet ?? 'N/A' }}</td>
                <td>{{ $row->name ?? 'N/A' }}</td>
                <td>{{ $row->code ?? 'N/A' }}</td>
                <td>{{ AppHelper::CUSTOMER_TYPE[$row->customer_type] ?? 'N/A' }}</td>
                <td>{{ $row->phone ?? 'N/A' }}</td>
                <td>{{ $row->city && $row->country ? "{$row->city}, {$row->country}" : 'N/A' }}</td>
                <td style="text-align: start">{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>{{ $fullDomain . '/storage/' . $row->outlet_photo ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
