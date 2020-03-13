<?php
namespace App\Http\Controllers;
use App\Mail\ConfirmationEmail;
use App\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function __construct()
    {
//        $this->middleware('client');
    }

    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */

    public function signup(Request $request)
    {


        $username = $request->username;
        $email = $request->email;
        $phone = $request->phone;
        $device_token = $request->device_token;
        $password = bcrypt($request->password);
        $timezone = $request->timezone;

        $existingUser = User::where('email',$email)->first();
        if($existingUser) {
            return response()->json(['message' => 'Email already exists.', 'status' => false, 'access_token' => null], 200);
        }


        $user = new User;
        $user->username = $username;
        $user->email = $email;
        $user->phone = $phone;
        $user->password = $password;
        $user->device_token = $device_token;
        $user->timezone = $timezone;
//
//        $user = new User([
//                'username' => $username,
//                'email' => $email,
//                'phone' => $phone,
//                'password' => $password,
//                'device_token' => $device_token,
//                'timezone' => $timezone
//            ]);
//

        if($user->save()){
                    $profile = new Profile();
                    $profile->user_id = $user->id;
                    if($profile->save()){
                        $tokenResult = $user->createToken('Foodee');
                        $token = $tokenResult->accessToken;

                        $dataEmail = ["username"=>$user->username, "email"=>$user->email, "uid"=>$user->id];
                        Mail::to($user->email)->send(new ConfirmationEmail($dataEmail));

                        return response()->json([
                            'status'=>true,
                            'access_token' => $tokenResult->accessToken,
                            'message' => 'Your account has been created successfully',
                            'user' => $user
                        ], 201);
                    }

                }else {
            return response()->json(['message' => 'Something went wrong', 'status' => false, 'access_token' => null], 200);

        }


//        if($existingUser) {
//            return response()->json(['message' => 'Email already exists.', 'status' => false, 'access_token' => null], 200);
//        }else {
//
//            $user = new User([
//                'username' => $username,
//                'email' => $email,
//                'phone' => $phone,
//                'password' => $password,
//                'device_token' => $device_token,
//                'timezone' => $timezone
//            ]);
//
//            if(!$user) {
//
//                throw new HttpException(500);
//
//            }else {
//                if($user->save()){
//                    $profile = new Profile();
//                    $profile->user_id = $user->id;
//                    if($profile->save()){
//                        $tokenResult = $user->createToken('Foodee');
//                        $token = $tokenResult->accessToken;
//
//                        $dataEmail = ["username"=>$user->username, "email"=>$user->email, "uid"=>$user->id];
//                        Mail::to($user->email)->send(new ConfirmationEmail($dataEmail));
//
//                        return response()->json([
//                            'status'=>true,
//                            'access_token' => $tokenResult->accessToken,
//                            'message' => 'Your account has been created successfully',
//                            'user' => $user
//                        ], 201);
//                    }
//
//                }
//            }
//        }


    }


    /*
     * account confirm
     */
    public function confirm($email, $id)
    {
        $user = User::where('id', $id)->where('email', $email)->where('email_confirm', 0)->first();
        if(isset($user)){
            $user->email_confirm = true;
            if($user->save()){
                return view('emails.user.confirmed');
            }
        }
    }

    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {

        $email = $request->email;
        $timezone = $request->timezone;
        $existingUser = User::where('email',$email)->first();

        if($existingUser) {

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials))
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect username or password!',
                    'token' => null
                ], 201);
            User::where('email', $email)->update(['device_token'=>$request->device_token, 'timezone'=>$timezone]);
            $user = $request->user();
            $tokenResult = $user->createToken('Foodee');
            $token = $tokenResult->accessToken;

            return response()->json([
                'status' => true,
                'access_token' => $token,
                'token_type' => 'Bearer',
                'user' => $user,
                'message' => 'Login Successfull',


            ], 200);
        }

    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }


    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request, $id)
    {
//        $user = $request->user();
        $user = User::where('id', $id)->first();
        if(isset($user->profile->foods)){
            $user['profile'] = $user->profile->foods;
        }

        return response()->json($user);
    }

    public function updateToken(Request $request)
    {
        $user = $request->user();
        $user->device_token = $request->device_token;
        if($user->save()){
            return response()->json(['success'=>true, 'message'=>'FCM token has been updated successfully']);
        }
    }

    /*
     * Update lat, lng and fetching users within 10miles radius.
     */
    public function userLatLng(Request $request)
    {
        $collection = collect();
        $user = $request->user();
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        if($user->save()){
            $users = DB::select(DB::raw("SELECT
  users.id, users.username, users.email, users.phone, users.lat, users.lng,
   profiles.user_id,
    profiles.avatar,
     profiles.cover,
     profiles.message,
     profiles.location,
     profiles.age,
     profiles.contribution,
     profiles.is_age_private,
      (
    3959 * acos (
      cos ( radians($request->lat) )
      * cos( radians( lat ) )
      * cos( radians( lng ) - radians($request->lng) )
      + sin ( radians($request->lat) )
      * sin( radians( lat ) )
    )
  ) AS distance
FROM users join profiles on profiles.user_id = users.id
HAVING distance <= 10
ORDER BY distance
LIMIT 0 , 20;"));

            foreach($users as $user){
//                echo $user->user_id;
                $user->is_age_private = ($user->is_age_private == 1 ? true : false);
                $user->foods = Profile::where('user_id', $user->user_id)->select('id', 'user_id')->first()->foods;
            }

            return response()->json(["success"=>true, "data"=>$users]);

        }
    }

    /*
     * Search users for tagging
     */
    public function findPeoples(Request $request)
    {
        $username = $request->query('username');
        return User::where('username', 'LIKE', "%{$username}%")->get();
    }
}
