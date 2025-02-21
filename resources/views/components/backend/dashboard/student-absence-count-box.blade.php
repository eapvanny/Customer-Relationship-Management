<x-backend.dashboard.count-box :total="$total_absences" title="Student Absences">
    <x-slot:detail>
        <b class="text-success me-1">{{ $total_absence_with_leaves }}</b>{{__('Leaves')}} | <b class="text-success me-1">{{ $total_absence_without_leaves }}</b>{{__('Without leaves')}}
    </x-slot:detail>
    <x-slot:icon>
        <span class="icon" style="background: #feb559">
            <i class="fa-solid fa-user-xmark"></i>
        </span>
    </x-slot:icon>
</x-backend.dashboard.count-box>
