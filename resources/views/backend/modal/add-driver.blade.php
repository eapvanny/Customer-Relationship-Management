<div class="modal fade" id="addDriverModal" tabindex="-1" aria-hidden="true" aria-labelledby="addModalLabel"
    data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addModalLabel">
                    <i class="fas fa-user-plus"></i> <b>{{ __('Add Driver') }}</b>
                </h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                </button>
            </div>

            <div class="modal-body">

                <form id="addDriverForm">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            {{ __('Check Employee To Assign Driver') }}
                        </label>

                        <div class="border rounded">

                            @foreach ($employees as $employee)
                                <div class="employee-item border-bottom p-3">

                                    <div class="form-check">

                                        <input type="checkbox" class="form-check-input employee-checkbox"
                                            id="employee_{{ $employee['id'] }}" name="employees[]"
                                            value="{{ $employee['id'] }}" data-id="{{ $employee['id'] }}"
                                            {{ $employee['has_driver'] ? 'checked' : '' }}>

                                        <label class="form-check-label" for="employee_{{ $employee['id'] }}">
                                            {{ $employee['label'] }}
                                        </label>

                                    </div>

                                    {{-- Driver fields --}}
                                    <div class="driver-fields mt-3" id="driver_fields_{{ $employee['id'] }}"
                                        style="{{ $employee['has_driver'] ? '' : 'display: none;' }}">

                                        <div class="row">

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    {{ __('Driver ID') }}
                                                </label>

                                                <input type="number" name="driver_id[{{ $employee['id'] }}]"
                                                    class="form-control" value="{{ $employee['driver_id'] ?? '' }}"
                                                    placeholder="{{ __('Enter Driver ID') }}"
                                                    {{ $employee['has_driver'] ? 'required' : '' }}>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label">
                                                    {{ __('Driver Name') }}
                                                </label>

                                                <input type="text" name="driver_name[{{ $employee['id'] }}]"
                                                    class="form-control" value="{{ $employee['driver_name'] ?? '' }}"
                                                    placeholder="{{ __('Enter Driver Name') }}"
                                                    {{ $employee['has_driver'] ? 'required' : '' }}>
                                            </div>

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ __('Close') }}
                        </button>

                        <button type="submit" class="btn btn-primary">
                            {{ __('Save') }}
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
</div>
