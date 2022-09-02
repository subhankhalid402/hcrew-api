<?php

namespace App\Http\Controllers;

use App\Helpers\SiteHelper;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeCategory;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Job;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Exp;

class DashboardController extends Controller
{
    public function countTotal()
    {

        $Role = Role::where('role_key', 'admin')->first();
        $Users = User::all();
        $total_user = $Users->count();
        $total_admin = $Users->where('role_id', $Role->id)->count();
        $total_employee = Employee::count();
        $total_client = Client::count();


//        CONTRACT TOTAL AND PERCENTAGE SHOW
        $contractStatusArray = ['completed', 'cancelled', 'inprogress', 'awaiting'];
        $contractArray = [];
        $statusPercentageArray = [];
        $Contracts = Contract::all();
        $total_contracts = $Contracts->count();

        foreach ($contractStatusArray as $status) {
            $contract_count = $Contracts->where('contract_status', $status)->count();
            $per = ($contract_count / $total_contracts) * 100;
            $contractArray[$status] = $contract_count;
            $statusPercentageArray[$status] = round($per, 1);
        }
        $contractArray['contracts'] = $total_contracts;
        $contractArray['status_per'] = $statusPercentageArray;


        return response()->json([
            'users' => $total_user,
            'admins' => $total_admin,
            'employees' => $total_employee,
            'clients' => $total_client,
            'contracts' => $contractArray,
            'expense' => $this->getExpense(),
            'income' => $this->getIncome(),
            'employeeCat' => $this->catrgoryWiseEmployee()
        ]);
    }

    public function getMonthlyPayment()
    {
        //        GET LAST 3 MONTHS
        $date = Carbon::now();
        $start_date = Carbon::parse($date)->format('Y-m-d');
        $last_date = $date->subMonth(2)->format('Y-m-d');

        $to = SiteHelper::reformatDate($start_date);
        $from = SiteHelper::reformatDate($last_date);

        $period = \Carbon\CarbonPeriod::create($from, '1 month', $to);

        $Payments = Payment::all();
        $total_net_payment = $Payments->sum('net_payment');

        $paymentArray = [];
        foreach ($period as $date) {
            $month = $date->format('Y-m');

            $total_payment = $Payments->where('month', $month)->sum('net_payment');
            $paymentArray[$month] = $total_payment;
        }
        $paymentArray['total_net'] = $total_net_payment;

        return $paymentArray;

    }

    public function catrgoryWiseEmployee()
    {
        $EmployeeCategorys = EmployeeCategory::withCount('employees')->get();
        return $EmployeeCategorys;

    }

    public function barChart()
    {
        $Contracts = Contract::with('jobs')->limit(15)->orderByDesc('id')->get();
        $contract_ids = $Contracts->pluck('id')->toArray();

        $setData =[];
        $label = [];
        $colors = [];
        foreach ($Contracts as $key => $Contract) {
            $id = $Contract->id;
            $total_amount = Payment::with('job_detail.job')->has('job_detail')->whereHas('job_detail', function ($JobDetail) use ($id) {
                $JobDetail->whereHas('job', function ($Job) use ($id) {
                    $Job->where('contract_id', $id);
                });
            })->sum('net_payment');

            $setData[$key] = $total_amount;
            $label[$key] = $Contract->title;
            $colors[$key] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        }


        return response()->json([
            'data' => $setData,
            'label' => $label,
            'color' => $colors
        ]);
    }

    public function pieChart()
    {

        $Clients = Client::with('contracts')->withCount('contracts')->orderByDesc('id')->get();
        $dataSet = [];
        $label = [];
        foreach ($Clients as $key => $client){
            $dataSet[$key] = $client->contracts_count;
            $label[$key] = $client->name;
        }
        return response()->json([
            'data' => $dataSet,
            'label' => $label
        ]);
    }

