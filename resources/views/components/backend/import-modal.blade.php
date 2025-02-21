<div class="modal modal-lg fade" id="{{ $modalId }}" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content rounded-3">
            <form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">{{ __('Import File') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card mb-2">
                        <div class="card-body">
                            <span class="fw-bold">{{ __('Note') }}:</span><br/>
                            - {{ __('File size must not exceed :number MB.', ['number' => 20]) }}<br/>
                            - {{ __('File extension must be excel document(.xlsx).') }}<br/>
                            {{-- - {{ __('To download sample file,') }} <a href="{{ $sampleFileUrl }}" class="btn btn-light btn-sm"><i class="fa-solid fa-download"></i> {{ __('Click here') }}</a> --}}
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">{{ $slot }}</div>
                    </div>

                    <div class="mb-3">
                        <label for="import_file" class="form-label">{{ __('Select file to import') }}</label>
                        <input class="form-control" type="file" name="import_file" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success" name="submit"><i class="fa-solid fa-file-import"></i> {{ __('Import') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
