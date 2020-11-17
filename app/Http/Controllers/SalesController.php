<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\DeleveredOrder;


class SalesController extends Controller
{
    public function test(Request $request){
        try{
            $Date = date("Y-m-d"); //current date
            $year = $request->all();
            // dd($year);
            $Delivered = Order::all()
                ->filter(function($order){
                    return $order->preferred_delivery_date == date("Y-m-d");
                })
                ->mapToGroups(function($order) use ($request){
                    return [
                        $order['preferred_delivery_date']->format($request) => $order      
                ];
                })
                ->mapToGroups(function($order,$choosenFilter){
            
                    return [
                        $choosenFilter => $order->sum("ubeHalayaJar_qty")
                    ];
            
                })
                ->map(function($order,$choosenFilter){
                    return $order->first() ;  
                })
                ->sortKeys();
            
                return response($Delivered);
        } catch (\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e->getMessage()]);
        }
    }
    
    public function index(Request $request){
        try{
            $Date = date("Y-m-d"); //current date
            $year = $request->all();

            $Delivered = Order::select(\DB::raw('sum("ubeHalayaJar_qty")as total'), 'preferred_delivery_date')
            ->where([
                ['order_status', '=','Delivered'],
                // ['delivery_date', '<=', $Date],
                [\DB::raw("YEAR(preferred_delivery_date)"), '=', $year['year']],
                [\DB::raw("MONTH (preferred_delivery_date)"), '=', $year['month']]
            ])
            ->groupBy('preferred_delivery_date')
            ->orderBy('preferred_delivery_date', 'ASC')
            ->get();
            
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        }
        
        // $Date = $Delivered[0]['delivery_date'];
        return response($Delivered);
    

        
        // return response()->json($Delivered);
    }
    //Daily for Tubs
    public function indexTub(Request $request){
        try{
        $Date = date("Y-m-d"); //current date
        $year = $request->all();
        $Delivered = Order::select(\DB::raw(`sum("ubeHalayaTub_qty")as total`), 'preferred_delivery_date')
        ->where([
            ['order_status', '=','Delivered'],
            // ['delivery_date', '<=', $Date],
            [\DB::raw("EXTRACT(YEAR FROM preferred_delivery_date)"), '=', $year['year']],
            [\DB::raw('EXTRACT(MONTH FROM preferred_delivery_date)'), '=', $year['month']]
        ])
        ->groupBy('preferred_delivery_date')
        ->orderBy('preferred_delivery_date', 'ASC')
        ->get();
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        // $Date = $Delivered[0]['delivery_date'];
        }
        return response($Delivered);
        // return response()->json($Delivered);
    }

    public function indexWeekly(Request $request){
       try{
        $req = $request->all();

        /**Getting the first delivery of the year */

        $firstDelivery = Order::select('preferred_delivery_date')
        ->where(\DB::raw('EXTRACT(YEAR FROM preferred_delivery_date)'), '=', $request['year'])
        ->first();

        /** Ends here */
        $weekNumber = date("W", strtotime($firstDelivery['preferred_delivery_date']));
        $currentYear = date("Y", strtotime($firstDelivery['preferred_delivery_date']));
        $weekArray = $this->getStartAndEndWeek($weekNumber,$currentYear);
        $weeklyData =[];
        for($i = 0; $i < sizeof($weekArray); $i++){
            \Log::info('Order::select sum(ubeHalayaJar_qty)->where([[preferred_delivery_date, >=, ' . $weekArray[$i]['start'] . ' ], [preferred_delivery_date, <=, '. $weekArray[$i]['end'] .' ])');

            $getWeeklySales =Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals'))
            ->where([
                ["preferred_delivery_date", ">=", $weekArray[$i]['start']],
                ["preferred_delivery_date", "<=", $weekArray[$i]['end']]
            ])
            ->get();

            array_push($weeklyData,$getWeeklySales);
        }
       }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
       }
            // array_push($name of array, $data)
        
        return response()->json($weeklyData);
        
        
    
    }
    public function indexWeeklyTub(Request $request){
       try{
        $req = $request->all();

        /**Getting the first delivery of the year */

        $firstDelivery = Order::select('preferred_delivery_date')
        ->where(\DB::raw('EXTRACT(YEAR FROM preferred_delivery_date)'), '=', $request['year'])
        ->first();

        /** Ends here */
        $weekNumber = date("W", strtotime($firstDelivery['preferred_delivery_date']));
        $currentYear = date("Y", strtotime($firstDelivery['preferred_delivery_date']));
        $weekArray = $this->getStartAndEndWeek($weekNumber,$currentYear);
        $weeklyData =[];
        for($i = 0; $i < sizeof($weekArray); $i++){
            \Log::info('Order::select sum(ubeHalayaTub_qty)->where([[preferred_delivery_date, >=, ' . $weekArray[$i]['start'] . ' ], [preferred_delivery_date, <=, '. $weekArray[$i]['end'] .' ])');

            $getWeeklySales =Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals'))
            ->where([
                ["preferred_delivery_date", ">=", $weekArray[$i]['start']],
                ["preferred_delivery_date", "<=", $weekArray[$i]['end']]
            ])
            ->get();

            array_push($weeklyData,$getWeeklySales);
        }
            // array_push($name of array, $data)
        
       }catch(\Exception $e){
             return response()->json(["message"=>"invalid", "data"=>$e]);
       }
        return response()->json($weeklyData);
    }


    public function getStartAndEndWeek($week, $year) 
{
    //Below gives week from mon to sun
    try{
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
    }catch(\Exception $e){
        return response()->json(["message"=>"invalid", "data"=>$e]);
    }
    return $weeks;
}
    public function indexMonthly(Request $request){
        try{
        $year = $request->all();
        $monthlySales = Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals')
        , \DB::raw("EXTRACT(MONTH FROM preferred_delivery_date) as months"))
        ->whereYear('preferred_delivery_date', '=', $year['year'])
        ->groupBy('months')
        ->get();
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        }
        return response()->json($monthlySales);
        
    }
    public function indexMonthlyTub(Request $request){
        try{
        $year = $request->all();
        $monthlySales = Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals')
        , \DB::raw("EXTRACT(MONTH FROM preferred_delivery_date) as months"))
        ->whereYear('preferred_delivery_date', '=', $year['year'])
        ->groupBy('months')
        ->get();
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        }
        return response()->json($monthlySales);
        
    
    }

    public function indexYearly(Request $request){
    try{
        $yearlySales = Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals'),
        \DB::raw("EXTRACT(YEAR FROM preferred_delivery_date) as years"))
        ->groupBy('years')
        ->get();
    }catch(\Exception $e){
        return response()->json(["message"=>"invalid", "data"=>$e]);
    }
        return response()->json($yearlySales);
    }
    public function indexYearlyTub(Request $request){
        try{
        $yearlySales = Order::select(\DB::raw('sum("ubeHalayaJar_qty") as totals'),
        \DB::raw("EXTRACT(YEAR FROM preferred_delivery_date) as years"))
        ->groupBy('years')
        ->get();
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        }
        return response()->json($yearlySales);
    }
    public function selectYear(Request $request){
        try{
        $selectingYear = Order::select(\DB::raw('EXTRACT(YEAR FROM preferred_delivery_date) as years'))
        ->groupBy('years')
        ->get();
        }catch(\Exception $e){
            return response()->json(["message"=>"invalid", "data"=>$e]);
        }
        return response()->json($selectingYear);
    }






    
}
