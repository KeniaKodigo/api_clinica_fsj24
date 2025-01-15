<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Tag(name="Users", description="API for authentication management")
 */

class AuthenticationController extends Controller
{
    //metodo para iniciar sesion
    /**
     * @OA\Post(
     *     path="/api/v1/login",
     *     summary="log in to the system",
     *     tags={"Users"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Inicio de sesión exitoso",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="string", example="user@example.com"),
     *             @OA\Property(property="token", type="string", example="eyJ0eXAiOiJK...")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="You are not authorized")
     *         )
     *     )
     * )
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        //validando si se rompe las reglas de entrada de datos
        if($validator->fails()){
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        //validamos si el usuario existe en la bd
        //select * from users where email = ? and password = ?
        $user = User::where('email',$email)->where('password','=',$password)->first();
        //{}
        //User::where(['email',$email],['password',$password])->first();

        if($user){
            //generar un token
            $token = $user->createToken('api-token')->plainTextToken;
            return response()->json([
                "user" => $email,
                "token" => $token
            ], 200);
        }else{
            return response()->json(['message' => 'You are not authorized'], 401);
        }
    }

    //metodo para cerrar sesion
    /**
     * @OA\Post(
     *     path="/api/v1/logout",
     *     summary="Logs out the authenticated user",
     *     tags={"Users"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Sesión cerrada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="mensaje", type="string", example="Se ha cerrado la sesion")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout(Request $request){
        //eliminamos el ultimo token del usuario
        $request->user()->currentAccessToken()->delete();

        // $request->user()->tokens()->delete()
        return response()->json(['mensaje' => 'Se ha cerrado la sesion']);
    }
}
