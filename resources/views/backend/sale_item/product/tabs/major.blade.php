<div class="box-header with-border ps-0 mb-3">
    <h3 class="box-title">{{ __('Major List') }} </h3>
    <div class="box-tools pull-right">
        <a class="btn btn-primary" id="btn-add-major" data-bs-toggle="modal" data-bs-target="#addMajorModal"><i class="fa-regular fa-plus"></i>{{__('Add')}}</a>
    </div>
</div>
@php
    $name = app()->currentLocale() == 'kh' ? 'name' : 'name_in_latin';
@endphp
<table class="table table-bordered" style="width: 100%;" id="productMJTable">
    <thead>
        <tr>

            <th>{{ __('No.') }}</th>
            <th>{{ __('Academic Degree') }}</th>
            <th>{{ __('Major') }}</th>
            <th class="d-none">{{ __('Grade Level') }}</th>
            <th>{{ __('Action') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($productGradeLevels as $i => $g)
            <tr>

                <td>{{ $i+1 }}</td>
                <td>{{ $g->academicDegree->{$name} }}</td>
                <td>{{ $g->major->{$name} }}</td>
                <td class="d-none">{{ $g->{$name} }}</td>
                <td>
                    @can('saleitem.product.edit')
                    <form action="{{ route('saleitem.product.destroy_grade_level',['gradeLevel' => $g, 'productId' => $product->id]) }}" class="form-delete-gl" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm text-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="modal modal-lg fade" id="addMajorModal" tabindex="-1" aria-labelledby="addMajorModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <form method="post" action="{{ route('saleitem.product.add_grade_level', $product) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addMajorModalLabel">{{ __('Add Major') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="addMajorTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="dont-style check_all" id="allCheckAdded"/></th>
                                    <th>{{ __('No.') }}</th>
                                    <th>{{ __('Academic Degree') }}</th>
                                    <th>{{ __('Major') }}</th>
                                    <th class="d-none">{{ __('Grade Level.') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradeLevels as $i => $g)
                                    <tr>
                                        <td><input type="checkbox" class="check-added-gl dont-style" id="allCheck" name="grade_level_ids[]" value="{{ $g->id }}"></td>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $g->academicDegree->{$name} }}</td>
                                        <td>{{ $g->major->{$name} }}</td>
                                        <td class="d-none">{{ $g->{$name} }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script type="text/javascript">
     $(document).ready(function () {
        $('input[type="checkbox"]').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue'
        });
        $('.check_all').on('ifChanged', function() {
            var isChecked = $(this).prop('checked');
            $('.check-added-gl').iCheck(isChecked ? 'check' : 'uncheck');
        });
     });
</script>    
@endpush