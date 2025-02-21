<x-backend.dashboard.count-box :total="$total_class" title="Total Classes">
    <x-slot:detail>
        <b class="text-success me-1">{{ $active_class }}</b>{{__('Actives')}}
    </x-slot:detail>
    <x-slot:icon>
        <span class="icon">
            <i class="fa fa-sitemap" aria-hidden="true"></i>
        </span>
    </x-slot:icon>
</x-backend.dashboard.count-box>
