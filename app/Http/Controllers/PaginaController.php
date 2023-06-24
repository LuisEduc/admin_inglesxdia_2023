<?php

namespace App\Http\Controllers;

use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaginaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $paginas = Pagina::orderByDesc('orden')->get();
        return view('paginas.index', compact('paginas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $paginas = Pagina::all();
        return view('paginas.crear', compact('paginas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $paginas = $request->all();
        Pagina::create($paginas);
        return redirect()->route('paginas.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pagina $pagina)
    {
        return view('paginas.editar', compact('pagina'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pagina $pagina)
    {
        $page = $request->all();
        $pagina->update($page);
        return redirect()->route('paginas.index');
    }

    public function updateOrden(Request $request)
    {

        $paginas = Pagina::all();

        foreach ($paginas as $pagina) {
            foreach ($request->orden as $orden) {
                if ($orden['id'] == $pagina->id) {
                    $pagina->update(['orden' => $orden['posicion']]);
                }
            }
        }
        
        return response('Update Successfully.', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pagina $pagina)
    {
        $pagina->delete();
        return redirect()->route('paginas.index'); 
    }

    // API
    public function getPaginas()
    {
        $paginas = DB::table('paginas')
        ->select('paginas.id',  'paginas.slug', 'paginas.titulo_seo', 'paginas.descripcion')
        ->where('paginas.estado', 'publica')
        ->where('slug', '!=', 'normal')
        ->where('slug', '!=', 'relacionada')
        ->orderByDesc('paginas.orden')
        ->get();

        $json['paginas'] = $paginas;
        return $json;
    }
}
