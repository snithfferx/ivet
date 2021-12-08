<?php
    class Loader {
        function __Construct () {
            require_once "app/core/helpers/H_definer_.helper.php";
            require_once "app/core/helpers/H_requirer_.helper.php";
        }
        function request () {
            $helper = new RequireHelper;
            if (!empty($_POST)) {
                return $helper->initPost();
            } elseif (!empty($_GET)) {
                return $helper->initGet();
            } else {
                return $helper->initDefault();
            }
        }
    }
    $app = new Loader;
    $app->request();
?>