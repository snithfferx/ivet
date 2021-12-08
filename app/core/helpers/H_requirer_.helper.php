<?php
    class RequireHelper {
        /**
         * Esta función verifica la existencia de indices en la variable goblal POST
         * Ejecuta la solicitud y devuelve un "echo" de la respuesta.
         *
         * @return void
         */
        public function initPost () {
            if ( isset($_POST['controller']) && !empty($_POST['controller']) ) {
                if ( isset($_POST['method']) && !empty($_POST['method']) ) {
                    if (requerir("clase","loader")) {
                        $carga = new Cargador;
                        $result = $carga->getResponse($_POST);
                        $carga = null;
                    } else {
                        /* requerir("referencia","errorMessage");
                        $error = new ErrorMessenger;
                        $response = $error->errorSender(1,"00501","Not found");
                        $error = null; */
                        $result = "Page not  found";
                    }
                } else {
                    /* requerir("referencia","errorMessage");
                    $error = new ErrorMessenger;
                    $response = $error->errorSender(1,"00204","Method does not exists.");
                    $error = null; */
                    $result = "Method does not exists.";
                }
            } else {
                /* requerir("referencia","errorMessage");
                $error = new ErrorMessenger;
                $response = $error->errorSender(1,"00204","Function is not available.");
                $error = null; */
                $result = "Function is not available.";
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
        public function initGet () {
            if (isset($_GET['ctr']) && !empty($_GET['ctr'])) {
                $data = explode("&",$_GET['ctr']);
                if ( isset($data['controller']) && !empty($data['controller']) ) {
                    if ( isset($data['method']) && !empty($data['method']) ) {
                        $loader = requerir("clase","loader");
                        if ($loader) {
                            $carga = new Cargador;
                            $response = $carga->getResponse($data);
                        } else {
                            requerir("referencia","errorMessage");
                            $error = new ErrorMessenger;
                            $response = $error->errorSender(1,"00501","Not found");
                        }
                    } else {
                        requerir("referencia","errorMessage");
                        $error = new ErrorMessenger;
                        $response = $error->errorSender(1,"00204","Method does not exists.");
                    }
                } else {
                    requerir("referencia","errorMessage");
                    $error = new ErrorMessenger;
                    $response = $error->errorSender(1,"00204","This function is not available.");
                }
            } else {
                requerir("referencia","errorMessage");
                $error = new ErrorMessenger;
                $response = $error->errorSender(1,"00502","Missing Arguments");
            }
            echo $response;
            return false;
        }
        /**
         * Esta función ejecuta la acción por defecto del servidor
         * para posteriormente realizar un "echo" de su respuesta.
         *
         * @return void
         */
        public function initDefault () {
            if (requerir("clase","loader")) {
                $carga = new Cargador;
                $result = $carga->index();
            } else {
                requerir("referencia","ErrorViewBuilder");
                $error = new ErrorViewBuilder;
                $result = $error->errorMessage(1,"00501","404");
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
        public function requerir(string $requirementType, string $requirementName ) {
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
                        $path = _HELPER_ . "H__" . $name . "__.helper.php";
                        break;
                    default:
                        $exists = false;
                        break;
                }
                $exists = file_exists($path);
                if ($exists) require_once $path;
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
            $moduleDir = $templateData['moduledir'];
            $layoutDir = $templateData['layoutdir'];
            $viewName = $templateData['viewname'];
            $templateDir = $templateData['templatedir'];
            $data = $templateData['content'];
            // ------------------- //
            $viewType = $templateData['viewtype'];
            $cacheDir = (isset($templateData['cachedir'])) ? $templateData['cachedir'] : "";
            $confDir = (isset($templateData['configdir']))? $templateData['configdir'] : "";
            /* ########## Asignando variables de entorno ##########*/
            $layoutsDir = (empty($layoutDir)) ? _VIEW_ . $moduleDir : _VIEW_ . $layoutDir;
            $templatesDir = (empty($templateDir)) ? _VIEW_ . $moduleDir : _VIEW_ . $templateDir;
            $configsDir = ($confDir == "") ? _APP_ . "/app/configs/" : _VIEW_ . "$moduleDir/$confDir/";
            $cachesDir = ($confDir == "") ? _APP_ . "/app/cache/" : _VIEW_ . "$moduleDir/$cacheDir/";
            include_once(_REFERENCE_ . "R_templateLibs.reference.php");
            $displayer->setTemplateDir($layoutsDir);
            $displayer->addTemplateDir($templatesDir,"tpl");
            $displayer->setConfigDir($configsDir);
            $displayer->setCacheDir($configsDir);
            /* ########## Realizando test ##########*/
            #$displayer->testInstall();
            /* ########## Asignando variables de contenido ##########*/
            $displayer->assign("data",$data['data']);
            $displayer->assign("layout",$data['layout']);
            $displayer->assign("_VISTA_",_VIEW_);
            #$displayer->assign("_DISENO_",_LAYOUT_);
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
                if (isset($templateData['layoutdir']) && !empty($templateData['layoutdir'])) {
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