<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\EquipementSalle;
use Illuminate\Http\Request;
use App\Models\Salle;
use App\Models\Reservation;

class SalleController extends Controller
{
    public function list()
    {
        $Salles = Salle::with('equipements')->get();
        return response()->json($Salles);
    }

    public function add(Request $request)
    {
        if (empty($request->nom)) {
            return response()->json([
                'message' => 'Veuillez saisir un nom pour ajouter une salle'
            ], 400);
        } elseif (empty($request->capacite)) {
            return response()->json([
                'message' => 'Veuillez saisir une capacité maximale à votre salle'
            ], 400);
        } else {
            $Salle = new Salle();
            $Salle->nom = $request->nom;
            $Salle->description = $request->description;
            $Salle->capacite = $request->capacite;
            $Salle->save();
            return response()->json([
                'message' => 'OK',
                'id' => $Salle->id,
                'nom' => $Salle->nom,
                'description' => $Salle->description,
                'capacite' => $Salle->capacite
            ], 200);
        }
    }

    public function getSallesDispo(Request $request)
    {
        $Salles = Salle::all();
        $SallesDispo = [];
        $Reservations = Reservation::all();
        $date = $request->date;
        if (empty($request->date)) {
            $date = date("Y-m-d");
        }

        foreach ($Salles as $Salle) {
            $dispo = true;
            foreach ($Reservations as $Reservation) {
                if ($Salle->id == $Reservation->idSalle) {
                    if ($date > $Reservation->dateDebut && $date < $Reservation->dateFin) {
                        $dispo = false;
                    }
                }
            }
            if ($dispo) {
                array_push($SallesDispo, $Salle);
            }
        }
        return response()->json($SallesDispo);
    }
}
