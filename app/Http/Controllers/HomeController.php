<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (view()->exists($request->path())) {
            return view($request->path());
        }
        return abort(404);
    }

    public function root(Request $request)
    {
        // Fetch the latest poolfunds data
        $poolfunds = DB::table('poolfunds')
            ->orderBy('last_updated', 'desc')
            ->first();

        // Aggregate data for machines
        $machineStats = DB::table('machines')
            ->selectRaw('
            SUM(total_revenue) as total_machine_asset,
            SUM(total_sales_volume) as total_water_sale,
            SUM(bottles_saved_count) as total_bottle_sale
        ')
            ->first();

        // Determine the group type: week, month, or year
        $groupType = $request->get('group_type', 'monthly'); // Default to monthly if not specified

        // Adjust SQL based on group type
        switch ($groupType) {
            case 'weekly':
                $groupBy = DB::raw("YEARWEEK(sale_date_time) as group_label");
                break;
            case 'monthly':
                $groupBy = DB::raw("DATE_FORMAT(sale_date_time, '%Y-%m') as group_label");
                break;
            case 'yearly':
                $groupBy = DB::raw("YEAR(sale_date_time) as group_label");
                break;
            default:
                $groupBy = DB::raw("DATE_FORMAT(sale_date_time, '%Y-%m') as group_label");
                $groupType = 'monthly';
                break;
        }

        // Query for average machine sale data
        $salesQuery = DB::table('machinerevenuetransactions')
            ->select($groupBy, DB::raw("AVG(sale_amount) as average_sale"))
            ->groupBy('group_label')
            ->orderBy('group_label', 'asc')
            ->get();

        // Format data for the graph
        $salesData = $salesQuery->map(function ($sale) {
            return [
                'label' => $sale->group_label,
                'average' => round($sale->average_sale, 2),
            ];
        });

        return view('index', compact('poolfunds', 'machineStats', 'salesData', 'groupType'));
    }








    /*Language Translation*/
    public function lang($locale)
    {
        if ($locale) {
            App::setLocale($locale);
            Session::put('lang', $locale);
            Session::save();
            return redirect()->back()->with('locale', $locale);
        } else {
            return redirect()->back();
        }
    }

    public function FormSubmit(Request $request)
    {
        return view('form-repeater');
    }
}
