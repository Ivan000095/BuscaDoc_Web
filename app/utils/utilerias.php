<?php

namespace App\Utils;
use Carbon\Carbon;

class Formato
{
    // public static function aTitleCase($string)
    // {
    //     return lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $string))));
    // }

    public static function dinero($monto)
    {
        return '$' . number_format($monto, 2, '.', ',');
    }

    public static function generarFolio($prefijo)
    {
        return strtoupper($prefijo) . '-' . time() . '-' . rand(100, 999);
    }

    public static function fechaFormato($fecha) {
        // $meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        $fechaObj = Carbon::parse($fecha);
        $fechaformato = $fechaObj->isoFormat('D [de] MMMM [del] YYYY');
        return $fechaformato;
    }
}