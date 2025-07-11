@php
    use App\Http\Helpers\AppHelper;
    use Illuminate\Support\Facades\URL;

    $fullDomain = url('/');

    // Custom short encryption for file path
    function shortEncrypt($string) {
        $key = substr(hash('sha256', config('app.key')), 0, 32); // 256-bit key
        $iv = random_bytes(16); // 128-bit IV
        $encrypted = openssl_encrypt($string, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        $result = base64_encode($iv . $encrypted);
        return rtrim(strtr($result, '+/', '-_'), '='); // URL safe
    }
@endphp

<table border="1">
    <thead>
        <tr>
            <th>{{ __('Area') }}</th>
            <th>{{ __('SPP') }}</th>
            {{-- <th>{{ __('ASM') }}</th> --}}
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
            <th>{{ __('Outlet Photo') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
                $user = $row->user;
                $sup = $user?->sup_id ? \App\Models\User::find($user->sup_id) : null;
                // $asm = $user?->asm_id ? \App\Models\User::find($user->asm_id) : null;
                $rsm = $user?->rsm_id ? \App\Models\User::find($user->rsm_id) : null;
                $lang = $user?->user_lang ?? 'en';
                $getFullName = fn($u) => $lang === 'en' ? ($u?->getFullNameLatinAttribute() ?? 'N/A') : ($u?->getFullNameAttribute() ?? 'N/A');
                // Generate a URL with encrypted outlet_photo path
                $photoUrl = $row->outlet_photo ? $fullDomain . '/photo/' . shortEncrypt($row->outlet_photo) : 'N/A';
            @endphp
            <tr>
                <td>{{ AppHelper::getAreaNameById($row->area_id) ?? 'N/A' }}</td>
                <td>{{ $getFullName($user) }}</td>
                {{-- <td>{{ $getFullName($asm) }}</td> --}}
                <td>{{ $getFullName($sup) }}</td>
                <td>{{ $getFullName($rsm) }}</td>
                <td>{{ $row->outlet ?? 'N/A' }}</td>
                <td>{{ $row->name ?? 'N/A' }}</td>
                <td>{{ $row->code ?? 'N/A' }}</td>
                <td>{{ AppHelper::CUSTOMER_TYPE[$row->customer_type] ?? 'N/A' }}</td>
                <td>{{ $row->phone ?? 'N/A' }}</td>
                <td>{{ $row->city && $row->country ? "{$row->city}, {$row->country}" : 'N/A' }}</td>
                <td style="text-align: start">{{ $row->latitude ?? 'N/A' }}</td>
                <td>{{ $row->longitude ?? 'N/A' }}</td>
                <td><a href="{{ $photoUrl }}" target="_blank">{{__('photoURL')}}</a></td>
            </tr>
        @endforeach
    </tbody>
</table>