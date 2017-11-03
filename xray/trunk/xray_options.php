<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (isset($_GET['exec']) and ($_GET['exec']=='xray')) {
	include_once ('xray_apc.php');
	exit;
}

