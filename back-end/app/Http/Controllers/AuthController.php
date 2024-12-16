<?php
  
namespace App\Http\Controllers;
  
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
  
  
class AuthController extends Controller
{


    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register() {
        
        $user = auth()->user();
        if (!$user->hasRole('admin')) return response()->json('Unauthorized', 422);

        $validator = Validator::make(request()->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:8',
        ]);
  
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
  
        $user = new User;
        $user->name = request()->name;
        $user->email = request()->email;
        $user->status = 'Inactive';
        $user->cpf =  request()->cpf;
        $user->password = bcrypt(request()->password);
        $user->save();
  
        return response()->json($user, 201);
    }
  
  
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
  
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
  
        return $this->respondWithToken($token);
    }
  
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }
  
    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
  
        return response()->json(['message' => 'Successfully logged out']);
    }
  
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    public function user()
    {
        return response()->json(auth()->user());
    }

    public function index()
    {
        $users = User::all();
        return response()->json($users, 200);
    }

    public function activate($id)
    {
        $user = User::find($id);

        if(is_null($user))
            return response()->json('User not found', 404);

        if($user->status == 'Active')
        $user->update(['status' => 'Inactive']);
        else
        $user->update(['status' => 'Active']);
        
        return response()->json($user, 200);
    }

    public function update(Request $request)
    {
        $user = User::find($request->id);

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'confirmed|min:8',
        ]);
  
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
  
        $user->name = $request->name;
        $user->email = $request->email;
        $user->cpf = $request->cpf;
        if($request->password)
            $user->password = bcrypt($request->password);
        $user->save();
  
        return response()->json($user, 200);
    }



  
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }


}