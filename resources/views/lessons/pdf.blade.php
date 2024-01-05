<!DOCTYPE html>
<html>

<head>
    <title>Document</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        body {
            padding: 2rem 5rem;
        }

        h1 {
            padding: 1rem 0;
            font-size: 2.5rem;
            text-align: left;
        }

        .imagen {
            text-align: left;
        }

        img {
            height: 400px;
            width: 325px;
        }

        .contenido ul,
        .contenido p {
            list-style-type: none;
            font-size: 1.7rem;
            text-align: justify;
            margin: 12px 0;
        }

        .contenido p em,
        .contenido ul em {
            font-style: normal;
        }

        footer {
            position: absolute;
            bottom: 2rem;
            font-size: 1rem;
            left: 5rem;
            right: 0;
            text-align: left;
        }
    </style>
</head>

<body>

    <div class="hero">
        <h1>{{$titulo_seo}}</h1>
        <div class="imagen">
            <img src="{{ $rutaImagen }}">
        </div>
    </div>

    <div class="contenido">
        <p>{!!$contenido!!}</p>
    </div>

    <footer>
        En <a href="https://inglesxdia.tech/">inglesxdia</a> aprende inglés todos los días | Todos los derechos reservados.
    </footer>
</body>

</html>