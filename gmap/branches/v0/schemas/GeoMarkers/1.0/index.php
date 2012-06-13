<?php

	$schema = './geomarkers.xsd';
	header('Content-Type: text/xml');
	header('Content-Length: '.filesize('./'.$schema));
	header('Pragma: public');
	readfile('./'.$schema);


?>