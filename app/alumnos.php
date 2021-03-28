<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class alumnos extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idAlumno', 'codigo', 'nombre', 'departamento', 'municipio','sexo'
    ];

}