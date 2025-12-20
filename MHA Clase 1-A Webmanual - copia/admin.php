<?php
require 'db.php';
session_start();
if (($_SESSION['tipousuario']) != "admin"):
    header("location:index.php");
endif;

?>
<select nombre, id, mail, tipousuario>
    