<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Pagina;
use App\Models\Lesson;
use App\Models\Lessonimage;
use App\Models\Pregunta;
use App\Models\Tipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Barryvdh\DomPDF\Facade\Pdf;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $lessons = Lesson::paginate(5); //paginacion sin usar dataTables
        $lessons = Lesson::orderByDesc('orden')->get();
        return view('lessons.index', compact('lessons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $lecciones = Lesson::all();
        $categorias = Categoria::all();
        $paginas = Pagina::all();
        $tipos = Tipo::all();
        return view('lessons.crear', compact('categorias', 'tipos', 'lecciones', 'paginas'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'imagen.*' => 'image|mimes:jpeg,jpg,png,svg,webp,JPEG,JPG,PNG,SVG,WEBP|max:5000',
            'audio' => 'file|mimes:audio/mpeg,mpga,mp3,wav,aac,ogg,MPEG,MPGA,MP3,WAV,AAC|max:5000'
        ]);

        $lesson = $request->except('imagen');

        if ($audio = $request->file('audio')) {
            $rutaGuardarAud = 'audio/';
            list($sec, $usec) = explode('.', microtime(true));
            $audioLesson = date('YmdHis', $sec) . $usec . "." . $audio->getClientOriginalExtension();
            $audio->move($rutaGuardarAud, $audioLesson);
            $lesson['audio'] = "$audioLesson";
        }

        $lec_creada = Lesson::create($lesson);

        if ($request->file('imagen')) {

            $images = $request->imagen;
            $rutaGuardarImg = 'imagen/';

            foreach ($images as $key => $image_value) {

                list($sec, $usec) = explode('.', microtime(true));
                $imagenLesson = date('YmdHis', $sec) . $usec . "." . $image_value->getClientOriginalExtension();
                $image_value->move($rutaGuardarImg, $imagenLesson);

                $data[] = $lec_creada->lessonimages()->create([
                    'id_lesson' => $lec_creada->id,
                    'id_imagen' => $key,
                    'imagen' => "$imagenLesson",
                ]);
            }
        }

        $id_lesson = $lec_creada->id;
        $preguntas = $lec_creada->preguntas;
        return view('lessons.crearpregunta', compact('id_lesson', 'preguntas'));
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
    public function edit(Lesson $lesson)
    {
        $categorias = Categoria::all();
        $paginas = Pagina::all();
        $tipos = Tipo::all();
        $preguntas = Pregunta::where('id_lesson', $lesson->id)->get();
        $lessonimage = Lessonimage::where('id_lesson', $lesson->id)
            ->orderBy('id_imagen')
            ->get();
        return view('lessons.editar', compact('lesson', 'categorias', 'tipos', 'paginas', 'preguntas', 'lessonimage'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Lesson $lesson)
    {

        $request->validate([
            'imagen.*' => 'image|mimes:jpeg,jpg,png,svg,webp,JPEG,JPG,PNG,SVG,WEBP|max:5000',
            'audio' => 'file|mimes:audio/mpeg,mpga,mp3,wav,aac,ogg,MPEG,MPGA,MP3,WAV,AAC|max:5000'
        ]);

        $leccion = $request
            ->except([
                'id_pregunta',
                'id_lesson',
                'pregunta',
                'opcion1',
                'opcion2',
                'opcion3',
                'respuesta',
                'imagen'
            ]);

        if ($audio = $request->file('audio')) {
            $oldAud = 'audio/' . $lesson->audio;
            if (File::exists($oldAud)) {
                File::delete($oldAud);
            }
            $rutaGuardarAud = 'audio/';
            list($sec, $usec) = explode('.', microtime(true));
            $audioLeccion = date('YmdHis', $sec) . $usec . "." . $audio->getClientOriginalExtension();
            $audio->move($rutaGuardarAud, $audioLeccion);
            $leccion['audio'] = "$audioLeccion";
        } else {
            unset($leccion['audio']);
        }

        $lesson->update($leccion);

        if ($request->id_pregunta) {

            $id_preguntas = $request->id_pregunta;
            $preguntas = $request->pregunta;
            $opciones_1 = $request->opcion1;
            $opciones_2 = $request->opcion2;
            $opciones_3 = $request->opcion3;
            $respuestas = $request->respuesta;
            foreach ($id_preguntas as $key => $id_pregunta_value) {
                $data = [
                    'pregunta' => $preguntas[$key],
                    'opcion1' => $opciones_1[$key],
                    'opcion2' => $opciones_2[$key],
                    'opcion3' => $opciones_3[$key],
                    'respuesta' => $respuestas[$key]
                ];

                $lesson->preguntas()->where('id_pregunta', $id_pregunta_value)->update($data);
            }
        }

        if ($request->imagen) {

            $images = $request->imagen;
            $rutaGuardarImg = 'imagen/';
            $imagenes = Lessonimage::where('id_lesson', $lesson->id)->get()->toArray();
            $count_imagenes = count($imagenes);

            foreach ($images as $key => $image_value) {

                list($sec, $usec) = explode('.', microtime(true));
                $imagenLesson = date('YmdHis', $sec) . $usec . "." . $image_value->getClientOriginalExtension();
                $image_value->move($rutaGuardarImg, $imagenLesson);

                $data[] = $lesson->lessonimages()->create([
                    'id_lesson' => $lesson->id,
                    'id_imagen' => $count_imagenes + $key,
                    'imagen' => "$imagenLesson",
                ]);
            }
        }

        return redirect()->route('lessons.index');
    }

    public function updateOrden(Request $request)
    {

        $lecs = Lesson::all();

        foreach ($lecs as $lec) {
            foreach ($request->orden as $orden) {
                if ($orden['id'] == $lec->id) {
                    $lec->update(['orden' => $orden['posicion']]);
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
    public function destroy(Lesson $lesson)
    {
        $lessonimages = Lessonimage::where('id_lesson', $lesson->id)->get()->toArray();
        foreach ($lessonimages as $lessonimage) {
            $img = $lessonimage['imagen'];
            File::delete("imagen/$img");
        }
        $lesson->delete();
        File::delete("audio/$lesson->audio");
        return redirect()->route('lessons.index');
    }


    // API

    public function getLecciones()
    {
        $lecciones = DB::table('lessons')
            ->select('lessons.id', 'lessons.orden', 'lessons.slug', 'lessons.titulo', 'lessons.descripcion', 'lessonimages.imagen', 'lessons.audio', 'categorias.slug as slug_cat')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->join('lessonimages', 'lessonimages.id_lesson', '=', 'lessons.id')
            ->where('lessons.estado', 'publica')
            ->where('lessonimages.id_imagen', '0')
            ->orderByDesc('lessons.orden')
            ->get();

        return $lecciones;
    }

    public function getLecturas()
    {
        $lecturas = DB::table('lessons')
            ->select('lessons.id', 'lessons.orden', 'lessons.slug', 'categorias.slug as slug_cat')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('lessons.estado', 'publica')
            ->where('categorias.slug', 'lc')
            ->orWhere('categorias.slug', 'lb')
            ->orWhere('categorias.slug', 'fm')
            ->orderByDesc('lessons.orden')
            ->get();

        return $lecturas;
    }

    public function getLeccionesCat($id_categoria)
    {

        $lecciones = DB::table('lessons')
            ->select('lessons.id', 'lessons.orden', 'lessons.slug', 'lessons.titulo', 'lessons.descripcion', 'lessonimages.imagen', 'lessons.audio', 'categorias.slug')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->join('lessonimages', 'lessonimages.id_lesson', '=', 'lessons.id')
            ->where('id_categoria', $id_categoria)
            ->where('lessonimages.id_imagen', '0')
            ->orderByDesc('lessons.orden')
            ->get();
        $json['lecciones'] = $lecciones;
        return $json;
    }

    public function getLeccionesCatSlug($slug_cat, $slug)
    {
        $preguntas = DB::table('lessons')
            ->select('preguntas.id_pregunta', 'preguntas.pregunta', 'preguntas.opcion1', 'preguntas.opcion2', 'preguntas.opcion3', 'preguntas.respuesta')
            ->join('preguntas', 'preguntas.id_lesson', '=', 'lessons.id')
            ->where('lessons.slug', $slug)
            ->get();

        $lecciones = DB::table('lessons')
            ->select('lessons.id', 'lessons.slug', 'lessons.titulo_seo', 'lessons.titulo', 'lessons.descripcion', 'lessons.audio', 'categorias.slug as slug_cat')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('categorias.slug', $slug_cat)
            ->where('lessons.slug', $slug)
            ->get();

        $imagenes = DB::table('lessons')
            ->select('lessonimages.imagen')
            ->join('lessonimages', 'lessonimages.id_lesson', '=', 'lessons.id')
            ->where('lessons.slug', $slug)
            ->orderBy('lessonimages.id_imagen')
            ->get();

        $json['leccion'] = $lecciones;
        $json['preguntas'] = $preguntas;
        $json['imagenes'] = $imagenes;
        return $json;
    }

    public function getLeccionContenido($slug_cat, $slug)
    {
        $contenido = DB::table('lessons')
            ->select('contenido')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('categorias.slug', $slug_cat)
            ->where('lessons.slug', $slug)
            ->get();

        $json['contenido'] = $contenido;
        return $json;
    }

    public function getAudio($audio)
    {
        return response()->file(public_path("audio/$audio"));
    }

    public function getInicio()
    {

        $tipos = Tipo::all()
            ->where('estado', 'publica')
            ->where('slug', '!=', 'normal')
            ->sortByDesc('orden')->values()->all();

        $lecciones = DB::table('lessons')
            ->select('lessons.id', 'lessons.slug', 'lessons.titulo', 'lessons.descripcion', 'lessonimages.imagen', 'lessons.audio', 'lessons.id_categoria', 'lessons.id_tipo', 'categorias.slug as slug_cat', 'paginas.slug as slug_pag')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->join('lessonimages', 'lessonimages.id_lesson', '=', 'lessons.id')
            ->join('paginas', 'paginas.id', '=', 'lessons.id_pagina')
            ->where('lessons.estado', 'publica')
            ->where('lessonimages.id_imagen', '0')
            ->orderByDesc('lessons.orden')
            ->get();

        foreach ($tipos as $i => $tipo) {
            $data[$i] = [
                'id' => $tipo->id,
                'titulo' => $tipo->titulo,
                'icono' => $tipo->icono,
                'color' => $tipo->color,
                'bg' => $tipo->bg
            ];
            foreach ($lecciones as $leccion) {
                if ($leccion->id_tipo == $tipo->id) {
                    $data[$i]['data'][] = $leccion;
                }
            }
        }

        $json['secciones'] = $data;
        return $json;
    }

    public function getLeccionPagina($slug_pag)
    {

        $id_pagina = DB::table('paginas')
            ->select('paginas.id')
            ->where('paginas.slug', $slug_pag)
            ->get();

        $json['paginas'] = $id_pagina;
        return $json;
    }

    public function getPagina($slug)
    {

        $pagina = Pagina::all()
            ->where('estado', 'publica')
            ->where('slug', '=', $slug)
            ->values()->all();

        $relacionada = Pagina::all()
            ->where('estado', 'publica')
            ->where('slug', '=', 'relacionada')
            ->values()->all();

        $leccion = DB::table('lessons')
            ->select('lessons.slug as slug_lec', 'categorias.slug as slug_cat')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('lessons.estado', 'publica')
            ->where('lessons.id_pagina', $pagina[0]->id)
            ->get();

        $relacionadas = DB::table('lessons')
            ->select('categorias.slug as slug_cat', 'lessons.slug as slug')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('lessons.estado', 'publica')
            ->where('lessons.id_pagina', $relacionada[0]->id)
            ->orderByDesc('lessons.orden')
            ->get();

        $leccionesRelacionadas = [];
        foreach ($relacionadas as $r) {
            $slugCat = $r->slug_cat;
            $slug = $r->slug;
            $leccionesRelacionadas[] = $this->getLeccionesCatSlug($slugCat, $slug);
        }

        $json['pagina'] = $pagina;
        $json['slugs'] = $leccion;
        $json['relacionadas'] = $leccionesRelacionadas;
        return $json;
    }

    public function buscarTermino($termino)
    {
        $lecciones = Lesson::where('titulo', 'like', '%' . $termino . '%')->get(['id', 'titulo']);
        $json['results'] = $lecciones;
        return $json;
    }

    public function buscar()
    {
        $lecciones = DB::table('lessons')
            ->select('lessons.id', 'lessons.id_categoria', DB::raw("CONCAT(lessons.titulo,' [',categorias.titulo,']') as titulo"), 'lessons.slug', 'categorias.slug as slug_cat')
            ->join('categorias', 'categorias.id', '=', 'lessons.id_categoria')
            ->where('lessons.estado', 'publica')
            ->get();

        $json['results'] = $lecciones;
        return $json;
    }

    public function getCursos()
    {
        $cursos = [
            [
                "id" => 0,
                "titulo" => "Curso de inglés BUSUU",
                "descripcion" => "El curso de inglés gratis de Busuu te ayuda a aprender inglés de forma social y práctica, con lecciones personalizadas y la ayuda de hablantes nativos.",
                "gratis" => true,
                "link" => "https://www.busuu.com/es",
            ],
            [
                "id" => 1,
                "titulo" => "Curso de inglés Edutin",
                "descripcion" => "Aprende inglés online con el curso gratuito de Edutin que cuenta con lecciones interactivas, apoyo de la comunidad y certificado opcional.",
                "gratis" => true,
                "link" => "https://app.edutin.com/category/79",
            ],
            [
                "id" => 2,
                "titulo" => "Curso de inglés Alison",
                "descripcion" => "Los cursos de inglés en línea de Alison ofrecen una variedad de cursos de inglés con certificación gratuita. Solo necesitas registrarte y comenzar a aprender hoy.",
                "gratis" => true,
                "link" => "https://alison.com/es/cursos?query=inglés",
            ],
            [
                "id" => 3,
                "titulo" => "Curso de inglés",
                "descripcion" => "Aprende inglés gratis con cursos en línea para todos los niveles, desde principiante hasta avanzado.",
                "gratis" => true,
                "link" => "https://www.curso-ingles.com/",
            ],
            [
                "id" => 4,
                "titulo" => "Curso de inglés UNAM",
                "descripcion" => "Aprende inglés de forma gratuita con el curso de inglés online que ofrece la UNAM, diponible para todos los niveles.",
                "gratis" => true,
                "link" => "https://avi.cuaieed.unam.mx/",
            ],
            [
                "id" => 5,
                "titulo" => "Curso de inglés Udemy",
                "descripcion" => "Udemy ofrece una variedad de cursos de inglés gratuitos para todos los niveles, desde principiante hasta avanzado. También ofrece la opción de obtener una certificación internacional.",
                "gratis" => true,
                "link" => "https://www.udemy.com/course/ingles-basico-i-ai/",
            ],
            [
                "id" => 6,
                "titulo" => "Curso de inglés edX",
                "descripcion" => "Aprende inglés gratis con los cursos online de edX. Aprende gramática básica, escritura, comprensión auditiva a través de videos, lecciones y ejercicios.",
                "gratis" => true,
                "link" => "https://www.edx.org/es/learn/professional-writing/university-of-california-berkeley-how-to-write-an-essay",
            ],
            // [
            //     "id" => 7,
            //     "titulo" => "Método inmersivo 3 en 9",
            //     "descripcion" => "Este es un programa intensivo en el que podrás desarrollar todas las habilidades comunicativas del inglés, como escuchar, leer, escribir y hablar. Lograrás desarrollar estas habilidades en un periodo de solo 9 meses.",
            //     "gratis" => false,
            //     "link" => "https://go.hotmart.com/W89436757X?ap=2221",
            // ],
            // [
            //     "id" => 8,
            //     "titulo" => "Habla inglés en 3 meses",
            //     "descripcion" => "Con este curso, aprender inglés será más fácil. Perderás el miedo y entenderás la gramática de manera práctica en menos de 90 días, mucho más rápido que con los cursos tradicionales.",
            //     "gratis" => false,
            //     "link" => "https://go.hotmart.com/A89456656E?ap=427e",
            // ],
        ];

        $json['cursos'] = $cursos;
        return $json;
    }

    public function getMaterial()
    {

        $hoy = date('YmdHis');
        $direccion = 'https://admin.inglesxdia.com/material';

        // El 0 está reservado para pdf automático
        $material = [
            [
                "id" => 1,
                "nivel" => "Inglés intermedio",
                "titulo" => "Guía para formar oraciones",
                "descripcion" => "Aprende a formar oraciones en inglés de forma fácil y efectiva con este curso de inglés intermedio gratis. Incluye explicaciones claras, ejercicios, y ejemplos prácticos.",
                "link" => "$direccion/guia-para-formar-oraciones.pdf",
                "archivo" => "$hoy-guia-para-formar-oraciones.pdf"
            ],
            [
                "id" => 2,
                "nivel" => "Curso de inglés básico",
                "titulo" => "El verbo To Be",
                "descripcion" => "En este curso básico de ingles, podrás aprender a usar el verbo To Be en sus formas afirmativa, negativa e interrogativa. Este verbo es fundamental en el idioma inglés, por lo que debes dominarlo.",
                "link" => "$direccion/curso-ingles-basico.pdf",
                "archivo" => "$hoy-curso-ingles-basico.pdf"
            ],
            [
                "id" => 3,
                "nivel" => "Curso intensivo de inglés",
                "titulo" => "Guía de inglés para viajeros",
                "descripcion" => "Prepárate para tu viaje a Estados Unidos con nuestro PDF Guía de Inglés para Viajeros. Aprende frases prácticas para aeropuertos, hoteles y situaciones comunes que facilitarán tu experiencia",
                "link" => "$direccion/ingles-para-viajeros.pdf",
                "archivo" => "$hoy-ingles-para-viajeros.pdf"
            ],
            [
                "id" => 4,
                "nivel" => "Curso de inglés básico",
                "titulo" => "In On At",
                "descripcion" => "Aprenda la diferencia entre in, on y at con este curso de inglés básico. Cubriremos las reglas básicas, ejemplos y ejercicios para que puedas utilizar las preposiciones correctas en cualquier contexto.",
                "link" => "$direccion/in-on-at.pdf",
                "archivo" => "$hoy-in-on-at.pdf"
            ],
            [
                "id" => 5,
                "nivel" => "Curso de inglés básico",
                "titulo" => "Uso de a y an",
                "descripcion" => "Descubre cómo usar 'a' y 'an' en inglés con esta guía. Aprende a elegir entre ambos artículos según el sonido que sigue a cada uno. Incluye ejercicios para practicar y mejorar.",
                "link" => "$direccion/a-an.pdf",
                "archivo" => "$hoy-a-an.pdf"
            ],
        ];

        $json['material'] = $material;
        return $json;
    }

    public function getPDF($slug)
    {
        $lesson = Lesson::where('slug', $slug)->first(['id', 'titulo', 'titulo_seo', 'contenido']);
        $imagen = Lessonimage::where('id_lesson', $lesson->id)->first('imagen')->imagen;
        $rutaImagen = public_path("imagen/$imagen");
        $lesson->rutaImagen = $rutaImagen;
        $contenido = $lesson->contenido;
        $patron = '/\{pdf\}(.*?)\{pdf\}/s';

        if (preg_match($patron, $contenido, $resultado)) {
            $contenidoPdf = $resultado[1];
            $lesson->contenido = $contenidoPdf;
        } else {
            abort(404);
        }

        $pdf = Pdf::loadView('lessons.pdf', $lesson->toArray());
        $pdf->setPaper('A4', 'portrait');
        return $pdf->download("$slug.pdf");
        // return $pdf->stream();
    }
}
