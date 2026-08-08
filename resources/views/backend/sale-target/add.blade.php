@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    {{ __('Sale Target') }}
@endsection
<!-- End block -->
@section('extraStyle')
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{ URL::route('target.index') }}"> {{ __('Sale Target') }} </a></li>
            <li class="active">
                @if ($target)
                    {{ __('Update') }}
                @else
                    {{ __('Add') }}
                @endif
            </li>
        </ol>
    </section>
    <!-- ./Section header -->

    <!-- Main content -->
    <section class="content">
        <form id="entryForm"
            action="@if ($target) {{ URL::Route('target.update', $target->id) }} @else {{ URL::Route('target.store') }} @endif"
            method="post" autocomplete="off">
            <div class="row">
                <div class="col-md-12">
                    <div class="wrap-outter-header-title">
                        <h4>{{ __('Sale Target') }}</h4>
                        <div class="action-btn-top none_fly_action_btn">
                            <a href="{{ URL::route('target.index') }}" class="btn btn-default"> {{ __('Cancel') }} </a>
                            <button type="submit" class="submitClick btn btn-info pull-right text-white">
                                <i class="fa @if ($target) fa-refresh @else fa-check-circle @endif"></i>
                                @if ($target)
                                    {{ __('Update') }}
                                @else
                                    {{ __('Save') }}
                                @endif
                            </button>
                            @if (!$target)
                                <button type="submit" class="submitClick submitAndContinue btn btn-success text-white">
                                    <i class="fa fa-plus-circle"></i> {{ __('Save & Add New') }}
                                </button>
                                <div class="boxfooter"></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @csrf
            @if ($target)
                @method('PUT')
            @endif
            <div class="wrap-outter-box">
                <div class="box box-info">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>{{ __('Sales Name') }} <span class="text-danger">*</span></label>

                                    <div class="mb-2">
                                        @if(!isset($target))
                                            <label>
                                                <input type="checkbox" id="checkAll">
                                                <strong>Select All</strong>
                                            </label>
                                        @endif
                                    </div>

                                    <div class="border p-2" style="max-height:350px; overflow-y:auto;">
                                        @foreach($users as $user)
                                            <div class="user-item mb-2">

                                                <label>
                                                    <input type="checkbox"
                                                        name="user_ids[]"
                                                        value="{{ $user->id }}"
                                                        class="user-checkbox"

                                                        {{ isset($selectedUserIds) && in_array($user->id,$selectedUserIds)
                                                            ? 'checked'
                                                            : ''
                                                        }}
                                                    >

                                                    {{ auth()->user()->user_lang == 'en'
                                                        ? $user->full_name_latin
                                                        : $user->full_name }}
                                                </label>

                                                <div class="user-amount mt-2 ml-4"
                                                    style="{{ isset($userAmounts[$user->id]) ? '' : 'display:none;' }}">

                                                    <input type="number"
                                                        name="amounts[{{ $user->id }}]"
                                                        class="form-control user-amount-input"
                                                        placeholder="Enter Amount"

                                                        value="{{ $userAmounts[$user->id] ?? '' }}"

                                                        {{ isset($userAmounts[$user->id]) ? 'required' : '' }}
                                                    >

                                                </div>

                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if (!$target)
                                <div class="col-md-12" 
                                    id="globalAmount" 
                                    style="{{ isset($target) && $target->amount ? '' : 'display:none;' }}">

                                    <div class="form-group">
                                        <label>Amount <span class="text-danger">*</span></label>

                                        <input type="number"
                                            name="amount"
                                            id="amount"
                                            class="form-control"
                                            value="{{ old('amount', $target->amount ?? '') }}">
                                    </div>
                                </div>
                            @endif
                            
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $(function () {

                $('#checkAll').change(function () {

                    if ($(this).is(':checked')) {

                        $('.user-checkbox').prop('checked', true);

                        $('#globalAmount').show();
                        $('#amount').prop('required', true);

                        $('.user-amount').hide();
                        $('.user-amount-input').prop('required', false);

                    } else {

                        $('.user-checkbox').prop('checked', false);

                        $('#globalAmount').hide();
                        $('#amount').prop('required', false).val('');

                        $('.user-amount').hide();
                        $('.user-amount-input').prop('required', false).val('');
                    }

                });

                $('.user-checkbox').change(function () {

                    // Don't show individual amounts when Select All is checked
                    if ($('#checkAll').is(':checked')) {
                        return;
                    }

                    let amountDiv = $(this).closest('.user-item').find('.user-amount');
                    let amountInput = amountDiv.find('.user-amount-input');

                    if ($(this).is(':checked')) {
                        amountDiv.slideDown();
                        amountInput.prop('required', true);
                    } else {
                        amountDiv.slideUp();
                        amountInput.prop('required', false).val('');
                    }

                });

            });

        });
    </script>
@endsection
<!-- END PAGE JS-->
