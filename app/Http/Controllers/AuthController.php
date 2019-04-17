<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Illuminate\Support\Facades\DB;

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
        $request->validate([
            'username' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'password' => 'required|string|confirmed'
        ]);
//        if ($request->fails()) {
//            return $this->errorResponse($request->errors()->all());
//        }
        $user = new User([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => bcrypt($request->password)
        ]);
        if($user->save()){
            $tokenResult = $user->createToken('Foodee');
            $token = $tokenResult->accessToken;
            return response()->json([
                'status'=>true,
                'access_token' => $tokenResult->accessToken,
                'token_type' => 'Bearer',
                'message' => 'Your account has been created successfully',
                'user' => $user,
                'status_code' => 201
            ], 201);
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
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'status'=>false,
                'message' => 'Incorrect username or password!',
                'status_code' => 401
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Foodee');
        $token = $tokenResult->accessToken;
//        if ($request->remember_me)
//            $token->expires_at = Carbon::now()->addWeeks(1);
//        $token->save();
        return response()->json([
            'status'=>true,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user,
            'message' => 'Login Successfull',
            'status_code' => 200
//            'expires_at' => Carbon::parse(
//                $tokenResult->token->expires_at
//            )->toDateTimeString()
        ]);
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
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
    
    /*
     * Update lat, lng and fetching users within 10miles radius.
     */
    public function userLatLng(Request $request)
    {
        $user = $request->user();
        $user->lat = $request->lat;
        $user->lng = $request->lng;
        if($user->save()){
            $users = DB::select(DB::raw("SELECT
  *, (
    3959 * acos (
      cos ( radians($request->lat) )
      * cos( radians( lat ) )
      * cos( radians( lng ) - radians($request->lng) )
      + sin ( radians($request->lat) )
      * sin( radians( lat ) )
    )
  ) AS distance
FROM users
HAVING distance <= 10
ORDER BY distance
LIMIT 0 , 20;"));
            return response()->json(["success"=>true, "data"=>$users]);
        }
    }
}