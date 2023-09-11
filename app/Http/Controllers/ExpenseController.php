<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Attachment;
use App\Models\Expense;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ExpenseController extends Controller
{

    public function all()
    {
        $Expenses = Expense::with('expense_head')->orderByDesc('id')->get();
        return response()->json([
            'status' => true,
            'data' => $Expenses
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        foreach ($request->amount as $key => $amount) {
            if ($amount) {

                $Expense = Expense::create([
                    'date' => SiteHelper::reformatDbDate($request->date),
                    'expense_head_id' => $request->expense_head_id[$key],
                    'amount' => $request->amount[$key],
                    'employee_id' => $request->employee_id[$key],
//                    'contract_id' => $request->contract_id[$key],
                    'notes' => $request->notes[$key],
                    'created_by' => 1,
                ]);

                if (isset($request->attachment[$key])) {
                    $picture = $request->file('attachment')[$key];
                    $picture_name = '';
                    if ($picture) {
                        $type = $picture->getClientOriginalExtension();
                        $picture_name = Str::random(10) . '.' . $type;
                        $picture->move(public_path('uploads/expense-attachment'), $picture_name);
                    }

                    Attachment::create([
                        'file_name' => $picture_name,
                        'type' => $type,
                        'object' => 'Expense',
                        'object_id' => $Expense->id,
                        'context' => 'expense'
                    ]);
                }

            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Expense saved successfully'
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Expense::with(['expense_head', 'attachment'])->find($request->id)
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ]);
        }

        foreach ($request->amount as $key => $amount) {
            if ($amount) {

                $Expense = Expense::find($request->expense_id);
                $Expense->update([
                    'date' => SiteHelper::reformatDbDate($request->date),
                    'expense_head_id' => $request->expense_head_id[$key],
                    'amount' => $request->amount[$key],
                    'employee_id' => $request->employee_id[$key],
                    'notes' => $request->notes[$key],
                    'created_by' => 1,
                ]);

                if (isset($request->attachment[$key])) {

                    //delete previous image if has any
                    if ($Expense->attachment) {
                        if (File::exists(public_path('uploads/expense-attachment/'), $Expense->attachment->file_name)) {
                            File::delete("public/uploads/expense-attachment/" . $Expense->attachment->file_name);
                        }
                    }

                    $picture = $request->file('attachment')[$key];
                    $picture_name = '';
                    if ($picture) {
                        $type = $picture->getClientOriginalExtension();
                        $picture_name = Str::random(10) . '.' . $type;
                        $picture->move(public_path('uploads/expense-attachment'), $picture_name);
                    }

//                    update or create file here
                    if ($Expense->attachment) {
                        $Expense->attachment->update([
                            'file_name' => $picture_name,
                            'type' => $type,
                            'object' => 'Expense',
                            'object_id' => $Expense->id,
                            'context' => 'expense'
                        ]);
                    } else {
                        Attachment::create([
                            'file_name' => $picture_name,
                            'type' => $type,
                            'object' => 'Expense',
                            'object_id' => $Expense->id,
                            'context' => 'expense'
                        ]);
                    }
                }

            }
        }

        return response()->json(['status' => true,
            'message' => 'Expense saved successfully']);
    }

    public
    function delete(Request $request)
    {
        Expense::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Expense Deleted Successfully'
        ]);
    }

    public
    function expenseFilterReport(Request $request)
    {
        $from_date = SiteHelper::reformatDbDate(Str::replace("'\'", '', $request->from_date));
        $to_date = SiteHelper::reformatDbDate(Str::replace("'\'", '', $request->to_date));

        $Expenses = Expense::with(['expense_head', 'employee', 'created_user'])->when($from_date, function ($Expense, $from_date) {
            return $Expense->where('date', '>=', $from_date);
        })->when($to_date, function ($Expense, $to_date) {
            return $Expense->where('date', '<=', $to_date);
        })->when($request->employee_id, function ($Expense, $employee_id) {
            return $Expense->where('employee_id', $employee_id);
        })->when($request->expense_head_id, function ($Expense, $expense_head_id) {
            return $Expense->where('expense_head_id', $expense_head_id);
        })->get();

        $expenseArray = [];
        $view = '';
        if ($request->report_type == 'datewise') {
            foreach (collect($Expenses)->toArray() as $expens) {
                $expenseArray[$expens['date']][] = $expens;
            }

            $view = 'expense.datewise_report';
        } else if ($request->report_type == 'expenseheadwise') {
            foreach (collect($Expenses)->toArray() as $expens) {
                $expenseArray[$expens['expense_head']['name']][] = $expens;
            }

            $view = 'expense.expense_head_wise_report';
        } else if ($request->report_type == 'employeewise') {
            foreach (collect($Expenses)->toArray() as $expens) {

                $expenseArray[$expens['employee']['first_name'] . $expens['employee']['last_name']][] = $expens;
            }

            $view = 'expense.employee_wise_report';
        }
        return view($view, ['expense' => collect($expenseArray)->toArray(), 'setting' => Setting::find(1)]);
    }
}
