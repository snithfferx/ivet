<?php
    /**
     * Clase que resuelve las llamadas del usuario a las distintas librerias.
     * @category File
     * @author Snithfferx
     * @version 0.06.0
     */
    class Inicializador {
        ########################### Public Methods ###########################
        /**
         * Función que devuelve un objeto solicitado, sea Controller(ctr), Libreria(lib), 
         * Modelo(mdl), Referencia(ref), etc.
         * @param string $type Guarda el tipo de objeto buscado
         * @param string $name Guarda el nombre del objeto buscado, con la nomenclatura 
         * "(Objeto)/(Clase)", "(Modulo)/(Objeto)/(Clase)" o (Objeto). 
         * @return object
         * @version 1.0.0
         * @example initObject(string $type, string $name) Donde "$type" es el tipo "Controller"
         * "$name" Contiene "Account/account/login"; "Account/" es el nombre del modulo, 
         * "account/" es el nombre del controlador y "login" es el nombre de la clase a inicializar.
         */
        public function initObject( string $type, string $name) {
            $x = explode("/", $name);
            if ( count($x) > 2 ) {
                $response = $this->initializer($type, $x[0], $x[1], $x[2]);
            } elseif ( count($x) > 1 ) {
                $response = $this->initializer($type, $x[0], $x[1]);
            } else {
                $response = $this->initializer($type, $x[0]);
            }
            return $response;
        }
        /**
         * Función que devuelve el objeto del tipo Model solicitado por el usuario.
         * @param string $module Guarda el nombre del modulo donde se buscará el modelo
         * @param string $model Guarda el nombre del Model buscado, con la nomenclatura "[Model]/[Class]"
         * @return object
         */
        public function getModel(string $module, string $model){
            $x = explode("/", $model);
            return (count($x) > 1 ) ? $this->model($module,$x[0],$x[1]) : $this->model($module,$x[0],$x[0]);
        }
        /**
         * Función para generar una palabra pluralizada sin exepciones. Lenguaje Inglés.
         * @method string pluralizer() pluralizer($string) Verifica la forma de convertir la palabra singular en plural
         * @param string $name Toma el valor de la palabra en singural, para generar su plural.
         * @return string
         */
        public function pluralizer(string $name) {
            $plural = "";
            if ( $name == "inicio" || $name == "home" || $name == "login" ) {
                $plural = $name;
            } else {
                $sufix = substr($name,(strlen($name) - 2),2);
                if ( $sufix == "ed") {
                    $plural = $name;
                } elseif ( $sufix == "ad" || $sufix == "ad") {
                    $plural = $name . "es";
                } else {
                    $to_pop = str_split($name);
                    $poped = array_pop($to_pop);
                    if ( $poped == "y") {
                        $plural = implode($to_pop) . "ies";
                    } elseif ( $poped == "z" ) {
                        $plural = implode($to_pop) . "ces";
                    } elseif ( $poped == "s" || $poped == "x" ) {
                        //$plural = implode($to_pop) . "ses";
                        $plural = $name;
                    } elseif ( $poped == "n" || $poped == "r" || $poped == "l" || $poped == "i" || $poped == "u" ) {
                        $plural = $name . "es";
                    } else {
                        $plural = $name . "s";
                    }
                }
            }
            return $plural;
        }
        /**
         * Función que devuelve la respuesta de un controlador
         * @param array $values Contiene el array de POST o GET enviada desde la vista.
         * Su estructura es ['controller'{[Modulo]/[Controlador]/[clase]}, 'method'{[función]},'datos'{[array]}]
         * @return array Contiene la información para la vista, así como el contenido de esta.
         */
        public function controller_init($object,string $method, array $values) {
            if ( $this->getMethod($method , $object) ) {
                $response = $object->$method($values);
            } else {
                $response = [
                    'error' => [
                        'code' => "00001",
                        'driver' => 2,
                        'message' => "Information not found"
                    ],
                    'message' => "fail",
                ];
            }
            return $response;
        }
        /**
         * Esta función busca un método en el controlador del modulo dado.
         * @param string $method Guarda el nombre del metodo a buscar
         * @param object $controller Guarda el objeto del tipo Controller donde se buscará el metodo a ejecutarse.
         * @return bool
         */
        public function methodExists(string $method, $controller) {
            return ( method_exists($controller, $method) ) ? true : false;
        }
        ########################### Protected Methods ###########################
        /**
         * Función que busca el objeto solicitado por el usuario y lo inicializa.
         * @param string $t Guarda el tipo de objeto a buscar e inicializar
         * @param string $m Guarda el nombre del modulo donde se buscará el objeto.
         * @param string $n Guarda el nombre del objeto a buscar e inicializar
         * @param string $c Guarda el nombre de la clase embebida en el objeto a buscar e inicializar
         * @return mixed
         */
        protected function initializer(string $type, string $lv0, string $lv1 = "", string $lv2 = "") {
            $path = "";
            switch ($type) {
                case 'lib':
                    $path = ($lv1 == "" ) ? _MODULE_ . $lv0 . "/_libraries/_" . $lv0 . "_.library.php" : _MODULE_ . $lv0 . "/_libraries/_" . $lv1 . "_.library.php";
                    break;
                case 'ref':
                    $path = ($lv1 == "") ? _MODULE_ . $lv0 . "/references/R_" . $lv0 . ".reference.php" : _MODULE_ . $lv0 . "/R_" . $lv1 . ".reference.php";
                    break;
                case 'hlp':
                    $path = ($lv1 == "") ? _MODULE_ . $lv0 . "/_helpers_/H_" . $lv0 . "_.helper.php" : _MODULE_ . $lv0 . "/_helpers_/H_" . $lv1 . "_.helper.php";
                    break;
                case 'ctr':
                    $ctrPlural = ($lv1 == "") ? $this->pluralizer($lv0) : $this->pluralizer($lv1);
                    $path = _MODULE_ . $lv0 . "/controllers/" . $ctrPlural . ".controller.php";
                    break;
                case 'mdl':
                    $path = ($lv1 == "") ? _MODULE_ . $lv0 . "/models/" . $lv0 . ".model.php" : _MODULE_ . $lv0 . "/models/" . $lv1 . ".model.php";
                    break;
                case 'cls':
                    $path = ($lv1 == "") ? _MODULE_ . $lv0 . "/classes_/_" . $lv0 . ".class.php" : _MODULE_ . $lv0 . "/classes_/_" . $lv1 . ".class.php";
                    break;
                case "clase":
                    $path = _CLASS_ . "_" . $lv0 . ".class.php";
                    break;
                case "referencia":
                    $path = _REFERENCE_ . "R_" . $lv0 . ".reference.php";
                    break;
                case "libreria":
                    $path = _LIBRARY_ . "_" . $lv0 . "_.library.php";
                    break;
                case "ayudante":
                    $path = _HELPER_ . "H_" . $lv0 . "_.helper.php";
                    break;
            }
            if ( file_exists($path) ) {
                require_once $path;
                if ($lv2 != "") {
                    $response = new $lv2;
                } elseif ($lv1 != "") {
                    $response = new $lv1;
                } else {
                    $response = new $lv0;
                }
            } else {
                $response = false;
            }
            return $response;
        }
        /**
         * Función que busca e inicializa un objeto de tipo Controller.
         * @param string $m Nombre del modulo que contiene el objeto.
         * @param string $ctr Nombre del Controller.
         * @param string $cl Nombre de la clase embebida en el controlador
         * @return mixed
         */
        protected function controller($m,$ctr,$cl) {
            $path = _MODULE_ . $m . "/controllers/" . $ctr . "s.controller.php";
            $exists = ( file_exists($path) ) ? true : false;
            if ( $exists == true ) {
                require_once $path;
                $response = new $cl;
            } else {
                $response = false;
            }
            return $response;
        }
        /**
         * Función que busca e inicializa un objeto de tipo Model.
         * @param string $m Nombre del modulo que contiene el objeto.
         * @param string $mo Nombre del Model.
         * @param string $cl Nombre de la clase embebida en el modelo.
         * @return mixed
         */
        protected function model($m,$mo, $cl) {
            $path = _MODULE_ . $m . "/models/" . $mo . ".reference.php";
            $exists = (file_exists($path)) ? true : false ;
            if ( $exists == true ) {
                require_once $path;
                $response = new $cl;
            } else {
                $response = false;
            }
            return $response;
        }
    }