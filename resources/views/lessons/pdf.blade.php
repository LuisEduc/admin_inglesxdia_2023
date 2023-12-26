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
            padding: 5rem;
        }

        h1 {
            padding: 1rem 0;
            font-size: 2.5rem;
            text-align: left;
            text-align: center;
        }

        .imagen {
            text-align: center;
        }

        img {
            height: 400px;
            width: 325px;
        }

        .contenido ul,
        .contenido p {
            list-style-type: none;
            font-size: 1.75rem;
            text-align: justify;
            margin-top: 1rem;
        }

        .contenido p em,
        .contenido ul em {
            font-style: normal;
        }

        footer {
            position: absolute;
            bottom: 1rem;
            font-size: 1rem;
            left: 0;
            right: 0;
            text-align: center;
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