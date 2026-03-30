<?php
require_once 'c:/xampp/htdocs/dsm/vendor/autoload.php';
define('SITE_URL', 'http://localhost/dsm');
require_once 'c:/xampp/htdocs/dsm/config/database.php';
require_once 'c:/xampp/htdocs/dsm/config/router.php';
require_once 'c:/xampp/htdocs/dsm/config/helpers.php';
$id = 1;
ob_start();
include 'c:/xampp/htdocs/dsm/pages/news/detail.php';
$out = ob_get_clean();
echo $out;
?>
