<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

Route::post('register', function(Request $request) {
    $user = new User();
    $user->name = $request->name;
    $user->email = $request->email;
    $user->password = Hash::make($request->password);
    $user->role = $request->role;
    $user->token = Illuminate\Support\Str::random(10);
    $user->save();
    return response()->json([
        'code'=>1,
        'message' => "Inscription réussi",
        'role' => $user->role,
        'id' => str($user->id),
        'token' => str($user->token),
    ]);
});

Route::post('login', function(Request $request) {
    $user = User::where('email', $request->email)->first();
    if($user != null){
        if(Hash::check($request->password, $user->password)){
            return response()->json([
                'code'=>1,
                'message' => "Connexion réussie",
                'role' => $user->role,
                'id' => str($user->id),
                'token' => str($user->token),
            ]);
        }
        else {
            return response()->json([
                'code'=>2,
                'message' => "Mot de passe incorrect"
            ]);
        }
    } else {
        return response()->json([
            'code'=>0,
            'message' => "Vous n'avez pas de compte"
        ]);
    }
});

Route::post('userInformation', function(Request $request) {
    $user = User::where('token', $request->token)->first();
    if($user != null){
        return response()->json([
            'code'=>1,
            'role'=>$user->role,
            'userName'=>$user->name,
            'user'=> $user,
            'cdf'=>str($user->cdf),
            'usd'=>str($user->usd),
            'token'=>$user->token
        ]);
    } else {
        return response()->json([
            'code'=>0
        ]);
    }
});

Route::post('userRecharge', function(Request $request) {
    $user = User::where('token', $request->token)->first();
    if($user != null){
        if($request->cdf != null){
            $user->cdf = $user->cdf + $request->cdf;
        } else {
            $user->usd = $user->usd + $request->usd;
        }
        $user->save();
        return response()->json([
            'code'=>1,
            'role'=>$user->role,
            'userName'=>$user->name,
            'user'=> $user,
            'cdf'=>str($user->cdf),
            'usd'=>str($user->usd),
            'token'=>$user->token,
            'message'=>"Votre compte a été rechargé."
        ]);
    } else {
        return response()->json([
            'code'=>0,
            'message'=>"Il y a eu une erreur, veuillez recommener."
        ]);
    }
});

Route::post('userPaiement', function(Request $request) {
    $user = User::where('token', $request->token)->first();
    $consoUser = User::where('token', $request->consoToken)->first();
    if($user != null && $consoUser != null){
        if($request->cdf != null){
            if($consoUser->cdf < $request->cdf){
                return response()->json([
                    'code'=>0,
                    'message'=>"Le soldle du client est inférieur."
                ]);
            } else {
                $user->cdf = $user->cdf + $request->cdf;
                $consoUser->cdf = $consoUser->cdf - $request->cdf;
            }
        } else {
            if($consoUser->usd < $request->usd){
                return response()->json([
                    'code'=>0,
                    'message'=>"Le soldle du client est inférieur."
                ]);
            } else {
                $user->usd = $user->usd + $request->usd;
                $consoUser->usd = $consoUser->usd - $request->usd;
            }
        }
        $user->save();
        $consoUser->save();
        return response()->json([
            'code'=>1,
            'role'=>$user->role,
            'userName'=>$user->name,
            'user'=> $user,
            'cdf'=>str($user->cdf),
            'usd'=>str($user->usd),
            'token'=>$user->token,
            'message'=>"Votre compte a été rechargé."
        ]);
    } else {
        return response()->json([
            'code'=>0,
            'message'=>"Il y a eu une erreur, veuillez recommener."
        ]);
    }
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
