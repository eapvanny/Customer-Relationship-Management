<div class="box-header with-border ps-0 mb-3">
    <h3 class="box-title">{{ __('Grade Level') }} </h3>
    <div class="box-tools pull-right">
        <a class="btn btn-primary" id="btn-add-grade-level" data-bs-toggle="modal" data-bs-target="#addGradeLevelModal"><i class="fa-regular fa-plus"></i> Add</a>
    </div>
</div>
<table class="table table-bordered" style="width: 100%;" id="productGLTable">
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
                <td>{{ $g->academicDegree->name }}</td>
                <td>{{ $g->major->name }}</td>
                <td class="d-none">{{ $g->name }}</td>
                <td>
                    @can('saleitem.product.edit')
                    <form action="{{ route('saleitem.product.destroy_grade_level',['gradeLevel' => $g, 'productId' => $product->id]) }}" class="form-delete-gl" method="post">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-transparent text-danger"><i class="fa-solid fa-trash"></i></button>
                    </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
<div class="modal modal-lg fade" id="addGradeLevelModal" tabindex="-1" aria-labelledby="addGradeLevelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded">
            <form method="post" action="{{ route('saleitem.product.add_grade_level', $product) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addGradeLevelModalLabel">Add Grade Level</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="addGradeLevelTable" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="dont-style" id="allCheckAdded"/></th>
                                    <th>{{ __('No.') }}</th>
                                    <th>{{ __('Academic Degree.') }}</th>
                                    <th>{{ __('Major.') }}</th>
                                    <th>{{ __('Grade Level.') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($gradeLevels as $i => $g)
                                    <tr>
                                        <td><input type="checkbox" class="check-added-gl dont-style" id="allCheck" name="grade_level_ids[]" value="{{ $g->id }}"/></td>
                                        <td>{{ $i+1 }}</td>
                                        <td>{{ $g->academicDegree->name }}</td>
                                        <td>{{ $g->major->name }}</td>
                                        <td>{{ $g->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Add Selected</button>
                </div>
            </form>
        </div>
    </div>
</div>