    public function donutChart()
    {
        $date = Carbon::now();
        $last_date = $date->subdays(29)->format('Y-m-d'); // 8

        $from = SiteHelper::reformatDate($last_date);

        $Expenses = Expense::with('expense_head')->whereDate('date', '>=', $from)->get()->groupBy('expense_head_id');

        $return_data = [];
        foreach ($Expenses as $key => $Head) {
            $return_data['label'][] = $Head->first()->expense_head->name;
            $return_data['returnData'][] = $Head->sum('amount');
//            $return_data[$key]['summary'] = $Head->map(function ($row) {
//                return $row->sum('amount');
//            });
//            return $return_data;
        }

        return response()->json([
            'status' => true,
            'data' => $return_data,
        ]);
    }

    public function incomePieChart()
    {
        $date = Carbon::now();
        $start_date = Carbon::parse($date)->format('Y-m-d');
        $last_date = $date->subdays(29)->format('Y-m-d'); // 8

        $to = SiteHelper::reformatDate($start_date);
        $from = SiteHelper::reformatDate($last_date);

        $Incomes = Income::with('income_head')->whereDate('date', '>=', $from)->get()->groupBy('income_head_id');

        $return_data = [];
        foreach ($Incomes as $key => $Income) {
            $return_data['label'][] = $Income->first()->income_head->name;
            $return_data['returnData'][] = $Income->sum('amount');
        }

        return response()->json([
            'status' => true,
            'data' => $return_data
        ]);
    }

    public function getExpense()
    {
        //        GET LAST 3 MONTHS
        $date = \Carbon\Carbon::now();
        $start_date = Carbon::parse($date)->format('Y-m-d');
        $last_date = $date->subMonth(2)->format('Y-m-d');

        $to = SiteHelper::reformatDate($start_date);
        $from = SiteHelper::reformatDate($last_date);

        $period = \Carbon\CarbonPeriod::create($from, '1 month', $to);

//        GET ALL EXPENSE FOR SUM OF AMOUNT
        $Expenses = Expense::all();
        $total_expense_amount = $Expenses->sum('amount');


//        GET SUM OF LAST 3 MONTHS AMOUNT
        $expenseArray = [];
        foreach ($period as $date) {
            $month = $date->format("Y-m");
            $last_month = Carbon::parse($date)->subMonth(1)->format('Y-m');

            $expense_amount = $Expenses->where('month', $month)->sum('amount');

            $previous_month_amount = $Expenses->where('month', $last_month)->sum('amount');

            $expenseArray[SiteHelper::reformatReadableMonthNice($month)] = $expense_amount;
        }
        $expenseArray['Total'] = $total_expense_amount;

        return $expenseArray;

    }

    public function getIncome()
    {

        //        GET LAST 3 MONTHS
        $date = \Carbon\Carbon::now();
        $start_date = Carbon::parse($date)->format('Y-m-d');
        $last_date = $date->subMonth(2)->format('Y-m-d');

        $to = SiteHelper::reformatDate($start_date);
        $from = SiteHelper::reformatDate($last_date);

        $period = \Carbon\CarbonPeriod::create($from, '1 month', $to);

        //        GET INCOME FOT SUM OF AMOUNT
        $Incomes = Income::all();
        $total_income_amount = $Incomes->sum('amount');

//        3 MONTHS AMOUNT SUM
        $incomeArray = [];
        foreach ($period as $date) {
            $month = $date->format("Y-m");
            $income_amount = $Incomes->where('month', $month)->sum('amount');
            $incomeArray[SiteHelper::reformatReadableMonthNice($month)] = $income_amount;
        }

        $incomeArray['Total'] = $total_income_amount;

        return $incomeArray;
    }

    public function recentActivities()
    {
        $incomes = DB::table("incomes as in")
            ->join('employees as e', 'in.employee_id', '=', 'e.id')
            ->select("in.amount"
                , "e.first_name",
                "in.date",
                "in.created_at",
               )->addSelect(DB::raw("'Income'"));

        $expenses = DB::table("expenses as ex")
            ->join('employees as e', 'ex.employee_id', '=', 'e.id')
            ->select("ex.amount", "e.first_name", "ex.date","ex.created_at",)
            ->addSelect(DB::raw("'expense'"))
            ->unionAll($incomes)
            ->limit(100)
            ->latest()
            ->get();


        return response()->json([
            'status' => true,
            'data' => $expenses
        ]);
    }

}
