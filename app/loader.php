<?php
    class Loader {
        function __Construct () {
            require_once "app/core/helpers/H_definer_.helper.php";
            require_once "app/core/helpers/H_requirer_.helper.php";
        }
        function request () {
            $helper = new RequirerHelper;
            if (!empty($_POST)) {
                return $helper->initPost();
            } elseif (!empty($_GET)) {
                return $helper->initGet();
            } else {
                return $helper->initDefault();
            }
        }
        function requireObject (array $values) {
            $helper = new RequirerHelper;
            return $helper->requerirObjecto($values['type'],$values['name']);
        }
    }
    $app = new Loader;
    function requerir (string $value) {
        $obj = explode($value);
        $app->requireObject($obj);
    }
    $app->request();
?>