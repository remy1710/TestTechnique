<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Reservation;
use App\Models\Salle;
use App\Models\User;

class ReservationController extends Controller
{
    public function list()
    {
        $Reservations = Reservation::all();
        return response()->json($Reservations);
    }

    public function add(Request $request)
    {
        if (empty($request->dateDebut)) {
            return response()->json([
                'message' => 'Veuillez saisir une date de début pour ajouter une réservation'
            ], 400);
        } elseif (empty($request->dateFin)) {
            return response()->json([
                'message' => 'Veuillez saisir une date de fin pour ajouter une réservation'
            ], 400);
        } elseif (empty($request->idSalle)) {
            return response()->json([
                'message' => 'Veuillez saisir un id de salle pour ajouter une réservation'
            ], 400);
        } elseif (empty($request->idUser)) {
            return response()->json([
                'message' => 'Veuillez saisir un id d\'utilisateur pour ajouter une réservation'
            ], 400);
        } elseif (empty($request->nbPersonnes)) {
            return response()->json([
                'message' => 'Veuillez saisir le nombre de personnes pour ajouter une réservation'
            ], 400);
        } else {
            //Contrôle existance salle et utilisateur
            $Salle = Salle::find($request->idSalle);
            $User = User::find($request->idUser);
            if (!$Salle || !$User) {
                return response()->json([
                    'message' => 'Salle ou utilisateur inconnus'
                ], 404);
            }

            //Contrôle cohérence de saisies
            if ($request->dateDebut > $request->dateFin) {
                return response()->json([
                    'message' => 'La date de début est supérieure à la date de fin'
                ], 400);
            }
            if ($request->dateDebut < date("Y-m-d H:i:s") || $request->dateFin < date("Y-m-d H:i:s")) {
                return response()->json([
                    'message' => 'La date de début est inférieure à la date actuelle ou la date de fin est inférieure à la date actuelle'
                ], 400);
            }
            if ($request->nbPersonnes > $Salle->capacite) {
                return response()->json([
                    'message' => 'Le nombre de personnes est supérieur à la capacité de la salle'
                ], 400);
            }

            //Contrôle disponibilité de la salle
            $Reservations = Reservation::where('idSalle', $request->idSalle)->get();
            foreach ($Reservations as $Reservation) {
                if ($request->dateDebut < $Reservation->dateFin && $request->dateFin > $Reservation->dateDebut) {
                    return response()->json([
                        'message' => 'La salle est déjà réservée pour cette période'
                    ], 400);
                }
            }

            $Reservation = new Reservation();
            $Reservation->dateDebut = $request->dateDebut;
            $Reservation->dateFin = $request->dateFin;
            $Reservation->idSalle = $request->idSalle;
            $Reservation->idUser = $request->idUser;
            $Reservation->save();
            return response()->json([
                'message' => 'OK',
                'id' => $Reservation->id,
                'dateDebut' => $Reservation->dateDebut,
                'dateFin' => $Reservation->dateFin,
                'idSalle' => $Reservation->idSalle,
                'idUser' => $Reservation->idUser
            ], 200);
        }
    }

    public function getReservationsByUser(Request $request)
    {
        $User = User::where('id', $request->idUser)->first();
        if (!$User) {
            return response()->json([
                'message' => 'Utilisateur inconnu'
            ], 404);
        }
        $Reservations = Reservation::where('idUser', $request->idUser)->get();
        return response()->json($Reservations);
    }

    public function delete(Request $request)
    {
        $Reservation = Reservation::find($request->id);
        if (!$Reservation) {
            return response()->json([
                'message' => 'Réservation inconnue'
            ], 404);
        }
        $Reservation->delete();
        return response()->json([
            'message' => 'OK'
        ], 200);
    }
}
