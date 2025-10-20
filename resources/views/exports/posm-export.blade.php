<h1>POSM Export</h1>
<p>Export date : {{ now() }}</p>
<table border="1">
    <thead>
        <tr>
            <th>{{ __('No') }}</th>
            <th>{{ __('POSM (KH)') }}</th>
            <th>{{ __('POSM (EN)') }}</th>
            <th>{{ __('Status') }}</th>
            <th>{{ __('Create By') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($posms as $key => $item)
            <tr>
                <th>{{ $key + 1 }}</th>
                <td class="text-start">{{ $item->name_kh }}</td>
                <td class="text-start">{{ $item->name_en }}</td>
                <td>
                    <p class="badge {{ $item->status == 1 ? 'text-bg-primary' : 'text-bg-danger' }}">
                        {{ $item->status == 1 ? __('Active') : __('Inactive') }}</p>
                </td>
                <td class="text-start">
                    @if (auth()->user()->user_lang == 'en')
                        {{ $item->creator->family_name_latin . ' ' . $item->creator->name_latin }}
                    @else
                        {{ $item->creator->family_name . ' ' . $item->creator->name }}
                    @endif
                    <span class="d-block text-muted">
                        {{ Carbon\Carbon::parse($item->created_at)->format('d-M-Y h:i:s A') }}
                    </span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
