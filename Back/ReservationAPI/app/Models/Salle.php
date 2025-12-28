<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $table = 'salle';
    public $timestamps = false;

    public function equipements()
    {
        return $this->belongsToMany(Equipement::class, 'equipementsalle', 'idSalle', 'idEquipement');
    }
}
