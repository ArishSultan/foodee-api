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


        $username = $request->username;
        $email = $request->email;
        $phone = $request->phone;
        $password = bcrypt($request->password);

        $existingUser = User::where('email',$email)->first();

        if($existingUser) {
            return response()->json(['message' => 'Email already exists.', 'status' => false, 'access_token' => null], 200);
        }else {

            $user = new User([
                'username' => $username,
                'email' => $email,
                'phone' => $phone,
                'password' => $password
            ]);

            if(!$user) {

                throw new HttpException(500);

            }else {
                if($user->save()){
                    $tokenResult = $user->createToken('Foodee');
                    $token = $tokenResult->accessToken;
                    return response()->json([
                        'status'=>true,
                        'access_token' => $tokenResult->accessToken,
                        'message' => 'Your account has been created successfully',
                        'user' => $user
                    ], 201);
                }
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
        $existingUser = User::where('email',$email)->first();

        if($existingUser) {

            $credentials = request(['email', 'password']);
            if (!Auth::attempt($credentials))
                return response()->json([
                    'status' => false,
                    'message' => 'Incorrect username or password!',
                    'token' => null
                ], 201);
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
    public function user(Request $request)
    {
        $user = $request->user();
        $user['profile'] = $user->profile;
        return response()->json($user);
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
  id, username, email, phone, lat, lng, user_id, avatar, cover, (
    3959 * acos (
      cos ( radians($request->lat) )
      * cos( radians( lat ) )
      * cos( radians( lng ) - radians($request->lng) )
      + sin ( radians($request->lat) )
      * sin( radians( lat ) )
    )
  ) AS distance
FROM users right join profiles on users.id = profiles.user_id
HAVING distance <= 10
ORDER BY distance
LIMIT 0 , 20;"));


            return response()->json(["success"=>true, "data"=>$users]);

        }
    }
}