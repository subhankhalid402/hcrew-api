<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Attachment;
use App\Models\Income;
use App\Models\School;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class IncomeController extends Controller
{

    public function all()
    {
        $Incomes = Income::with('income_head')->orderByDesc('id')->get();
        return response()->json([
            'status' => true,
            'data' => $Incomes
        ]);
    }

    public function create()
    {
        return view('income.create');
    }

    public function filterView()
    {
        return view('income.report');
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

                $Income = Income::create([
                    'date' => SiteHelper::reformatDbDate($request->date),
                    'income_head_id' => $request->income_head_id[$key],
                    'amount' => $request->amount[$key],
                    'client_id' => $request->client_id[$key],
                    'contract_id' => $request->contract_id[$key],
                    'notes' => $request->notes[$key],
                    'created_by' => Auth::id(),
                ]);

                if (isset($request->attachment[$key])) {
                    $picture = $request->file('attachment')[$key];
                    $picture_name = '';
                    if ($picture) {
                        $type = $picture->getClientOriginalExtension();
                        $picture_name = Str::random(10) . '.' . $type;
                        $picture->move(public_path('uploads/income-attachment'), $picture_name);
                    }

                    Attachment::create([
                        'file_name' => $picture_name,
                        'type' => $type,
                        'object' => 'Income',
                        'object_id' => $Income->id
                    ]);
                }

            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Record saved successfully'
        ]);
    }

    public function show(Request $request)
    {
        return response()->json([
            'status' => true,
            'data' => Income::with(['income_head', 'attachment'])->find($request->id)
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

                $Income = Income::find($request->income_id);
                $Income->update([
                    'date' => SiteHelper::reformatDbDate($request->date),
                    'income_head_id' => $request->income_head_id[$key],
                    'amount' => $request->amount[$key],
                    'client_id' => $request->client_id[$key],
                    'contract_id' => $request->contract_id[$key],
                    'notes' => $request->notes[$key],
                    'created_by' => Auth::id(),
                ]);

                if (isset($request->attachment[$key])) {

                    //delete previous image if has any
                    if ($Income->attachment) {
                        if (File::exists(public_path('uploads/income-attachment/'), $Income->attachment->file_name)) {
                            File::delete("public/uploads/income-attachment/" . $Income->attachment->file_name);
                        }
                    }

                    $picture = $request->file('attachment')[$key];
                    $picture_name = '';
                    if ($picture) {
                        $type = $picture->getClientOriginalExtension();
                        $picture_name = Str::random(10) . '.' . $type;
                        $picture->move(public_path('uploads/income-attachment'), $picture_name);
                    }


                    //update or create file here
                    if ($Income->attachment) {
                        $Income->attachment->update([
                            'file_name' => $picture_name,
                            'type' => $type,
                            'object' => 'Income',
                            'object_id' => $Income->id,
                            'context' => 'income'
                        ]);
                    } else {
                        Attachment::create([
                            'file_name' => $picture_name,
                            'type' => $type,
                            'object' => 'Income',
                            'object_id' => $Income->id,
                            'context' => 'income'
                        ]);
                    }

                }

            }
        }

        return response()->json([
            'status' => true,
            'message' => 'Record saved successfully'
        ]);
    }

    public function delete(Request $request)
    {
        Income::where(['id' => $request->id])->delete();
        return response()->json([
            'status' => true,
            'message' => 'Record Deleted Successfully'
        ]);
    }

    public function incomeFilterReport(Request $request)
    {
        $from_date = SiteHelper::reformatDbDate(Str::replace("'\'", '', $request->from_date));
        $to_date = SiteHelper::reformatDbDate(Str::replace("'\'", '', $request->to_date));

        $Incomes = Income::with(['income_head', 'client', 'created_user'])->when($from_date, function ($Income, $from_date) {
            return $Income->where('date', '>=', $from_date);
        })->when($to_date, function ($Income, $to_date) {
            return $Income->where('date', '<=', $to_date);
        })->when($request->client_id, function ($Income, $client_id) {
            return $Income->where('client_id', $client_id);
        })->when($request->income_head_id, function ($Income, $income_head_id) {
            return $Income->where('income_head_id', $income_head_id);
        })->get();

        $incomeArray = [];
        $view = '';
        if ($request->report_type == 'datewise') {
            foreach (collect($Incomes)->toArray() as $income) {
                $incomeArray[$income['date']][] = $income;
            }

            $view = 'income.datewise_report';
        } else if ($request->report_type == 'incomeheadwise') {
            foreach (collect($Incomes)->toArray() as $income) {
                $incomeArray[$income['income_head']['name']][] = $income;
            }

            $view = 'income.income_head_wise_report';
        } else if ($request->report_type == 'clientwise') {
            foreach (collect($Incomes)->toArray() as $income) {

                $incomeArray[$income['client']['name']][] = $income;
            }

            $view = 'income.client_wise_report';
        }
        return view($view, ['income' => collect($incomeArray)->toArray(), 'setting' => Setting::find(1)]);
    }
}
