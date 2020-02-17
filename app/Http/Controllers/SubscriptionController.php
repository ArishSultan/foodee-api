<?php


namespace App\Http\Controllers;
use App\Subscription;
use Illuminate\Http\Request;
use Carbon\Carbon;


class SubscriptionController extends Controller
{

    public function purchaseSubscription(Request $request) {
//        $user = $request->user();
        $userId = $request->id;
        $subscriptionType = $request->type; // 1 => Per Month, 2=> Per Year
        $userExist = Subscription::where("user_id",$userId)->first();
        $endDate = 0;
        if($userExist) {
          //If purchasing again
            $existingSubscription = new Subscription;
            $currentTime =Carbon::now();
            $existingSubscription->start_date = $currentTime;
            $existingSubscription->status = "active";
            if($subscriptionType == 1) {
                $endDate = $currentTime->addMonth(1);
                $existingSubscription->end_date =$endDate;
            }else if($subscriptionType == 2) {
                $endDate = $currentTime->addYear(1);
                $existingSubscription->end_date =$endDate;
            }

            if($existingSubscription->save()) {
                return response()->json(['success'=>true, 'message'=>'Subscription successfully Renewed', 'subscriptionEnd'=>'End Date']);
            }
            return response()->json(['success'=>factory(), 'message'=>'There is an error purchasing Subscription, Kindly contact support']);

        }else {

            //If purchase is new
            $newSubscription = new Subscription;
            $currentTime =Carbon::now();
            $newSubscription->user_id = $userId;
            $newSubscription->start_date = $currentTime;
            $newSubscription->status = "active";

            if($subscriptionType == 1) {
                $endDate = $currentTime->addMonth(1);
                $newSubscription->end_date = $endDate;
            }else if($subscriptionType == 2) {
                $endDate = $currentTime->addYear(1);
                $newSubscription->end_date = $endDate;
            }
            if($newSubscription->save()) {
                return response()->json(['success'=>true, 'message'=>'Subscription successfully purchased', 'subscriptionEnd'=>'End Date']);
            }
            return response()->json(['success'=>factory(), 'message'=>'There is an error purchasing Subscription, Kindly contact support']);
        }
    }

    public function checkSubscription(Request $request) {

//        $user = $request->user();
        $userId = $request->id;
        $date = new Carbon;
        $userExist = Subscription::where("user_id",$userId)->first();

        if($userExist) {
//            $currentTime =Carbon::now();
            $startDate = $userExist->start_date;
            $endDate = $userExist->end_date;
            return response()->json(['success'=>true, 'subscription'=>start_date, 'subscriptionEnd'=>$startDate,'message' => 'Your subscription is active']);



            if($startDate > $endDate)  {
                return response()->json(['success'=>true, 'subscription'=>'active', 'subscriptionEnd'=>'End Date','message' => 'Your subscription is active']);
            }
            $userExist->status = 'inactive';
            $userExist->user_id = $userId;
            $userExist->save();
            return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);
        }
        return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);


    }

    public function sayHello() {
        return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);

    }


}
