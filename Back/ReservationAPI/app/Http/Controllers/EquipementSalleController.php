<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EquipementSalle;
use App\Models\Salle;
use App\Models\Equipement;

class EquipementSalleController extends Controller
{
    public function list()
    {
        $EquipementSalles = EquipementSalle::all();
        return response()->json($EquipementSalles);
    }

    public function add(Request $request)
    {
        if (empty($request->idEquipement) || empty($request->idSalle)) {
            return response()->json([
                'message' => 'Veuillez saisir un id d\'equipement et un id de salle'
            ], 400);
        } else {
            //Contrôle existance salle et equipement
            $Salle = Salle::find($request->idSalle);
            $Equipement = Equipement::find($request->idEquipement);
            if (!$Salle || !$Equipement) {
                return response()->json([
                    'message' => 'Salle ou equipement inconnus'
                ], 404);
            }

            $EquipementSalle = new EquipementSalle();
            $EquipementSalle->idEquipement = $request->idEquipement;
            $EquipementSalle->idSalle = $request->idSalle;
            $EquipementSalle->save();
            return response()->json([
                'message' => 'OK',
                'id' => $EquipementSalle->id,
                'idEquipement' => $EquipementSalle->idEquipement,
                'idSalle' => $EquipementSalle->idSalle
            ], 200);
        }
    }
}
