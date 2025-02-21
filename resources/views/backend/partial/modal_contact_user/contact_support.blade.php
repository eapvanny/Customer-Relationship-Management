@if ($contacts->isNotEmpty())
    @foreach ($contacts as $contact)
        <div class="image-box text-center">
            <img src="{{ $contact->photo ? asset('storage/' . $contact->photo) : asset('images/avatar.png') }}" 
                alt="Image" width="100">
            
            <div class="support-name mt-2"> {{ session('user_lang', 'kh') == 'en' ? $contact->name_in_latin : $contact->name }}</div>
            
            <div class="footer-icons d-flex justify-content-center gap-2 mt-2">
                @if($contact->link_telegram)
                    <a href="{{ $contact->link_telegram }}" target="_blank" class="btn btn-outline-primary">
                        <i class="fa-brands fa-telegram"></i>
                    </a>
                @endif

                {{-- @if($contact->phone)
                    <a href="tel:{{ $contact->phone }}" class="btn btn-outline-success" 
                        data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $contact->phone }}">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                @endif --}}
            </div>
        </div>
    @endforeach
@else
    <p class="text-center">{{ __('No contacts available') }}</p>
@endif
