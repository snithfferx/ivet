<?php
    /**
     * Clase que resuelve las llamadas del usuario a la base de datos.
     * @category File
     * @author Snithfferx
     * @version 0.1.4
     */
    #########################-/LO QUE DEVUELVE/-#########################
    /* $results = [
        'message' => 'success',
        'rows_affected' => $results['rows_aff'],
        'rows_registered' => $results['registered'],
        'row_id' => $results['lastId'],
        'error' => [
            'code' => $results['error'][0],
            'driver' => $results['error'][1],
            'message' => $results['error'][2]
        ]
    ]; */
    #########################-//-#########################
    require_once "_dbContext.class.php";
    class Model extends _dbContext {
        ########################## Public Functions ##########################
        public function select( string $tableName, array $data, int $limit = 0, string $sort = '', string $sortBy = '' ) {
            if ( is_array($data) ) {
                /* $tableName, será el nombre de la tabla de donde se obtendran los registros.
                    $data, será el array de campos a obtenerse.
                    $limit, será la cantidad maxima de registros a devolver por la función.
                    $sort, será el orden de que los registros se presentarán. ORDER BY tabla.campo*/
                return $this->getDBData($tableName,$data,$limit,$sort,$sortBy);
            }
        }
        public function insert( string $tableName, array $data ) {
            if ( is_array($data) ) {
                $fields = $data['fields'];
                $values = $data['values'];
                return $this->setDBData( 'insert', $tableName, $fields, $values );
            }
        }
        public function delete( string $tableName, array $data ) {
            if ( is_array($data) ) {
                $fields = $data['fields'];
                $values = $data['values'];
                $params = $data['params'];
                return $this->setDBData( 'update', $tableName, $fields, $values, $params );
            }
        }
        public function edit( string $tableName, array $data ) {
            if ( is_array($data) ) {
                $fields = $data['fields'];
                $values = $data['values'];
                $params = $data['params'];
                return $this->setDBData( 'update', $tableName, $fields, $values, $params );
            }
        }
        public function custom(string $tableName, $data ){
            $fields = $data['fields']; //Campos a modificar o colectar.
            $values = $data['values']; //Valores a Insertar o vacio al colectar datos.
            $params = $data['params']; //Parametros a cumplirse para colectar o modificar un registro.
            $limit = $data['limit']; //Límite de registros a devolver.
            $sorted = $data['sort']; //Orden en el que se presentaran los registros obtenidos.
            $type = $data['type']; //Tipo de consulta a realizar (Insert, Select, Update).
            return $this->getDBCustomData( $type, $tableName, $fields, $values, $params, $limit, $sorted );
        }
        ########################## Protected Functions ##########################
        protected function getDBData( string $t, array $q, int $l = 0, string $s = '', string $sby ='' ) {
            $query_request = "SELECT ";
            if ( is_array($q['fields']) AND !empty($q['fields']) ) {
                $c = count($q['fields']) - 1;
                for ($x = 0; $x < count($q['fields']); $x++ ) {
                    if( $x < $c ) {
                        $query_request .= $q['fields'][$x] . ", ";
                    } else {
                        $query_request .= $q['fields'][$x];
                    }
                }
            } else {
                $query_request .= $q['fields'];
            }
            $query_request .= " FROM `$t` ";
            if ( !empty($q['inners']) ) {
                foreach ( $q['inners'] as $in ) {
                    //print($in['innerType']);
                    $query_request .= "INNER $in[innerType] `$in[innerTable]` ON `$in[innerTable]`.`$in[innerFilter]` = `$in[innerCompareTable]`.`$in[innerCompare]`";
                }
            }
            if ( !empty($q['params']) ) {
                $query_request .= " WHERE ";
                $pspt = preg_split('/([,|;|~])/',$q['params'],NULL,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $y = 1;
                foreach ( $pspt as $ps ) {
                    if ( $ps == "," ) {
                        $query_request .= " AND ";
                    } elseif ( $ps == ";" ) {
                        $query_request .= " OR ";
                    } elseif ( $ps == "~" ) {
                        $query_request .= " LIKE ";
                    } else {
                        $query_request .= "$ps";
                    }
                }
            }
            /* ORDER_ASC, ORDER_DES, GROUP */
            if ( $s != '' AND $sby != '' ) {
                if ( $s != NULL AND $sby != NULL ) {
                    # tipo de busqueda o arreglo
                    switch ($s) {
                        case 'ORDER_ASC':
                            $query_request .= " ORDER BY $sby ASC ";
                            break;
                        case 'ORDER_DES':
                            $query_request .= " ORDER BY $sby DESC ";
                            break;
                        case 'GROUP':
                            $query_request .= " GROUP BY $sby ";
                            break;
                        default:
                            $query_request .= "";
                            break;
                    }
                }
            }
            if ( $l != NULL ) {
                $query_request .= ( $l > 0 ) ? " LIMIT " . $l . " ;" : ";";
            } else {
                $query_request .= " ;";
            }
            $result = $this->getDBResponse($query_request, 'select');
            if ( $result['error']['code'] != 0 ) {
                $response = [
                    'data'=>[
                        'rows' => array(),
                        'affected' => array(),
                        'id_row' => '',
                    ],
                    'error' => $result['error'],
                    'message'=> $result['message']
                ];
            } else {
                $response = [
                    'data' => [
                        'rows' => $result['rows_registered'],
                        'affected' => $result['rows_affected'],
                        'id_row' => $result['row_id'],
                    ],
                    'error' => $result['error'],
                    'message' => $result['message'],
                ];
            }
            return $response;
        }
        protected function setDBData( string $type, string $t, array $f, array $v, string $p = '' ) {
            if ( $type == 'insert' ) {
                $query_request = "INSERT INTO $t (";
                $c1 = count($f) - 1;
                $c2 = count($v) - 1;
                for ($x = 0; $x < count($f); $x++ ) {
                    if( $x < $c1 ) {
                        $query_request .= $f[$x] . ", ";
                    } else {
                        $query_request .= $f[$x];
                    }
                }
                $query_request .= ") VALUES (";
                for ($x = 0; $x < count($v); $x++ ) {
                    if ($x < $c2 ) {
                        $query_request .= "'$v[$x]', ";
                    } else {
                        $query_request .= "'$v[$x]'";
                    }
                }
                $query_request .= ");";
                $result = $this->getDBResponse($query_request, 'insert');
            } else/* if ( $type == 'update' ) */ {
                // UPDATE tabla SET field = value WHERE param;
                $query_request = "UPDATE $t SET ";
                $c1 = count($f) - 1;
                for ($x = 0; $x < count($f); $x++ ) {
                    if( $x < $c1 ) {
                        $query_request .= "$f[$x] = $v[$x], ";
                    } else {
                        $query_request .= "$f[$x] = $v[$x]";
                    }
                }
                $query_request .= " WHERE ";
                $pspt = preg_split('/([,|;|~])/',$p,NULL,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                //var_dump($pspt);
                $y = 1;
                foreach ( $pspt as $ps ) {
                    if ( $ps == "," ) {
                        $query_request .= " AND ";
                    } elseif ( $ps == ";" ) {
                        $query_request .= " OR ";
                    } elseif ( $ps == "~" ) {
                        $query_request .= " LIKE ";
                    } else {
                        $query_request .= "$ps";
                    }
                }
                $query_request .= " ;";
                $result = $this->getDBResponse($query_request, $type);
            } /* elseif ( $type == 'delete' ) {
                $query_request = "UPDATE $t SET ";
                $c1 = count($f) - 1;
                for ($x = 0; $x < count($f); $x++ ) {
                    if( $x < $c1 ) {
                        $query_request .= "$f[$x] = $v[$x], ";
                    } else {
                        $query_request .= "$f[$x] = $v[$x]";
                    }
                }
                $query_request = " WHERE ";
                $pspt = preg_split('/([,|;])/',$p,NULL,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                $y = 0;
                foreach ( $pspt as $ps) {
                    if ( $ps != "," || $ps != ';' ) {
                        $query_request .= "$t.$ps";
                    } elseif ( $ps == "," ) {
                        $query_request .= "$t.$ps AND ";
                    } else {
                        $query_request .= "$t.$ps OR ";
                    }
                }
                $query_request .= " ;";
                //$result = $this->getDBResponse($query_request, 'update');
            } */
            if ( $result['error']['code'] != 0 ) {
                $response = [
                    'data'=>[
                        'rows' => array(),
                        'affected' => array(),
                        'id_row' => '',
                    ],
                    'error' => $result['error'],
                    'message'=> $result['message']
                ];
            } else {
                $response = [
                    'data' => [
                        'rows' => $result['rows_registered'],
                        'affected' => $result['rows_affected'],
                        'id_row' => $result['row_id'],
                    ],
                    'error' => $result['error'],
                    'message' => $result['message'],
                ];
            }
            return $response;
        }
        protected function getDBCustomData( string $type, string $t, array $f, array $v, array $p, int $l, string $s ) {
            if ( $type == "insert" ) {
                $query_request = "INSERT INTO $t (";
                $c1 = count($f) - 1;
                $c2 = count($v) - 1;
                for ($x = 0; $x < count($f); $x++ ) {
                    if( $x < $c1 ) {
                        $query_request .= $f[$x] . ", ";
                    } else {
                        $query_request .= $f[$x];
                    }
                }
                $query_request .= ") VALUES (";
                for ($x = 0; $x < count($v); $x++ ) {
                    if ($x < $c2 ) {
                        $query_request .= "$v[$x], ";
                    } else {
                        $query_request .= $v[$x];
                    }
                }
                $query_request .= ");";
                $result = $this->getDBResponse($query_request, $type);
            } elseif ( $type == "update" || $type == "delete" ) {
                $query_request = "UPDATE $t SET ";
                $c1 = count($f) - 1;
                for ($x = 0; $x < count($f); $x++ ) {
                    if( $x < $c1 ) {
                        $query_request .= "$f[$x] = $v[$x], ";
                    } else {
                        $query_request .= "$f[$x] = $v[$x]";
                    }
                }
                $query_request .= " WHERE ";
                foreach ( $p as $prm ) {
                    $query_request .= "$prm ";
                }
                $result = $this->getDBResponse($query_request, $type);
            } else {
                $query_request = "SELECT $f FROM $t";
                if ( !empty($v['inners']) ) {
                    foreach ( $v['inners'] as $in ) {
                        $query_request .= " INNER $in[innerType] `$in[innerTable]` ON `$in[innerTable]`.`$in[innerFilter]` = $t.`$in[innerCompare]`";
                    }
                    $query_request .= " WHERE ";/* 
                }
                if ( !empty($v['inners']) ) { */
                    foreach ( $p as $prm ) {
                        $query_request .= " $prm ";
                    }
                }
                $query_request .= " LIMIT " . $l . " ";
                $query_request .= $s . " ;";
                $result = $this->getDBResponse($query_request, $type);
            }
            if ( $result['error']['code'] != 0 ) {
                $response = [
                    'data'=>array(),
                    'error' => $result['error'],
                    'message'=> $result['message']
                ];
            } else {
                $response = [
                    'data' => [
                        'rows' => $result['rows_registered'],
                        'affected' => $result['rows_affected'],
                        'id_row' => $result['lastId'],
                    ],
                    'error' => $result['error'],
                    'message' => $result['message'],
                ];
            }
            return $response;
        }
    /*
        private $context;

        protected function getResolveQuery(array $queryArray) {
            try {
                if ( isset($queryArray) && !empty($queryArray) ) {
                    $querySTR =  explode(' ',$queryArray['query']);
                    $strType = strtolower($querySTR[0]);
                    if ( $queryArray['data'] != '' ) {
                        $query = $queryArray['query'] . $queryArray['data'];
                    } else {
                        $query = $queryArray['query'];
                    }
                    //$sanitizedQuery = $this->sanitizer($query['data']);
                    if ( $strType == 'select' ) {
                        $queryResponse = $this->getDBResponse($query, $strType);
                        if ( $queryResponse['error']['code'] == '00000' && $queryResponse['message'] == 'success' ) {
                            $response = [
                                'data' => [
                                    'rows' => $queryResponse['rows_registered'],
                                    'id_num' => $queryResponse['row_id'],
                                    'affected' => $queryResponse['rows_affected']
                                ],
                                'message' => $queryResponse['message'],
                                'error' => $queryResponse['error']
                            ];
                        } else {
                            throw new Exception("Error 00003", 1);
                        }
                    } elseif ( $strType == 'insert' ) {
                        $queryResponse = $this->getDBResponse($query, $strType);
                        if ($queryResponse['rows_affected'] > 0 && $queryResponse['error']['message'] == '00000') {
                            $response = [
                                'data' => [
                                    'rows' => $queryResponse['rows_registered'],
                                    'id_num' => $queryResponse['row_id'],
                                    'affected' => $queryResponse['rows_affected']
                                ],
                                'message' => $queryResponse['message'],
                                'error' => $queryResponse['error']
                            ];
                        } else {
                            throw new Exception("Error 00003", 1);
                        }
                    } elseif ( $strType == 'update' ) {
                        $queryResponse = $this->getDBResponse($query, $strType);
                        if ($queryResponse['rows_affected'] > 0 && $queryResponse['error']['code'] == '00000' ) {
                            $response = [
                                'data' => [
                                    'rows' => $queryResponse['rows_registered'],
                                    'id_num' => $queryResponse['row_id'],
                                    'affected' => $queryResponse['rows_affected']
                                ],
                                'message' => $queryResponse['message'],
                                'error' => $queryResponse['error']
                            ];
                        } else {
                            throw new Exception("Error 00003", 1);
                        }
                    } elseif ( $strType == 'delete' ) {
                        $queryResponse = $this->getDBResponse($query, $strType);
                        if ( $queryResponse['rows_affected'] > 0 && $queryResponse['error']['code'] == '00000' ) {
                            $response = [
                                'data' => [
                                    'rows' => $queryResponse['rows_registered'],
                                    'id_num' => $queryResponse['row_id'],
                                    'affected' => $queryResponse['rows_affected']
                                ],
                                'message' => $queryResponse['message'],
                                'error' => $queryResponse['error']
                            ];
                        } else {
                            throw new Exception("Error 00003", 1);
                        }
                    } else {
                        throw new Exception("Error 00002", 1);
                    }
                } else {
                    throw new Exception("Error 00001", 1);
                }
            } catch (Exception $ex) {
                $exceptional = $ex;
                $response['message'] = 'Error: Request error.\n';
                $response['error'] = [
                    'code' => '00001',
                    'driver' => 1,
                    'message' => 'Error: Processing Request\n' . $ex->getMessage() . ' Line: ' . $ex->getLine()
                ];
                $response['data'] = array();
            }
            return $response;
        }
        public function sanitizer(string $query) {
            $conexion = new PDO("mysql:dbname=sigpma_db;host=localhost:3307;charset=utf8", 'root', '');
            $sanitizedQuery = '';
            if(is_string($query)){
                $string = strtolower($query);
                $sanitizedQuery = $conexion->quote($query);
            }
            return $sanitizedQuery;
        }
    */
    }
    /* $model = new Model;
    $fields = "'animal','familly','name','owner','doctor'";
    $tabla = "doctor";
    $data = [
        'fields'=>$fields,
        'inners'=>[
            ['innerType'=>"JOIN",'innerTable'=>"owner",'innerFilter'=>"pet_id",'innerCompare'=>"petOwner"],
            ['innerType'=>"JOIN",'innerTable'=>"doctor",'innerFilter'=>"doctor_id",'innerCompare'=>"petOwner"],
        ],
        'params'=>[
            'WHERE animals.pet_Name="chico"',
            'AND owner.owner_Name="Joel"',
        ],
    ];
    echo $model->select("animals",$data,50,"ORDER BY animals.id");
    echo "<br><hr>";
    $data = [
        'fields' => array('doctor_Name','doctor_Tel','doctor_dir'),
        'values' => array('Jose Raul','4356345','SALA40'),
        'params' => "",
    ];
    echo $model->insert($tabla,$data);
    echo "<br><hr>";
    //update doctor set 'doctor.doctor_Name'='Jose Ramos','doctor.doctor_Tel'='21545','doctor.doctor_dir'='SALA05' where doctor_Name'='Jose Raul' OR doctor_Name'='Raul Jose';
    $data = [
        'fields' => array('doctor_Name','doctor_Tel','doctor_dir'),
        'values' => array('Jose Ramos','21545','SALA05'),
        'params' => "`doctor_Name`='Jose Raul';`doctor_Name`='Raul Jose'",
    ];
    echo $model->edit($tabla,$data);
    echo "<br><hr>";
    $data = [
        'fields' => array('doctor_Name','doctor_Tel','doctor_dir'),
        'values' => array('Jose Ramos','21545','SALA05'),
        'params' => "`doctor_Name`='Jose Raul',`doctor_tel`='4356345'",
    ];
    echo $model->delete($tabla,$data);
    echo "<br><hr>";
    $data = [
        'fields' => array('doctor_Name','doctor_Tel','doctor_dir'),
        'values' => array('Jose Ramos','21545','SALA05'),
        'params' => "`doctor_Name`~'Jose Raul',`doctor_tel`='4356345'",
    ];
    echo $model->edit($tabla,$data);
    echo "<br><hr>";
    $str = preg_split('/[+|\/]/', "'doctor_Name=Jose Raul'+'doctor_Tel'+'doctor_dir'+'Jose Raul'/'4356345'/'SALA40'", NULL, PREG_SPLIT_DELIM_CAPTURE); */