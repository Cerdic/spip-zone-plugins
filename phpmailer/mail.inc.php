<?php

	include_spip('class.phpmailer');
	include_spip('class.smtp');

	// Definir sa fonction perso pour surcharger la fonction par defaut
	// ensuite appeller la fonction via include_spip('mail.inc')

	class MyMailer extends PHPMailer {
    // Set default variables for all new objects
    var $From     = "from@email.com";
    var $FromName = "Mailer";
    var $Host     = "smtp1.site.com;smtp2.site.com";
    var $Mailer   = "smtp";                         // Alternative to IsSMTP()
    var $WordWrap = 75;

    // Replace the default error_handler
    function error_handler($msg) {
        print("My Site Error");
        print("Description:");
        printf("%s", $msg);
        exit;
    }

    // Create an additional function
    function do_something($something) {
        // Place your new code here
    }
}


?>