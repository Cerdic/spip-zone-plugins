<?	
function jeux_install($install){
	switch($install){
		case 'install' :
			jeux_verifier_base();
			echo "spip";
			break;
				};	
	}
function jeux_verifier_base(){
	include_spip('base/jeux_tables');
	include_spip('base/create');
	include_spip('base/abstract_sql');
	creer_base();	
	}
	
?>