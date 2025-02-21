@if($messages->isEmpty())
    <p class="text-muted text-center">No messages yet.</p>
@endif

@foreach($messages as $msg)
    <div class="d-flex {{ $msg->user_id == auth()->id() ? 'justify-content-end' : 'justify-content-start' }}">
        <div class="p-2 rounded" style="background: {{ $msg->user_id == auth()->id() ? '#d1ecf1' : '#f8f9fa' }};">
            <small><strong>{{ $msg->user->name }}</strong> ({{ $msg->created_at->format('H:i') }})</small><br>
            {{ $msg->message }}
        </div>
    </div>
@endforeach
