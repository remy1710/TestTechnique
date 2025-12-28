<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function list()
    {
        $Users = User::all();
        foreach ($Users as $User) {
            $User->pass = '********';
        }
        return response()->json($Users);
    }

    public function add(Request $request)
    {
        if (empty($request->nom)) {
            return response()->json([
                'message' => 'Veuillez saisir un nom pour ajouter un utilisateur'
            ], 400);
        } elseif (empty($request->email)) {
            return response()->json([
                'message' => 'Veuillez saisir un email pour ajouter un utilisateur'
            ], 400);
        } elseif (empty($request->pass)) {
            return response()->json([
                'message' => 'Veuillez saisir un mot de passe pour ajouter un utilisateur'
            ], 400);
        } else {
            $UserThere = User::where('email', $request->email)->first();
            if ($UserThere) {
                return response()->json([
                    'message' => 'Utilisateur deja existant'
                ], 400);
            } else {
                $User = new User();
                $User->nom = $request->nom;
                $User->email = $request->email;
                $User->pass = $request->pass;
                $User->save();
                return response()->json([
                    'message' => 'OK',
                    'id' => $User->id,
                    'nom' => $User->nom,
                    'email' => $User->email
                ], 200);
            }
        }
    }
    public function login(Request $request)
    {
        if (empty($request->email) || empty($request->pass)) {
            return response()->json([
                'message' => 'Veuillez saisir un email et un mot de passe'
            ], 400);
        }

        $User = User::where('email', $request->email)
            ->where('pass', $request->pass)
            ->first();

        if (!$User) {
            return response()->json([
                'message' => 'Email ou mot de passe incorrect'
            ], 401);
        }

        return response()->json([
            'message' => 'Connexion réussie',
            'user' => [
                'id' => $User->id,
                'nom' => $User->nom,
                'email' => $User->email
            ]
        ], 200);
    }
}
