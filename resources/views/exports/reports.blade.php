@php
    use App\Http\Helpers\AppHelper;
    use Illuminate\Support\Facades\Request;
    $asm = \App\Models\User::find(auth()->user()->asm_id);
    $rsm = \App\Models\User::find(auth()->user()->rsm_id);
    $fullDomain = url('/');
    // Initialize totals
    $total_250ml = 0;
    $total_350ml = 0;
    $total_600ml = 0;
    $total_1500ml = 0;
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
            <th>{{ __('SO Number') }}</th>
            <th>{{ __('SO Date') }}</th>
            <th>{{ __('250ml') }}</th>
            <th>{{ __('350ml') }}</th>
            <th>{{ __('600ml') }}</th>
            <th>{{ __('1500ml') }}</th>
            <th>{{ __('Default') }}</th>
            <th>{{ __('Latitude') }}</th>
            <th>{{ __('Longitude') }}</th>
            <th>{{ __('Address') }}</th>
            <th>{{ __('POSM') }}</th>
            <th>{{ __('Quantity') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $val_250ml = intval($row->{'250_ml'} ?? 0);
                $val_350ml = intval($row->{'350_ml'} ?? 0);
                $val_600ml = intval($row->{'600_ml'} ?? 0);
                $val_1500ml = intval($row->{'1500_ml'} ?? 0);
                $default = $val_250ml + $val_350ml + $val_600ml + $val_1500ml;
                $total_250ml += $val_250ml;
                $total_350ml += $val_350ml;
                $total_600ml += $val_600ml;
                $total_1500ml += $val_1500ml;
            @endphp
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
                <td>{{ $row->customer->outlet ?? 'N/A' }}</td>
                <td>{{ $row->customer->name ?? 'N/A' }}</td>
                <td>{{ $row->customer->code ?? 'N/A' }}</td>
                <td>{{ $row->so_number ?? 'N/A' }}</td>
                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') : 'N/A' }}</td>
                <td>{{ $val_250ml }}</td>
                <td>{{ $val_350ml }}</td>
                <td>{{ $val_600ml }}</td>
                <td>{{ $val_1500ml }}</td>
                <td>{{ $default }}</td>
                <td>{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>{{ ($row->city ?? '') . ', ' . ($row->country ?? '') ?: 'N/A' }}</td>
                <td>{{ $fullDomain . '/storage/' . $row->photo ?? 'N/A' }}</td>
                <td>{{ $row->qty ?? 'N/A' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="9">{{ __('Total') }}</td>
            <td>{{ $total_250ml }}</td>
            <td>{{ $total_350ml }}</td>
            <td>{{ $total_600ml }}</td>
            <td>{{ $total_1500ml }}</td>
            <td>{{ $total_250ml + $total_350ml + $total_600ml + $total_1500ml }}</td>
            <td colspan="2"></td>
        </tr>
    </tbody>
</table>