<?php
    /**
     * Clase que interconecta la base de datos y resuelve las consultas emitidas por el modelo.
     * @category File
     * @author Snithfferx
     * @version 1.2.3
     */
    class _dbContext {
        ### Variables to use ###
        private $db_context;
        private $db_string;
        private $db_user;
        private $db_pass;
        protected $results;
        protected $consult;
        protected $response;
        protected $connection;
        ############################################################################################
        /*public function _construct($request = '', $data = array(), $algomas = '') {
            return $this->getDBResponse($data,$request);
        }*/
        private static function stablish_connection() {
            $message = array();
            $app = new _dbContext;
            $app->appConfig();
            try {
                $connection = new PDO( $app->db_string , $app->db_user, $app->db_pass);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $connection->exec("SET CHARACTER SET utf8");
            } catch(PDOException $excepcion) {
                //die("Error:&nbsp;".$excepcion->getMessage());
                $message['error'] = $excepcion->getMessage();
                $message['linea'] = "Linea del error:".$excepcion->getLine();
            }
            return ( isset($message['error']) ) ? $message : $connection;
        }
        protected function getDBResponse($query, $type) {
            $this->db_context = self::stablish_connection();
            if (is_string($this->db_context)) {
                return $this->db_context;
            } else {
                if ( isset($this->db_context['error']) ) {
                    $response = [
                        'message' => 'error',
                        'rows_affected' => 0,
                        'rows_registered' => 0,
                        'row_id' => "",
                        'error' => [
                            'code' => 00005,
                            'driver' => 1,
                            'message' => $this->db_context['error'] . "\n" . $this->db_context['linea']
                        ]
                    ];
                } else {
                    if ( isset($type) && !empty($type) ) {
                        $aff = array();
                        $reg = array();
                        $id = "";
                        $er = "";
                        if ( $type == 'insert' ) {
                            try {
                                $queryPDO = $this->db_context->prepare($query);
                                $aff = $queryPDO->execute();
                                $id = $queryPDO->lastInsertId();
                                $er = $queryPDO->errorInfo();
                            } catch (PDOException $th) {
                                $er = "ERROR:" . $th->getMessage() . ".\nLinea del error:" . $excepcion->getLine();
                            }
                            $results = [
                                'rows_aff' => $aff,
                                'registered' => array(),
                                'lastId' => $id,
                                'error' => $er,
                            ];
                            $queryPDO = null;
                        } elseif ( $type == 'update' ) {
                            $queryStr = $query;
                            $results = [
                                'rows_aff' => $this->db_context->exec($queryStr),
                                'registered' => array(),
                                'lastId' => '',
                                'error' => $this->db_context->errorInfo()
                            ];
                        } elseif ( $type == 'delete' ) {
                            $queryStr = $query;
                            $results = [
                                'rows_aff' => $this->db_context->exec($queryStr),
                                'registered' => array(),
                                'lastId' => '',
                                'error' => $this->db_context->errorInfo()
                            ];
                        } elseif ( $type == 'select' ) {
                            /* $queryStr = $query;
                            $response = $this->db_context->query($queryStr);
                            $results = [
                                'rows_aff' => array(),
                                'registered' => $response->fetchAll(PDO::FETCH_ASSOC),
                                'lastId' => '',
                                'error' => $this->db_context->errorInfo()
                            ]; */
                            try {
                                $queryPDO = $this->db_context->prepare($query);
                                $aff = $queryPDO->execute();
                                $reg = $aff->fetchAll(PDO::FETCH_ASSOC);
                                $id = $queryPDO->lastInsertId();
                                $er = $queryPDO->errorInfo();
                            } catch (PDOException $th) {
                                $er = "ERROR:" . $th->getMessage() . ".\nLinea del error:" . $excepcion->getLine();
                            }
                            $results = [
                                'rows_aff' => $aff,
                                'registered' => $reg,
                                'lastId' => $id,
                                'error' => $er,
                            ];
                            $queryPDO = null;
                        } else {
                            $results = [
                                'rows_aff' => array(),
                                'registered' => array(),
                                'lastId' => '',
                                'error' => [
                                    'code' => '00001',
                                    'driver' => 0,
                                    'message' => "Type error."]
                            ];
                        }
                    } else {
                        $results = [
                            'rows_aff' => array(),
                            'registered' => array(),
                            'lastId' => '',
                            'error' => [
                                'code' => '00001',
                                'driver' => 0,
                                'message' => "Missing type."]
                        ];
                    }
                    if ( $results['error'][0] != '00000' ) {
                        $response = [
                            'message' => 'fail', 
                            'rows_affected' => array(),
                            'rows_registered' => array(),
                            'row_id' => 0,
                            'error' => [
                                'code' => $results['error'][0],
                                'driver' => $results['error'][1],
                                'message' => $results['error'][2]
                            ]
                        ];
                    } else {
                        $response = [
                            'message' => 'success',
                            'rows_affected' => $results['rows_aff'],
                            'rows_registered' => $results['registered'],
                            'row_id' => $results['lastId'],
                            'error' => [
                                'code' => $results['error'][0],
                                'driver' => $results['error'][1],
                                'message' => $results['error'][2]
                            ]
                        ];
                    }
                    $results = null;
                }
                $this->db_context = null;
            }
            return $response;
        }
        private function appConfig() {
            $response = json_decode(file_get_contents(_REFERENCE_ . "config.json"), true);
            $this->db_string = "mysql:host=" . $response['dbhost'] . ";dbname=" . $response['dbname'] .";charset=utf8";
            $this->db_user = $response['dbuser'];
            $this->db_pass = $response['dbpass'];
        }
    }
    /* // la variable $pdo contendrá el objeto con la conexión PDO
    $pdo = new PDO('mysql:host=mihost;dbname=basedatos', "usuario", "contraseña");
    $id_usuario = $_POST["id"];
    $sentencia = $pdo->prepare("SELECT * FROM usuarios WHERE id = :idusuario");
    $sentencia=$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    $sentencia->bindParam(":idusuario", $id_usuario, PDO::PARAM_INT);
    $sentencia->execute(); */
    //if(isset($optionsArray) && !empty($optionsArray))
    //{$consult = $ds_context->prepare($queryTempalte,$optionsArray);}
    //else{$consult = $ds_context->prepare($queryTempalte);}
    //$consult::execute($valuesArray);
    //$results['registered_rows'] = $consult->fetchAll();
    //try {
