<x-backend.dashboard.count-box :total="$total_teachers" title="Total Teachers">
    <x-slot:detail>
        <b class="text-success me-1">{{ $total_male_teachers }}</b>{{__('Male')}} |
        <b class="text-success me-1">{{ $total_female_teachers }}</b>{{__('Female')}}
    </x-slot:detail>
    <x-slot:icon>
        <span class="icon" style="background-color: #39a1ea;">
            <i class="fa fa-solid fa-person-dots-from-line" aria-hidden="true"></i>
        </span>
    </x-slot:icon>
</x-backend.dashboard.count-box>
