<?php
    /**
     * Clase que resuelve las llamadas del usuario, analizando el post o get enviado por el controlador.
     * @category File
     * @author Snithfferx
     * @version 0.2.0
     */
    $JsonFile = file_get_contents(_MODULE_ . "config.json");
    if ( !empty($JsonFile) ) {
        /* "autor": "Bytes4Run",
        "descripcion": "Layout main page",
        "version": "",
        "language":"es",
        "skin":"login-page",
        "Company":"",
        "dbname":"",
        "dbpass":"" */
        $response = json_decode($JsonFile);
    }
