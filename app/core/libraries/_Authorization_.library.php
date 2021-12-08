<?php
/**
     * Clase que resuelve las llamadas del usuario, analizando el post o get enviado por el controlador.
     * @category File
     * @author Snithfferx
     * @version 0.3.1
     */
    class Authorization {
        ########################## Public Functions ##########################
        /**
         * Función que verifica la existencia de una sesión en el servidor. De existir devuelve la seción, caso contrario devuelve "false"
         * @return bool|array
         */
        public function getUserSession() {
            if ( !isset($_SESSION) || empty($_SESSION) ) @session_start();
            if ( isset($_SESSION['user']) && !empty($_SESSION['user']) ) {
                $response = ( $this->timeOut($_SESSION['time']) ) ? false : $_SESSION;
            } else {
                $response = false;
            }
            return $response;
        }
        /**
         * Esta función se encarga de verificar sí la sesión del usuario está activa.
         * 
         * @return boolean
         */
        public function isUserSessionOn () {
            if ( !isset($_SESSION) || empty($_SESSION) ) @session_start();
            if ( isset($_SESSION['user']) && !empty($_SESSION['user']) ) {
                $response = ( $this->timeOut($_SESSION['time']) ) ? false : true;
            } else {
                $config = json_decode(file_get_contents(_REFERENCE_ . "config.json"),true);
                $response = ( $config['enviroment'] == 'dev' ) ? true : false;
            }
            return $response;
        }
        /**
         * Función que genera un tiempo de vida para la sesión y asigna al usuario a dicha sesión
         * @param $userId
         * @return bool
         */
        public function setUserSessionStandBy(string $userName, array $userOption) {
            if ( !isset($_SESSION) || empty($_SESSION) ) @session_start();
            $time = $this->setTime(400);
            $user = "APP_$" . base64_encode($userName) . "_U_base";
            $session_id = substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(5/strlen($password)) )),1,12);
            session_id($session_id);
            $_SESSION['user'] = $user;
            $_SESSION['time'] = $time;
            $_SESSION['options'] = $userOption;
            $_SESSION['cookieid'] = base64_encode("COOKIE_D" . date("Ydm",time()) . "&_U" . $user);
            $cookieName = "COOKIE_D" . date("Ydm",now()) . "&_U" . $user;
            $cookieData = $cookieName . "&_T" . $time . "&_O" . $userOption . "&_@Bytes";
            setcookie($cookieName,$cookieData,$time);
            return true;
        }
        ########################## Protected Functions ##########################

        /**
         * Función que verifica si la sesión ha caducado.
         * @param $value
         * @return bool
         */
        protected function timeOut($value) {
            $timeNow = time();
            $timeLeft = $timeNow - $value;

            return ( $timeLeft > 0 ) ? false : true;
        }
        /**
         * Función para reasignar tiempo de vida para la sesión.
         * @param $timeLapse
         * @return float|int
         */
        protected function setTime($timeLapse) {
            return $time = time() + ($timeLapse * 60);
        }
    }