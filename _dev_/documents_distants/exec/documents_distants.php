<?

function exec_documents_distants(){
	
	$commencer_page= charger_fonction('commencer_page', 'inc');
	importer_document($documents_distants,$type_lien,$id);
	
	echo $commencer_page($titre=_T('documentsdistants:importer'));
	
	echo gros_titre(_T('documentsdistants:importer'));
	debut_gauche();
	debut_droite();
	debut_cadre_formulaire();
	$texte="<form method='post' name='documents_distants' action='".generer_url_ecrire('documents_distants')."'>
	<label for='document'>"._T('documentsdistants:explicatif')."
	</label>
	<textarea name='documents' rows='40' cols='80'></textarea>
	<br />
	<label>"._T("documentsdistants:attribuer")."
	
	<select name='type_lien'>
		<option value='article'>"._T('info_article')."</option>
		<option valure'rubrique'>"._T('info_rubriques')."</option>
		</select> 
	
	</label><label>"._T('id')."
	<input type='texte' name='id' /></label>
	<input type='submit' value='"._T('bouton_enregistrer')."'>
	</form>";
	echo $texte;
	fin_cadre_formulaire();
	
	}

function importer_document($documents_distants,$type_lien,$id)
	{
	
	}

?>