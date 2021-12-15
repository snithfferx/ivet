<?php
    class RequirerHelper {
        private $request;
        private $controller;
        private $method;
        private $params;
        function __Construct() {
            $this->request = $_REQUEST;
        }
        /**
         * Esta función verifica la existencia de indices en la variable goblal POST
         * Ejecuta la solicitud y devuelve un "echo" de la respuesta.
         *
         * @return void
         */
        public function requestResolver () {
            if ( isset($this->request['controller']) && !empty($this->request['controller']) ) {
                $this->controller = $this->request['controller'];
            } elseif (isset($this->request['ctr']) || !empty($this->request['ctr'])) {
                $this->controller = $this->request['ctr'];
            } else {
                $this->controller = null;
            }
            array_shift($this->request);
            try {
                if ($this->requerirObjecto("referencia","viewBuilder")) $messenger = new ViewBuilderReference;
                if ($this->requerirObjecto("clase","initializer")) $initializer = new InicializadorClass;
                if ($this->controller != null || !empty($this->controller)) {
                    $this->controller = $initializer->initObject("ctr",$this->controller);
                    if ( isset($this->request['method']) && !empty($this->request['method']) ) {
                        $this->method = $this->request['method'];
                    } elseif (isset($this->request['mtd']) || !empty($this->request['mtd'])) {
                        $this->method = $this->request['mtd'];
                    } else {
                        $this->method = null;
                    }
                    array_shift($this->request);
                    if ($this->method != null || !empty($this->method)) {
                        if ($initializer->methodExists($this->method, $this->controller)) {
                            $this->params = (is_array($this->request['data'])) ? $this->request['data'] : $this->request;
                            $result = $initializer->controller_init($this->controller,$this->method,$this->params);
                        } else {
                            #throw new Exception('Method is not can be found.');
                            $result = $messenger->viewBuilder("error",[1,"00501","Method is not can be found."]);
                        }
                    } else {
                        #throw new Exception('Method is not can be found.');
                        $result = $messenger->viewBuilder("error",[1,"00501","Method is not can be found."]);
                    }
                } else {
                    $result = $messenger->viewBuilder("error",[1,"00501","Controller is not can be found."]);
                    #throw new Exception('Controller is not can be found.');
                }
            } catch (Exception $exception) {
                $data = [
                    'message' => $exception->getMessage(),
                    'code'    => $exception->getCode(),
                    'line'    => $exception->getLine(),
                    'trace'   => $exception->getTraceAsString()
                ];
                $result = $messenger->viewBuilder("error",$data);
            }            
            $response = (is_array($result)) ? $this->smarty($result) : $result;
            return $response;
        }
        /**
         * Esta función verifica la existencia de indices en la variable goblal GET
         * Ejecuta la solicitud y devuelve un "echo" de la respuesta.
         *
         * @return void
         */
        /* public function initGet () {
            if (isset($_GET['ctr']) && !empty($_GET['ctr'])) {
                $data = explode("&",$_GET['ctr']);
                if ( isset($data['controller']) && !empty($data['controller']) ) {
                    if ( isset($data['method']) && !empty($data['method']) ) {
                        $loader = $this->requerirObjecto("clase","loader");
                        if ($loader) {
                            $carga = new Cargador;
                            $response = $carga->getResponse($data);
                        } else {
                            $this->requerirObjecto("referencia","errorMessage");
                            $error = new ErrorMessenger;
                            $response = $error->errorSender(1,"00501","Not found");
                        }
                    } else {
                        $this->requerirObjecto("referencia","errorMessage");
                        $error = new ErrorMessenger;
                        $response = $error->errorSender(1,"00204","Method does not exists.");
                    }
                } else {
                    $this->requerirObjecto("referencia","errorMessage");
                    $error = new ErrorMessenger;
                    $response = $error->errorSender(1,"00204","This function is not available.");
                }
            } else {
                $this->requerirObjecto("referencia","errorMessage");
                $error = new ErrorMessenger;
                $response = $error->errorSender(1,"00502","Missing Arguments");
            }
            echo $response;
            return false;
        } */
        /**
         * Esta función ejecuta la acción por defecto del servidor
         * para posteriormente realizar un "echo" de su respuesta.
         *
         * @return void
         */
        public function initDefault () {
            if ($this->requerirObjecto("clase","loader")) {
                $carga = new Cargador;
                $result = $carga->index();
            } else {
                $messenger = $this->requerirObjecto("referencia","viewBuilder");
                $result = $messenger->viewBuilder("error",[1,"00501","404"]);
            }
            $response = (is_array($result)) ? $this->smarty($result) : $result;
            return $response;
            /* var_dump($result);
            return; */
        }
        /**
         * Función que realiza un "require_once" de un objeto solicitado por el sistema.
         *
         * @param string $requirementType
         * @param string $requirementName
         * @return bool
         */
        public function requerirObjecto(string $requirementType, string $requirementName ) {
            $type = (!empty($requirementType) || $requirementType != "") ? $requirementType : false;
            $name = (!empty($requirementName) || $requirementName != "") ? $requirementName : false;
            if ($type != false && $name != false) {
                switch ($type) {
                    case "clase":
                        $path = _CLASS_ . "_" . $name . ".class.php";
                        break;
                    case "referencia":
                        $path = _REFERENCE_ . "R_" . $name . ".reference.php";
                        break;
                    case "libreria":
                        $path = _LIBRARY_ . "_" . $name . "_.library.php";
                        break;
                    case "ayudante":
                        $path = _HELPER_ . "H_" . $name . "_.helper.php";
                        break;
                    default:
                        $exists = false;
                        break;
                }
                $exists = (file_exists($path)) ? require_once $path : false;
            } else {
                $exists = false;
            }
            return $exists;
        }
        # ---------- Métodos protegidos ---------- #
        /**
         * Esta función se encarga de generar una vista usando las librerias de Smarty.
         * 
         * @param array $templateData Esta contiene los datos a ser mostrados en la plantilla.
         * @return string
         */
        protected function smarty(array $templateData) {
            /* ########## Colectando información para crear la vista ##########*/
            $viewData = $templateData['view'];
            $viewType = $viewData['type'];
            $viewName = $viewData['name'];
            $moduleDir = $viewData['data']['module'];
            $layoutDir = $viewData['data']['layout'];
            $templateDir = $viewData['data']['template'];
            $data = $templateData['data'];
            // ------------------- //
            $cacheDir = (isset($templateData['cachedir'])) ? $templateData['cachedir'] : "";
            $confDir = (isset($templateData['configdir']))? $templateData['configdir'] : "";
            /* ########## Asignando variables de entorno ##########*/
            $layoutsDir = (empty($layoutDir)) ? _VIEW_ . "$moduleDir/" : _VIEW_ . "$moduleDir/$layoutDir/";
            $templatesDir = (empty($templateDir)) ? _VIEW_ . "$moduleDir/" : _VIEW_ . "$moduleDir/$templateDir/";
            $configsDir = ($confDir == "") ? _APP_ . "/configs/" : _VIEW_ . "$moduleDir/$confDir/";
            $cachesDir = ($confDir == "") ? _APP_ . "/cache/" : _VIEW_ . "$moduleDir/$cacheDir/";
            include_once(_REFERENCE_ . "R_templateLibs.reference.php");
            $displayer->setTemplateDir($layoutsDir);
            $displayer->addTemplateDir($templatesDir,"tpl");
            $displayer->setConfigDir($configsDir);
            $displayer->setCacheDir($configsDir);
            /* ########## Realizando test ##########*/
            //$displayer->testInstall();
            /* ########## Asignando variables de contenido ##########*/
            $displayer->assign("dom",$data['content']);
            //$displayer->assign("layout",$data['layout']);
            $displayer->assign("_VISTA_",_VIEW_);
            #$displayer->assign("_DESIGN_",_LAYOUT_);
            /* ########## Creando la vista ##########*/
            if ( $viewType == "layout" ) {
                if ($displayer->templateExists($viewName)) {
                    $response = $displayer->fetch($viewName); #Fetching la respuesta de la librería.
                    $displayer->clearAllAssign(); #Limpiando las variables
                    //$displayer = null; #Cerrando la librería.
                } else {
                    #ERROR#
                    $response = "La vista no se ha encontrado";
                    $displayer->clearAllAssign();
                    //$displayer = null;
                }
            } else {
                if (isset($layoutDir) && !empty($layoutDir)) {
                    $view = $layoutsDir . $viewName;
                    if ($displayer->templateExists($view)) {
                        $response = $displayer->fetch($viewName); #Fetching la respuesta de la librería.
                        $displayer->clearAllAssign(); #Limpiando las variables
                        //var_dump($displayer->getTemplateDir());
                        //$displayer = null; #Cerrando la librería.
                    } else {
                        #ERROR#
                        $response = "La vista no se ha encontrado";
                        $displayer->clearAllAssign();
                        //$displayer = null;
                    }
                } else {
                    $view = $templatesDir . $viewName;
                    if ($displayer->templateExists($view)) {
                        $response = $displayer->fetch($view);
                        $displayer->clearAllAssign();
                        //var_dump($displayer->getTemplateDir());
                        //$displayer = null; #Cerrando la librería.
                    } else {
                        #ERROR#
                        $response = "La vista no se ha encontrado";
                        $displayer->clearAllAssign();
                    }
                }
            }
            $displayer = null;
            return $response;
        }
    }
?>