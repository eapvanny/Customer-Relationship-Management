<x-backend.dashboard.count-box :total="$total_parents" title="Total Parents">
    <x-slot:detail>
        <b class="text-success me-1">{{ $total_fathers }}</b>{{__('Father')}} |
        <b class="text-success me-1">{{ $total_mothers }}</b>{{__('Mother')}} |
        <b class="text-success me-1">{{ $total_guardians }}</b>{{__('Guardians')}}
    </x-slot:detail>
    <x-slot:icon>
        <span class="icon" style="background-color: #2aaa91;">
            <i class="fa fa-solid fa-user-group" aria-hidden="true"></i>
        </span>
    </x-slot:icon>
</x-backend.dashboard.count-box>
