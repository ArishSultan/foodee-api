<?php

namespace App\Http\Controllers;

use App\User;
use App\Profile;
use Illuminate\Http\Request;
use App\Mail\ConfirmationEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    function sendMail() {
        $dataEmail = [
            "uid" => 123,
            "email" => 'email',
            "username" => 'user'
        ];

        Mail::to('arishsultan104@gmail.com')->send(new ConfirmationEmail($dataEmail));
    }

    /**
     * Create user0xaa0000
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function signup(Request $request): ?JsonResponse
    {
        $email = $request->get('email');
        $phone = $request->get('phone');
        $timezone = $request->get('timezone');
        $username = $request->get('username');
        $device_token = $request->get('device_token');
        $password = bcrypt($request->get('password'));

        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser) {
            return response()->json([
                'status' => false,
                'access_token' => null,
                'message' => 'Email already exists.'
            ], 200);
        }


        $user = new User;

        $user->email = $email;
        $user->phone = $phone;
        $user->email_confirm = 0;
        $user->username = $username;
        $user->password = $password;
        $user->timezone = $timezone;
        $user->device_token = $device_token;

        if ($user->save()) {
            $profile = new Profile;
            $profile->user_id = $user->id;

            if ($profile->save()) {
                $tokenResult = $user->createToken('Foodee');

                $dataEmail = [
                    "uid" => $user->id,
                    "email" => $user->email,
                    "username" => $user->username
                ];

                Mail::to($user->email)->send(new ConfirmationEmail($dataEmail));

                return response()->json([
                    'status' => true,
                    'user' => $user,
                    'access_token' => $tokenResult->accessToken,
                    'message' => 'Your account has been created successfully'
                ], 201);
            }
        }

        return response()->json(['message' => 'Something went wrong', 'status' => false, 'access_token' => null]);
    }


    public function confirm($email, $id)
    {
        $user = User::query()
            ->where('id', $id)
            ->where('email', $email)
            ->where('email_confirm', 0)
            ->first();

        if (isset($user)) {
            $user->email_confirm = true;
            if ($user->save()) {
                return view('emails.user.confirmed');
            }
        }

        return null;
    }

    /**
     * Login user and create token
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): ?JsonResponse
    {
        $email = $request->get('email');
        $timezone = $request->get('timezone');
        $existingUser = User::query()->where('email', $email)->first();

        if ($existingUser) {
            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'token' => null,
                    'status' => false,
                    'message' => 'Incorrect password!'
                ], 201);
            }

            User::query()->where('email', $email)->update([
                'device_token' => $request->get('device_token'),
                'timezone' => $timezone
            ]);

            $user = $request->user();
            $tokenResult = $user->createToken('Foodee');
            $token = $tokenResult->accessToken;

            return response()->json([
                'user' => $user,
                'status' => true,
                'token_type' => 'Bearer',
                'access_token' => $token,
                'message' => 'Login Successful',
            ]);
        }

        return response()->json(['message' => 'No User Exists']);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): ?JsonResponse
    {
        $request->user()->token()->revoke();
        return response()->json(['message' => 'Successfully logged out']);
    }


    /**
     * Get the authenticated User
     *
     * @param $id
     * @return JsonResponse
     */
    public function user($id): ?JsonResponse
    {
        $user = User::query()->where('id', $id)->first();
        if (isset($user->profile->foods)) {
            $user['profile'] = $user->profile->foods;
        }

        return response()->json($user);
    }

    public function updateToken(Request $request): ?JsonResponse
    {
        $user = $request->user();
        $user->device_token = $request->get('device_token');

        if ($user->save()) {
            return response()->json(['success' => true, 'message' => 'FCM token has been updated successfully']);
        }

        return null;
    }

    public function nearby(Request $request): array
    {
        $user = $request->user();
        $user_lat=$user->lat;
        $user_lng=$user->lng;
        $user_result = null;
        $food = $request->query('food') . "%";
        $type = $request->query('type');
        if ($food && $type) {
            $user_result = DB::select('SELECT distinct (ST_Distance_Sphere(POINT(u.lng, u.lat), POINT(:ulng, :ulat)) / 1000) as distance, p2.is_age_private, u.*, p2.contribution
FROM food_categories as fc
    inner join food_profile as p on fc.id = p.food_id
    inner join profiles p2 on p.profile_id = p2.id
    inner join users u on p2.user_id = u.id
WHERE (NOT u.id = :id) fc.name LIKE :foodname AND p2.contribution = :type;', [
                "foodname" => $food,
                "type" => $type,
                "ulat" => $user_lat,
                "ulng" => $user_lng,
                "id" => $user->id
            ]);
        } else if ($food) {
            $user_result = DB::select('SELECT distinct (ST_Distance_Sphere(POINT(u.lng, u.lat), POINT(:ulng, :ulat)) / 1000) as distance, p2.is_age_private, u.*, p2.contribution FROM food_categories as fc
    inner join food_profile as p on fc.id = p.food_id
    inner join profiles p2 on p.profile_id = p2.id
    inner join users u on p2.user_id = u.id
WHERE (NOT u.id = :id) AND fc.name LIKE :foodname ;', [
                "foodname" => $food,
                "ulat" => $user_lat,
                "ulng"=> $user_lng,
                "id" => $user->id
            ]);
        } else if ($type) {
            $user_result = DB::select('SELECT distinct (ST_Distance_Sphere(POINT(u.lng, u.lat), POINT(:ulng, :ulat)) / 1000) as distance, a.is_age_private, u.*, a.contribution FROM profiles as a
    inner join users u on a.user_id = u.id
WHERE (NOT u.id = :id) AND a.contribution = :type;', [
                "type" => $type,
                "ulat" => $user_lat,
                "ulng"=> $user_lng,
                "id" => $user->id
            ]);
        }
        return $user_result;

//        return $user_result;
//        $result = DB::select('select distinct profile_id from food_categories as a inner join food_profile as b on a.id=b.food_id where a.id=2');

//        return $result;
//        $user = $request->user();
//
//        $type = $request->query('type');
//        $name = $request->query('name');
//
//        $query = "SELECT users.*, profiles.contribution, profiles.is_age_ ST_Distance_Sphere(POINT(users.lat, users.lng), POINT(:lat, :lng)) as distance ";
//        $query .= "FROM users join profiles on profiles.user_id = users.id WHERE NOT users.id = ".$user->id;
//
//        if ($type || $name) {
//            $query .= " AND ";
//        }
//
//        if ($type) {
//            $query .= "profiles.contribution = '".$type."'";
//        }
//
//        if ($type && $name) {
//            $query .= " AND ";
//        }
//
//        if ($name) {
//            $query .= "users.username LIKE LOWER('".$name."%') ";
//        }
//
//        $query .= " ORDER BY distance"; //, );
//
//        $users = DB::select($query, ["lat" => $user->lat, "lng" => $user->lng]);
//
//        foreach($users as $user) {
//            $user->is_age_private = $user->is_age_private == 1;
//            $user->foods = Profile::query()->where('user_id', $user->user_id)->select('id', 'user_id')->first()->foods;
//            $user->password = $query;
//        }
//
//        return $users;
    }

    /*
     * Update lat, lng and fetching users within 10miles radius.
     */
    public function userLatLng(Request $request)
    {
        $user = $request->user();

        $user->lat = $request->get('lat');
        $user->lng = $request->get('lng');

        $user->save();

        return $user;
    }

    /*
     * Search users for tagging
     */
    public function findPeoples(Request $request)
    {
        $username = $request->query('username');
        return User::with('profile')->where('username', 'LIKE', "%{$username}%")->get();
    }
}
