<?php

session_start();
session_unset();
session_destroy();

header("location: http://localhost/mer_ecommerce/1home.php");

exit();
