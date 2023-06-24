<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pagina extends Model
{
    use HasFactory;

    protected $fillable = [  'slug', 'orden', 'titulo', 'titulo_seo', 'descripcion', 'estado', 'pagcontenido' ];

    protected $hidden = ['created_at', 'updated_at'];

    public function lessons(){
        return $this->hasMany(Tipo::class, 'id');
    }
}