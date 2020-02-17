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
        if($userExist) {
          //If purchasing again
            $existingSubscription = new Subscription;
            $currentTime =Carbon::now();
            $existingSubscription->start_date = $currentTime;
            $existingSubscription->status = "active";
            if($subscriptionType == 1) {
                $existingSubscription->end_date =$currentTime->addMonth(1);
            }else if($subscriptionType == 2) {
                $existingSubscription->end_date = $currentTime->addYear(1);;
            }

            if($existingSubscription->save()) {
                return response()->json(['success'=>true, 'message'=>'Subscription successfully Renewed', 'subscriptionEnd'=>'End Date']);
            }
            return response()->json(['success'=>factory(), 'message'=>'There is an error purchasing Subscription, Kindly contact support']);

        }else {

            //If purchase is new
            $newSubscription = new Subscription;
            $currentTime =Carbon::now();
            $newSubscription->userId = $userId;
            $newSubscription->start_date = $currentTime;
            $newSubscription->status = "active";

            if($subscriptionType == 1) {
                $newSubscription->end_date =$currentTime->addMonth(1);
            }else if($subscriptionType == 2) {
                $newSubscription->end_date = $currentTime->addYear(1);;
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
        $userExist = Subscssription::where("user_id",$userId)->first();
        if($userExist) {
//            $currentTime =Carbon::now();
            $endDate = $userExist->end_date;

            if($date > $endDate)  {
                return response()->json(['success'=>true, 'subscription'=>'active', 'subscriptionEnd'=>'End Date','message' => 'Your subscription is active']);
            }
            $userExist->status = 'inactive';
            $userExist->save();
            return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);
        }
        return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);


    }

    public function sayHello() {
        return response()->json(['success'=>true, 'subscription'=>'inactive', 'message' => 'Your subscription is inactive']);

    }


}
