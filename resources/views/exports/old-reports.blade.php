@php
    use App\Http\Helpers\AppHelper;
    use Illuminate\Support\Facades\URL;
    
    $fullDomain = url('/');

    // Use pre-calculated totals
    $total250ml = $totals['total_250ml'] ?? 0;
    $total350ml = $totals['total_350ml'] ?? 0;
    $total600ml = $totals['total_600ml'] ?? 0;
    $total1500ml = $totals['total_1500ml'] ?? 0;
@endphp

<table border="1">
    <thead>
        <tr>
            <th>{{ __('Area') }}</th>
            <th>{{ __('SSP_NAME') }}</th>
            <th>{{ __('SSP_ID') }}</th>
            <th>{{ __('SUP_NAME') }}</th>
            <th>{{ __('SUP_ID') }}</th>
            <th>{{ __('ASM_NAME') }}</th>
            <th>{{ __('RSM_NAME') }}</th>
            <th>{{ __('Depo Name') }}</th>
            <th>{{ __('Customer Name') }}</th>
            <th>{{ __('Customer Code') }}</th>
            <th>{{ __('SO Number') }}</th>
            <th>{{ __('SO Date') }}</th>
            <th>{{ __('250ml') }}<span>{{__('(Case)')}}</span></th>
            <th>{{ __('350ml') }}<span>{{__('(Case)')}}</span></th>
            <th>{{ __('600ml') }}<span>{{__('(Case)')}}</span></th>
            <th>{{ __('1500ml') }}<span>{{__('(Case)')}}</span></th>
            <th>{{ __('Default') }}</th>
            <th>{{ __('Latitude') }}</th>
            <th>{{ __('Longitude') }}</th>
            <th>{{ __('Address') }}</th>
            <th>{{ __('Photo Outlet') }}</th>
            <th>{{ __('POSM PHOTO') }}</th>
            <th>{{ __('POSM1') }}</th>
            <th>{{ __('Quantity1') }}</th>
            <th>{{ __('POSM2')}}</th>
            <th>{{ __('Quantity2')}}</th>
            <th>{{ __('POSM3')}}</th>
            <th>{{ __('Quantity3')}}</th>
            <th>{{ __('Status')}}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $data = $row->processed_data;
                $val250ml = $data['val_250ml'];
                $val350ml = $data['val_350ml'];
                $val600ml = $data['val_600ml'];
                $val1500ml = $data['val_1500ml'];
                $default = $data['default'];
                
                $reportUser = $data['user'];
                $sup = $data['sup'];
                $rsm = $data['rsm'];
                $asm = $data['asm'];
                
                $posm = isset(AppHelper::MATERIAL[$row->posm]) ? __(AppHelper::MATERIAL[$row->posm]) : ($row->posm_name1 ?? 'N/A');
                $posm2 = isset(AppHelper::MATERIAL[$row->posm2]) ? __(AppHelper::MATERIAL[$row->posm2]) : ($row->posm_name2 ?? 'N/A');
                $posm3 = isset(AppHelper::MATERIAL[$row->posm3]) ? __(AppHelper::MATERIAL[$row->posm3]) : ($row->posm_name3 ?? 'N/A');
                
                $OutletUrl = $row->outlet_photo
                    ? $fullDomain . '/photo/' . AppHelper::shortEncrypt($row->outlet_photo)
                    : 'No_Photo';

                $PosmUrl = $row->photo
                    ? $fullDomain . '/photo/' . AppHelper::shortEncrypt($row->photo)
                    : 'No_Photo';
                
                $userLang = session('user_lang', 'kh');
            @endphp
            <tr>
                <td>
                    {{
                        !empty($row->area_id)
                            ? AppHelper::getAreaNameById($row->area_id)
                            : ($reportUser?->area ?? 'N/A')
                    }}
                </td>
                <td>
                    {{ $reportUser
                        ? ($userLang === 'kh'
                            ? ($reportUser->full_name ?? $reportUser->full_name_latin ?? $row->ssp_name ?? 'N/A')
                            : ($reportUser->full_name_latin ?? $reportUser->full_name ?? $row->ssp_name ?? 'N/A'))
                        : ($row->ssp_name ?? 'N/A')
                    }}
                </td>
                <td>{{ $reportUser?->staff_id_card ?? $row->ssp_id ?? 'N/A' }}</td>
                <td>
                    {{ $sup
                        ? ($userLang === 'en'
                            ? ($sup->full_name_latin ?? $row->sup_name ?? 'N/A')
                            : ($sup->full_name ?? $row->sup_name ?? 'N/A'))
                        : ($row->sup_name ?? 'N/A')
                    }}
                </td>
                <td>{{ $sup?->staff_id_card ?? $row->sup_id ?? 'N/A' }}</td>
                <td>
                    {{ $asm
                        ? ($userLang === 'en'
                            ? ($asm->full_name_latin ?? $row->asm_name ?? 'N/A')
                            : ($asm->full_name ?? $row->asm_name ?? 'N/A'))
                        : ($row->asm_name ?? 'N/A')
                    }}
                </td>
                <td>
                    {{ $rsm
                        ? ($userLang === 'en'
                            ? ($rsm->full_name_latin ?? $row->rsm_name ?? 'N/A')
                            : ($rsm->full_name ?? $row->rsm_name ?? 'N/A'))
                        : ($row->rsm_name ?? 'N/A')
                    }}
                </td>
                <td>{{ $row->depo_name ?? $row->outlet_name ?? 'N/A' }}</td>
                <td>{{ $row->customer_name ?? $row->customer_name ?? 'N/A' }}</td>
                <td>{{ $row->customer_code ?? 'N/A' }}</td>
                <td>{{ $row->so_number ?? 'N/A' }}</td>
                <td>{{ $row->date ? \Carbon\Carbon::parse($row->date)->format('d-M-Y') : 'N/A' }}</td>
                <td>{{ $val250ml }}</td>
                <td>{{ $val350ml }}</td>
                <td>{{ $val600ml }}</td>
                <td>{{ $val1500ml }}</td>
                <td>{{ $default }}</td>
                <td>{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>
                    {{ ($row->city && $row->country)
                        ? $row->city . ', ' . $row->country
                        : ($row->address ?? 'N/A') }}
                </td>
                <td>
                    @if ($OutletUrl === 'No_Photo')
                        {{ __('No_Photo') }}
                    @else
                        <a href="{{ $OutletUrl }}" target="_blank">{{ __('OUTLET_URL') }}</a>
                    @endif
                </td>
                <td>
                    @if ($PosmUrl === 'No_Photo')
                        {{ __('No_Photo') }}
                    @else
                        <a href="{{ $PosmUrl }}" target="_blank">{{ __('POSM_URL') }}</a>
                    @endif
                </td>
                <td>{{ $posm ?? 'N/A' }}</td>
                <td>{{ $row->qty ?? 'N/A' }}</td>
                <td>{{ $posm2 ?? 'N/A' }}</td>
                <td>{{ $row->qty2 ?? 'N/A' }}</td>
                <td>{{ $posm3 ?? 'N/A' }}</td>
                <td>{{ $row->qty3 ?? 'N/A' }}</td>
                <td>{{ $row->status ?? '' }}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="12">{{ __('Total') }}</td>
            <td>{{ $total250ml }}</td>
            <td>{{ $total350ml }}</td>
            <td>{{ $total600ml }}</td>
            <td>{{ $total1500ml }}</td>
            <td>{{ $total250ml + $total350ml + $total600ml + $total1500ml }}</td>
            <td colspan="5"></td>
        </tr>
    </tbody>
</table>