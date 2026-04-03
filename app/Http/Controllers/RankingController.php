<?php

namespace App\Http\Controllers;
use App\Models\Doctor;
use App\Models\Farmacia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RankingController extends Controller
{
    //
    public function index(){
        $doctores = Doctor::with('user', 'especialidades')
        ->select('doctors.*')
        ->join('users','doctors.user_id','=','users.id')
        ->leftJoin('comentarios',"users.id",'=','comentarios.id_destinatario')
        ->selectRaw('AVG(comentarios.calificacion) as promedio_estrellas')
        ->groupBy('doctors.id')
        ->orderby('promedio_estrellas')
        ->take(3)   
        ->get();

        $farmacias = Farmacia::with('user')
        ->select('farmacias.*')
        ->join('users','farmacias.user_id','=','users.id')
        ->leftJoin('comentarios',"users.id",'=','comentarios.id_destinatario')
        ->selectRaw('AVG(comentarios.calificacion) as promedio_estrellas')
        ->groupBy('farmacias.id')
        ->orderByDesc('promedio_estrellas')
        ->take(3)
        ->get();
        
        return view('top5', compact('doctores', 'farmacias'));

    }
}
