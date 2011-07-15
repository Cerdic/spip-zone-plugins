<?php


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	header('Location: '
		   . htmlspecialchars(sinon($GLOBALS['meta']['adresse_site'],'.'))
		   . '/?page=auteurs_supprimer'
		   );
	exit;