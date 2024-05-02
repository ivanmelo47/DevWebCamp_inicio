<?php

namespace Controllers;

use Classes\Paginacion;
use Model\Ponente;
use MVC\Router;
use Intervention\Image\ImageManagerStatic as Image;

class PonentesController {

    public static function index(Router $router) {

        // Validacion de Paginacion
        $pagina_actual = $_GET['page'];
        $pagina_actual = filter_var($pagina_actual, FILTER_VALIDATE_INT);
        if (!$pagina_actual || $pagina_actual < 1) {
            header('Location: /admin/ponentes?page=1');
        }
        //Paginacion
        $registros_por_pagina = 5;
        $total = Ponente::total();
        $paginacion = new Paginacion($pagina_actual, $registros_por_pagina, $total);
        // Valida que no se pagine mas haya de lo existente
        if ($paginacion->total_paginas() < $pagina_actual) {
            header('Location: /admin/ponentes?page=1');
        }
        // Llama datos de la BD en base a la paginacion
        $ponentes = Ponente::paginar($registros_por_pagina, $paginacion->offset());

        if (!is_admin()) {
            header('Location: /login');
        }

        $router->render('admin/ponentes/index', [
            'titulo' => 'Ponentes / Conferencistas',
            'ponentes' => $ponentes,
            'paginacion' => $paginacion->paginacion()
        ]);
    }

    public static function crear(Router $router) {
        if (!is_admin()) {
            header('Location: /login');
        }

        $alertas = [];
        $ponente = new Ponente;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('Location: /login');
            }
            //Leer imagen
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';

                // Crear Carpeta de Imagenes si no existe
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0777, true);
                }

                // Crea las imagenes png y webp a partir de la imagen subida a traves del formulario de crear ponente
                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(880, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(880, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid( rand(), true) );

                $_POST['imagen'] = $nombre_imagen;
            }

            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);

            $ponente->sincronizar($_POST);

            //Validar
            $alertas = $ponente->validar();

            //Guardar Registro
            if(empty($alertas)) {
                // Guardar Imgenes
                $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');

                // Guardar en la BD
                $resultado = $ponente->guardar();

                if($resultado){
                    header('Location: /admin/ponentes');
                }
            }
        }

        $router->render('admin/ponentes/crear', [
            'titulo' => 'Registrar Ponente New',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
        ]);
    }

    public static function editar(Router $router) {
        // Si el usuario no es admin redirecciona al apartado de login
        if (!is_admin()) {
            header('Location: /login');
        }

        $alertas = [];
        
        // Validar Id
        $id = $_GET['id'];
        $id = filter_var($id, FILTER_VALIDATE_INT);

        if (!$id) {
            header('Location: /admin/ponentes');
        }

        // Busca en la BD el Ponente a Editar
        $ponente = Ponente::find($id);

        // Si el ponente no existe redirecciona al dashboard de ponentes
        if (!$ponente) {
            header('Location: /admin/ponentes');
        }

        // Guarda en una variable el nombre actual de la imagen del ponente
        $ponente->imagen_actual = $ponente->imagen;

        // Valida que se haya hecho un POST desde el formulario para editar ponente
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!is_admin()) {
                header('Location: /login');
            }

            //Leer imagen desde el formulario de editar ponente
            if (!empty($_FILES['imagen']['tmp_name'])) {
                $carpeta_imagenes = '../public/img/speakers';

                // Crear Carpeta de Imagenes si no existe
                if (!is_dir($carpeta_imagenes)) {
                    mkdir($carpeta_imagenes, 0777, true);
                }

                $imagen_png = Image::make($_FILES['imagen']['tmp_name'])->fit(880, 800)->encode('png', 80);
                $imagen_webp = Image::make($_FILES['imagen']['tmp_name'])->fit(880, 800)->encode('webp', 80);

                $nombre_imagen = md5(uniqid( rand(), true) );

                $_POST['imagen'] = $nombre_imagen;
            } else {
                $_POST['imagen'] = $ponente->imagen_actual;
            }

            $_POST['redes'] = json_encode($_POST['redes'], JSON_UNESCAPED_SLASHES);
            $ponente->sincronizar($_POST);
            $alertas = $ponente->validar();

            if (empty($alertas)) {
                if (isset($nombre_imagen)) {
                    // Eliminar imagenes anteriores si existen
                    $ruta_imagen_anterior = $carpeta_imagenes . '/' . $ponente->imagen_actual;
                    if (file_exists($ruta_imagen_anterior.'.png') && file_exists($ruta_imagen_anterior.'.webp')) {
                        unlink($ruta_imagen_anterior . '.png');
                        unlink($ruta_imagen_anterior . '.webp');
                    }

                    // Guardar Imagenes Nuevas
                    $imagen_png->save($carpeta_imagenes . '/' . $nombre_imagen . '.png');
                    $imagen_webp->save($carpeta_imagenes . '/' . $nombre_imagen . '.webp');
                }
                $resultado = $ponente->guardar();

                if ($resultado) {
                    header('Location: /admin/ponentes');
                }
            }
        }

        $router->render('admin/ponentes/editar', [
            'titulo' => 'Editar Ponente',
            'alertas' => $alertas,
            'ponente' => $ponente,
            'redes' => json_decode($ponente->redes)
        ]);
    }
    public static function eliminar() {
        if (!is_admin()) {
            header('Location: /login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!is_admin()) {
                header('Location: /login');
            }

            $id = $_POST['id'];
            $ponente = Ponente::find($id);

            // Guardar el nombre de la imagen del ponente
            $ponente->imagen_actual = $ponente->imagen;
            $carpeta_imagenes = '../public/img/speakers';

            // Verifica si se encontro ponente con el id dado
            if (!isset($ponente)) {
                header('Location: /admin/ponentes');
            }
            
            // Elimina el registro del ponente de la base de datos
            $resultado = $ponente->eliminar();

            // Verifica si esque el ponente fue eliminado o no
            if (!isset($resultado)) {
                header('Location: /admin/ponentes');
            } else {
                // Eliminar imagenes del ponente que esten en el servidor
                $ruta_imagen_anterior = $carpeta_imagenes . '/' . $ponente->imagen_actual;
                if (file_exists($ruta_imagen_anterior.'.png') && file_exists($ruta_imagen_anterior.'.webp')) {
                    unlink($ruta_imagen_anterior . '.png');
                    unlink($ruta_imagen_anterior . '.webp');
                }

                header('Location: /admin/ponentes');
            }
        }
    }

}