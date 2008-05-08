<?
function mao_affiche_milieu($flux){

	if ($flux['args']['exec']=='mots_edit'){
		$id_mot = $flux['args']['id_mot'];
		$id_groupe = sql_getfetsel('id_groupe','spip_mots','id_mot='.intval($id_mot));
		$page = evaluer_fond("prive/contenu/mot", array('id_mot'=>$id_mot,'id_groupe'=>$id_groupe), $connect);
		
		$flux['data'] .= $page['texte'];
	}
	return $flux;
}
