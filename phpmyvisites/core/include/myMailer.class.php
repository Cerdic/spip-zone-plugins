<?php
/* 
 * phpMyVisites : website statistics and audience measurements
 * Copyright (C) 2002 - 2006
 * http://www.phpmyvisites.net/ 
 * phpMyVisites is free software (license GNU/GPL)
 * Authors : phpMyVisites team
*/

// $Id: myMailer.class.php,v 1.4 2005/11/20 15:02:35 matthieu_ Exp $


require_once INCLUDE_PATH . "/libs/phpmailer/class.phpmailer.php";

class MyMailer extends PHPMailer {

	var $From     = ADMINISTRATOR_MAIL;
    var $FromName = "phpMyVisites | web analytics";
    var $CharSet  = "UTF-8";
	var $Mailer   = "smtp";
    var $WordWrap = 75;

    // Replace the default error_handler
    function error_handler($msg) {
        print("My Site Error");
        print("Description:");
        printf("%s", $msg);
        exit;
    }
}
?>