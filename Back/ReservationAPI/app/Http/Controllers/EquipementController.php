<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Equipement;

class EquipementController extends Controller
{
    public function list()
    {
        $Equipements = Equipement::all();
        return response()->json($Equipements);
    }

    public function add(Request $request)
    {
        if (empty($request->nom)) {
            return response()->json([
                'message' => 'Veuillez saisir un nom pour ajouter un équipement'
            ], 400);
        }
        $Equipement = new Equipement();
        $Equipement->nom = $request->nom;
        $Equipement->description = $request->description;
        $Equipement->save();
        return response()->json([
            'message' => 'OK',
            'id' => $Equipement->id,
            'nom' => $Equipement->nom,
            'description' => $Equipement->description
        ], 200);
    }



}
