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
                return $helper->requestResolver();
            }
        }
        function requireObject (string $type, string $values) {
            $helper = new RequirerHelper;
            return $helper->requerirObjecto($type,$values);
        }
    }
    $app = new Loader;
    function requerir (string $type, string $name) {
        $app = new Loader;
        $app->requireObject($type,$name);
    }
    echo $app->request();
?>