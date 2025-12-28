<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SalleController;
use App\Http\Controllers\EquipementController;
use App\Http\Controllers\EquipementSalleController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;

//Endpoints de listings
Route::post('/getSalles', [SalleController::class, 'list']);
Route::post('/getSallesDispo', [SalleController::class, 'getSallesDispo']);
Route::post('/getReservations', [ReservationController::class, 'list']);
Route::post('/getReservationsByUser', [ReservationController::class, 'getReservationsByUser']);
Route::post('/getEquipements', [EquipementController::class, 'list']);
Route::post('/getEquipementSalles', [EquipementSalleController::class, 'list']);
Route::post('/getUsers', [UserController::class, 'list']);
Route::post('/login', [UserController::class, 'login']);

//Endpoints d'ajout
Route::post('/addSalle', [SalleController::class, 'add']);
Route::post('/addEquipement', [EquipementController::class, 'add']);
Route::post('/addEquipementSalle', [EquipementSalleController::class, 'add']);
Route::post('/addReservation', [ReservationController::class, 'add']);
Route::post('/addUser', [UserController::class, 'add']);

//Endpoints de suppression
Route::post('/deleteReservation', [ReservationController::class, 'delete']);

