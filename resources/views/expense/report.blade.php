@extends('layout.master')
@section('page_title','Expense')

@section('content')
    <div class="card text-dark shadow-2 mb-3" style="max-width: 18rem;">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>
                    <h2 class="text-info"><i class="fas fa-plus-circle fa-icon"></i> Create </h2>
                </div>
                <div>
                    <a href="{{env('BASE_URL').'expenses'}}" class="btn btn-outline-success btn-sm">Expense
                        List</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form method="get" id="form-data" action="{{env('BASE_URL').'expenses/expense-report'}}" target="_blank">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label"><span class="required"></span> From Date </label>
                        <input type="text" class="form-control datepicker" id="from_date" name="from_date"
                               placeholder="dd\mm\YY" autocomplete="off">
                    </div>
                    <div class="col-md-3">
                        <label for="to_date" class="form-label"><span class="required"></span> To Date </label>
                        <input type="text" class="form-control datepicker" id="to_date" name="to_date"
                               placeholder="dd\mm\YY" autocomplete="off">
                    </div>

                    <div class="col-md-3">
                        <label for="expense_head_id" class="form-label"><span class="required"></span> Expense Heads </label>
                        <select name="expense_head_id" class="form-control expense_head_id select2"></select>
                    </div>

                    <div class="col-md-3">
                        <label for="user_session_id" class="form-label"><span class="required"></span> Employee </label>
                        <select name="user_session_id" class="form-control user_session_id select2"></select>
                    </div>
                </div><!--End of row-->


                <div class="row form-row mb-3">
                    <label class="col-md-3 control-label"><b></b>SELECT THE REPORT</label>
                    <div class="col-md-4 form-group">
                        <input type="radio" id="datewise" name="report_type" checked="checked" value="datewise">
                        <label for="datewise">Expense Report Datewise</label><br>
                        <input type="radio" id="expenseheadwise" name="report_type" value="expenseheadwise">
                        <label for="headwise">Expense Report Headwise</label><br>
                        <input type="radio" id="summary" name="report_type" value="employeewise">
                        <label for="summary">Expense Report Employee wise</label><br>
                    </div>
                    <div class="col-md-5">
                        <label for="notes" class="form-label"><span class="required"></span> Report Description </label>
                        <textarea name="notes" id="notes" class="form-control tinemcy"></textarea>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-success mb-4 submit-btn">Save <i
                            class="fas fa-chevron-right ms-3 go-icon"></i></button>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('page_level_scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            expense_head_optional_load();
            user_optional_load();

            // $("#form-data").on('submit', function (e) {
            //     alert('jello')
            //     e.preventDefault();
            //     $.ajax({
            //         url: api_url + "expenses/store",
            //         type: "POST",
            //         data: JSON.stringify(getFormData()),
            //         contentType: "application/json",
            //         dataType: "JSON",
            //         success: function (data) {
            //             if (data.status) {
            //                 success_notify(data.message);
            //
            //             } else {
            //                 error_notify(data.message);
            //             }
            //         }
            //     });
            // });

        });


    </script>
@endsection

