@php
    use App\Http\Helpers\AppHelper;
    use Illuminate\Support\Facades\URL;

    $fullDomain = url('/');

    // Custom short encryption for file path
    function shortEncrypt($string)
    {
        $key = substr(hash('sha256', config('app.key')), 0, 32); // 256-bit key
        $iv = random_bytes(16); // 128-bit IV
        $encrypted = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $result = base64_encode($iv . $encrypted);
        return rtrim(strtr($result, '+/', '-_'), '='); // URL safe
    }

    // Initialize totals
    $total_250ml = 0;
    $total_350ml = 0;
    $total_600ml = 0;
    $total_1500ml = 0;
@endphp
<h4>{{ $title }}</h4>
<p>{{ __('Export date:') }} {{ Carbon\Carbon::parse(now())->format('d-m-Y h:i:s A') }}</p>
<table border="1">
    <thead>
        <tr>
            <th>{{ __('Area') }}</th>
            <th>{{ __('SPP (SE)') }}</th>
            <th>{{ __('SUP') }}</th>
            <th>{{ __('RSM') }}</th>
            <th>{{ __('Depo Name') }}</th>
            <th>{{ __('Customer Name') }}</th>
            <th>{{ __('SO Date') }}</th>
            <th>{{ __('250ml') }}</th>
            <th>{{ __('350ml') }}</th>
            <th>{{ __('600ml') }}</th>
            <th>{{ __('1500ml') }}</th>
            <th>{{ __('Default') }}</th>
            <th>{{ __('Other') }}</th>

            <th>{{ __('Latitude') }}</th>
            <th>{{ __('Longitude') }}</th>
            <th>{{ __('Address') }}</th>

            <th>{{ __('FOC 250ml') }}</th>
            <th>{{ __('FOC 350ml') }}</th>
            <th>{{ __('FOC 600ml') }}</th>
            <th>{{ __('FOC 1500ml') }}</th>
            @if ($asm)
                <th>{{ __('FOC Special') }}</th>
                <th>{{ __('FOC Special Qty') }}</th>
            @endif

            <th>{{ __('POSM 1') }}</th>
            <th>{{ __('POSM 1 Qty') }}</th>

            <th>{{ __('POSM 2') }}</th>
            <th>{{ __('POSM 2 Qty') }}</th>

            <th>{{ __('POSM 3') }}</th>
            <th>{{ __('POSM 3 Qty') }}</th>

            <th>{{ __('FOC Img (link)') }}</th>
            <th>{{ __('POSM Img (link)') }}</th>
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

                $reportUser = $row->user;
                $sup = $reportUser ? \App\Models\User::find($reportUser->sup_id) : null;
                $rsm = $reportUser ? \App\Models\User::find($reportUser->rsm_id) : null;

                // $photoUrl = $row->outlet_photo ? $fullDomain . '/photo/' . shortEncrypt($row->outlet_photo) : 'N/A';
                $focPhotoUrl = $row->photo_foc ? $fullDomain . '/photo/' . shortEncrypt($row->photo_foc) : 'N/A';
                $posmPhotoUrl = $row->photo ? $fullDomain . '/photo/' . shortEncrypt($row->photo) : 'N/A';
            @endphp
            <tr>
                <td>{{ $row->region->region_name . ' - ' . $row->region->se_code ?? 'N/A' }}</td>
                <td>{{ $reportUser ? ($reportUser->user_lang === 'en' ? $reportUser->full_name_latin ?? 'N/A' : $reportUser->full_name ?? 'N/A') : 'N/A' }}
                </td>
                <td>{{ $sup ? ($sup->user_lang === 'en' ? $sup->full_name_latin ?? 'N/A' : $sup->full_name ?? 'N/A') : 'N/A' }}
                </td>
                <td>{{ $rsm ? ($rsm->user_lang === 'en' ? $rsm->full_name_latin ?? 'N/A' : $rsm->full_name ?? 'N/A') : 'N/A' }}
                </td>
                <td>{{ $row->outlet->name ?? 'N/A' }}</td>
                <td>{{ $row->CustomerProvince->name ?? 'N/A' }}</td>
                {{-- <td>{{ $row->mcustomer->code ?? 'N/A' }}</td> --}}
                {{-- <td>{{ $row->so_number ?? 'N/A' }}</td> --}}
                <td>{{ $row->created_at ? \Carbon\Carbon::parse($row->created_at)->format('d-M-Y') : 'N/A' }}</td>
                <td>{{ $val_250ml }}</td>
                <td>{{ $val_350ml }}</td>
                <td>{{ $val_600ml }}</td>
                <td>{{ $val_1500ml }}</td>
                <td>{{ $default }}</td>
                <td>{{ $row->other ?? 'N/A' }}</td>
                <td>{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td>{{ ($row->city ?? '') . ', ' . ($row->country ?? '') ?: 'N/A' }}</td>

                <td>{{ $row->foc_250_qty ?? 'N/A' }}</td>
                <td>{{ $row->foc_350_qty ?? 'N/A' }}</td>
                <td>{{ $row->foc_600_qty ?? 'N/A' }}</td>
                <td>{{ $row->foc_1500_qty ?? 'N/A' }}</td>
                @if ($asm)
                    <td>{{ $row->foc_other ? $row->foc_other . 'ml' : 'N/A' }}</td>
                    <td>{{ $row->foc_other_qty ?? 'N/A' }}</td>
                @endif

                {{-- <td>{{ $row->posm ?? 'N/A' }}</td> --}}
                {{-- <td>{{ isset(AppHelper::MATERIAL[$row->posm]) ? __(AppHelper::MATERIAL[$row->posm]) : __('N/A') }}</td> --}}

                <td>{{ session('user_lang') == 'en' ? $row->posm1->name_en ?? 'N/A' : $row->posm1->name_kh ?? 'N/A' }}
                </td>
                <td>{{ $row->posm_1_qty ?? 'N/A' }}</td>

                <td>{{ session('user_lang') == 'en' ? $row->posm2->name_en ?? 'N/A' : $row->posm2->name_kh ?? 'N/A' }}
                </td>
                <td>{{ $row->posm_2_qty ?? 'N/A' }}</td>

                <td>{{ session('user_lang') == 'en' ? $row->posm3->name_en ?? 'N/A' : $row->posm3->name_kh ?? 'N/A' }}
                </td>
                <td>{{ $row->posm_3_qty ?? 'N/A' }}</td>




                <td>
                    @if ($focPhotoUrl === 'N/A')
                        {{ __('No_Photo') }}
                    @else
                        <a href="{{ $focPhotoUrl }}" target="_blank">{{ __('FOC PHOTO') }}</a>
                    @endif
                </td>
                <td>
                    @if ($posmPhotoUrl === 'N/A')
                        {{ __('No_Photo') }}
                    @else
                        <a href="{{ $posmPhotoUrl }}" target="_blank">{{ __('POSM PHOTO') }}</a>
                    @endif
                </td>

            </tr>
        @endforeach
        <tr>
            <td colspan="7">{{ __('Total') }}</td>
            <td>{{ $total_250ml }}</td>
            <td>{{ $total_350ml }}</td>
            <td>{{ $total_600ml }}</td>
            <td>{{ $total_1500ml }}</td>
            <td>{{ $total_250ml + $total_350ml + $total_600ml + $total_1500ml }}</td>
            <td colspan="5"></td>
        </tr>
    </tbody>
</table>
