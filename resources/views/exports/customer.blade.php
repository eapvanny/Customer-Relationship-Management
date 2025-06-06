@php
    use App\Http\Helpers\AppHelper;
@endphp
<table border="1">
    <thead>
        <tr>
            <th>{{ __('Created by') }}</th>
            <th>{{ __('Creator ID') }}</th>
            <th>{{ __('Area') }}</th>
            <th>{{ __('Outlet') }}</th>
            <th>{{ __('Name') }}</th>
            <th>{{ __('Phone') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td>{{ auth()->user()->user_lang === 'en' ? (auth()->user()->getFullNameLatinAttribute()) ?? ('N/A') : (auth()->user()->user_lang === 'kh' ? (auth()->user()->getFullNameAttribute() ?? 'N/A') : 'N/A'); }}</td>
                <td>{{ (auth()->user()->staff_id_card) }}
                </td>
                <td>{{ AppHelper::getAreaName($row->area_id) ?? 'N/A' }}</td>
                <td>{{ $row->outlet ?? 'N/A' }}</td>
                <td>{{ $row->name ?? 'N/A' }}</td>
                <td>{{ $row->phone ?? 'N/A' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
