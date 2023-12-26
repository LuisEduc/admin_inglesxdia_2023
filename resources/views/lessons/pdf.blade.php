<!DOCTYPE html>
<html>

<head>
    <title>Document</title>
    <style>
        * {
            font-family:'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
        }

        h1 {
            font-size: 2.5rem;
            margin: 1rem 3rem;
            text-align: center;
        }

        div {
            text-align: center;
        }

        img {
            height: 400px;
            width: 325px;
            margin: auto;
        }

        ul, p {
            list-style-type: none;
            margin: 1rem 2rem;
            font-size: 1.75rem;
            text-align: justify;
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

    <h1>{{$titulo_seo}}</h1>
    <div>
        <img src="{{ $rutaImagen }}">
    </div>
    <div class="contenido">
        <p>{!!$contenido!!}</p>
    </div>
    <footer>
        En <a href="https://inglesxdia.tech/">inglesxdia</a> aprende inglés todos los días | Todos los derechos reservados.
    </footer>
</body>

</html>