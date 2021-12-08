<?php
    /** Loader para carga de la aplicacion
     * @category File 
     * @global _APP_ Declara la ruta raiz de la aplicacion
     * @global _CLASS_ Declara la ruta raiz de las clases
     * @global _REFERENCE_ Declara la ruta raiz de las ferencias
     * @global _MODULE_ Declara la ruta raiz de las modulos
     * @global _LIBRARY_ Declara la ruta raiz de las librerias
     * @global _HELPER_ Declara la ruta raíz de los ayudantes.
     * @global _VIEW_ Declara la ruta raiz de las vistas
     * @global _LAYOUT_ Declara la ruta raiz de las layouts
     * @var object $loader esta variable guardara la clase y sus componentes.
     * @version 2.4.6
     */
        # ---------- Variables globales ---------- #
    define("_APP_", dirname(__DIR__,2));
    define("_CLASS_", _APP_ . "/app/core/classes/");
    define("_REFERENCE_", _APP_ . "/app/core/references/");
    define("_MODULE_", _APP_ . "/app/modules/");
    define("_LIBRARY_", _APP_ . "/app/core/libraries/");
    define("_HELPER_", _APP_ . "/app/core/helpers/");
    define("_VIEW_", _APP_ . "/public/views/");