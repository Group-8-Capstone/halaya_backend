<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeleveredOrder;


class SalesController extends Controller
{
    public function index(Request $request){
        $Date = date("Y-m-d"); //current date
        $year = $request->all();
        $Delivered = Order::select(\DB::raw('sum(ubeHalayaJar_qty)as total'), 'preferred_delivery_date')
        ->where([
            ['order_status', '=','Delivered'],
            // ['delivery_date', '<=', $Date],
            [\DB::raw('Year(`preferred_delivery_date`)'), '=', $year['year']],
            [\DB::raw('Month(`preferred_delivery_date`)'), '=', $year['month']]
        ])
        ->groupBy('preferred_delivery_date')
        ->orderBy('preferred_delivery_date', 'ASC')
        ->get();
        // $Date = $Delivered[0]['delivery_date'];
        return response($Delivered);
        // return response()->json($Delivered);
    }

    public function indexWeekly(Request $request){
        $req = $request->all();

        /**Getting the first delivery of the year */

        $firstDelivery = Order::select('preferred_delivery_date')
        ->where(\DB::raw('Year(`preferred_delivery_date`)'), '=', $request['year'])
        ->first();

        /** Ends here */
        $weekNumber = date("W", strtotime($firstDelivery->delivery_date));
        $currentYear = date("Y", strtotime($firstDelivery->delivery_date));
        $weekArray = $this->getStartAndEndWeek($weekNumber,$currentYear);
        $weeklyData =[];
        for($i = 0; $i < sizeof($weekArray); $i++){
            \Log::info('Order::select sum(order_quantity)->where([[preferred_delivery_date, >=, ' . $weekArray[$i]['start'] . ' ], [preferred_delivery_date, <=, '. $weekArray[$i]['end'] .' ])');

            $getWeeklySales = Order::select(\DB::raw("sum(`ubeHalayaJar_qty`) as totals"))
            ->where([
                ["preferred_delivery_date", ">=", $weekArray[$i]['start']],
                ["preferred_delivery_date", "<=", $weekArray[$i]['end']]
            ])
            ->get();

            array_push($weeklyData,$getWeeklySales);
        }
            // array_push($name of array, $data)
        
        return response()->json($weeklyData);
        
        
    
    }
    public function getStartAndEndWeek($week, $year) 
{
    //Below gives week from mon to sun
    $range = date("W") - $week ; // first day the delivery to the present
    $weeks = [];
    $dto = new \DateTime();
    $dto->setISODate($year, $week)->modify('-1 days');    
    for($i = 0; $i <= $range; $i++) {               
        $weeks[$i]['start'] = $dto->format('Y-m-d');        
        $dto->modify('+6 days');        
        $weeks[$i]['end'] = $dto->format('Y-m-d');        
        $dto->modify('+1 days');
    }
    return $weeks;
}
    public function indexMonthly(Request $request){
        $year = $request->all();
        $monthlySales = Order::select(\DB::raw("sum(`ubeHalayaJar_qty`) as `totals`")
        , \DB::raw("Month(`preferred_delivery_date`) as `months`"))
        ->whereYear('preferred_delivery_date', '=', $year['year'])
        ->groupBy('months')
        ->get();
        return response()->json($monthlySales);
    }
    public function indexYearly(Request $request){
        $yearlySales = Order::select(\DB::raw("sum(`order_quantity`) as `totals`"),
        \DB::raw("Year(`preferred_delivery_date`) as `years`"))
        ->groupBy('years')
        ->get();
        return response()->json($yearlySales);
    }
    public function selectYear(Request $request){
        $selectingYear = Order::select(\DB::raw("Year(`preferred_delivery_date`) as `years`"))
        ->groupBy('years')
        ->get();
        return response()->json($selectingYear);
    }
    
}
