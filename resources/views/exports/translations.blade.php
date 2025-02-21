<table>
    <tbody>
        <tr>
            <td colspan="4">
                Note: <br/>
                Locale: kh, en
            </td>
        </tr>
        <tr>
            <th style="background: yellow">Locale</th>
            <th style="background: #b4c7dc">Item</th>
            <th style="background: #b4c7dc">Text</th>
        </tr>

    @foreach($rows as $row)
        <tr>
            <td>{{ $row->locale }}</td>
            <td>{{ $row->item }}</td>
            <td>{{ $row->text }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
