<?php
    /**
     * Clase que resuelve las llamadas del usuario, analizando el post o get enviado por el controlador.
     * @category File
     * @author Snithfferx
     * @version 0.4.8
     */
    ############################## Clases requeridas ##############################
    requerir("clase","controller");
    #require_once _CLASS_ . "_initializer.class.php";
    /**
     * @category Class Cargador Clase encargada de manejar las peticiones del usuario.
     * @param protected $userOn Esta variable guarda si el usuario esta activo.
     * @version 0.1.0.0
     */
    class Cargador extends Controller {
        private $userOn;
        private $config;
        protected $controller;
        /**
         * El constructor inicializa el archivo de autorizacion del usuario, para llevar al usuario al login o al controller.
         * @return void
         */
        function __construct() {
            $this->userOn = false;
            ############################## File exist ##############################
            if (file_exists(_LIBRARY_ . "_Authorization_.library.php")) { //Si se usa el control de usuarios este archivo debe existir.
                $auth = $this->initObject("lib","Authorization/Authorization");
                if ($auth) $this->userOn = $auth->getUserSession();
            } else {
                $this->userOn = true; // Cuando no se usa el control de usuarios, se iniciará *siempre* un usuario.
            }
        }
        /**
         * Esta funcion genera una respuesta por defecto del controlador HOME.
         * @return void
         */
        public function index() {
            $request = ['controller' => "home/home",'method' => "index",'params' => array()];
            return $this->getResponse($request);
        }
        /**
         * Función que devuelve la respuesta del servidor con respecto a la consulta del usuario.
         * @author snithfferx <email@email.com>
         * @param array $values Contiene el nombre del controlador, su método y los parametros a ejecutar.
         * @return string
         * @version 1.5.5
         */
        public function getResponse(array $values) {
            $view = Controller::initObject("referencia","viewBuilder");
            $error = Controller::initObject("referencia","errorViewBuilder");
            if (Controller::controllerExists($values['controller'])) {
                $this->controller = Controller::initObject("ctr",$values['controller']);
                if (Controller::methodExists($values['method'],$this->controller)) {
                    $metodo = $values['method'];
                    $request = ( is_array($values['params']) ) ? $values['params'] : array($values['params']);
                    $result = Controller::controller_init($this->controller,$metodo,$request);
                    if ( is_array($result) ) {
                        $response = $view->builder($result);
                        $view = null;
                        $error = null;
                    } else {
                        $response = $result;
                    }
                } else {
                    $response = $error->errorView(1,"00204","Not found");
                    $error = null;
                }
            } else {
                $response = $error->errorView(1,"00404","Not found");
                $error = null;
            }
            return $response;
        }
    }