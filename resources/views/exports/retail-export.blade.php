<h1>{{ $title }}</h1>
<p>Export Date : {{ now() }}</p>
<table id="datatabble"
    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server">
    <thead>
        <tr>
            <th>{{ __('No') }}</th>
            <th> {{ __('Staff Info') }} </th>
            <th>{{ __('Region') }}</th>
            <th>{{ __('SM Name') }}</th>
            <th>{{ __('RSM Name') }}</th>
            <th>{{ __('ASM Name') }}</th>
            <th>{{ __('SUP Name') }}</th>
            <th>{{ __('SE Name') }}</th>
            <th>{{ __('SE Code') }}</th>
            <th>{{ __('Customer Code') }}</th>
            <th>{{ __('Depot Contact') }}</th>
            <th>{{ __('Depot Name') }}</th>
            <th>{{ __('Retail Name') }}</th>
            <th>{{ __('Retail Contact') }}</th>
            <th>{{ __('Business Type') }}</th>
            <th>{{ __('Sale KPI') }}</th>
            <th>{{ __('Display Qty') }}</th>
            <th>{{ __('FOC Qty') }}</th>
            <th>{{ __('Location') }}</th>
            <th>{{ __('Remark') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($reports as $key => $item)
            <tr>
                <th>{{ $key + 1 }}</th>
                <td>
                    <p>ID: {{ $item->user->staff_id_card }}</p>
                    <p>{{ $item->user->family_name . ' ' . $item->user->name }}</p>
                </td>
                <td> {{ $item->region }} </td>
                <td>{{ $item->sm_name }}</td>
                <td>{{ $item->rsm_name }}</td>
                <td>{{ $item->asm_name }}</td>
                <td>{{ $item->sup_name }}</td>
                <td>{{ $item->se_name }}</td>
                <td>{{ $item->se_code }}</td>
                <td>{{ $item->customer_code }}</td>
                <td>{{ $item->depo_contact }}</td>
                <td>{{ $item->depo_name }}</td>
                <td>{{ $item->retails_name }}</td>
                <td>{{ $item->retails_contact }}</td>
                <td>{{ $item->business_type }}</td>
                <td>{{ $item->sale_kpi }}</td>
                <td>{{ $item->display_qty }}</td>
                <td>{{ $item->foc_qty }}</td>
                <td>{{ $item->location }}</td>
                <td>{{ $item->remark }}</td>
            </tr>
        @endforeach

    </tbody>
</table>
