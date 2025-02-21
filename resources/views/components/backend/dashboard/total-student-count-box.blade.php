<x-backend.dashboard.count-box :total="$total_students" title="Total Students">
    <x-slot:detail>
        <b class="text-success me-1">{{ $total_male_students }}</b>{{__('Male')}} |
        <b class="text-success me-1">{{ $total_female_students }}</b>{{__('Female')}}
    </x-slot:detail>
    <x-slot:icon>
        <span class="icon" style="background-color: #ff7556;">
            <i class="fa icon-student" aria-hidden="true"></i>
        </span>
    </x-slot:icon>
</x-backend.dashboard.count-box>

