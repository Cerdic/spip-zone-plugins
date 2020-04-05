<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function traitements_hashtags($str, $id_groupe=''){

	# Pattern hashtags!
	$pattern = "~#(!->|->|!)*(\\w|\\')+(?![^<]*[]>|[^<>]*<\\/)~u";

	if ( preg_match_all($pattern, $str, $matches) ) {

		$tagsBrutes = $matches[0];
		$tagsPropres = preg_replace('`#(!->|!|->)*`','',$tagsBrutes);

		# Clause where REGEXP... todo: avec like certainement plus rapide ou eventuellement avec in?
		$where = array(
			"titre REGEXP '" . addslashes(implode(array_unique($tagsPropres), '|')) . "'",
			"id_groupe=$id_groupe");

		$mots = array();
		if ( count($tagsPropres) AND $res = sql_allfetsel('id_mot, titre', 'spip_mots', $where) )
			foreach ($res as $r)
			   $mots[$r['titre']] = $r['id_mot'];

		$patterns = array();
		foreach ($tagsBrutes as $v)
			$patterns[] = '/' . $v .  '(?![\w\d])+(?![^<]*[]>|[^<>]*<\\/)/';

		$replacements = array();
		foreach ( $tagsPropres as $k => $v )
			if ( $mots[$v] AND $matches[1][$k] === "!->" )
				$replacements[] = "<strong><a href='" . generer_url_entite($mots[$v], 'mot') . "'>" . $v . "</a></strong>";
			elseif ( $mots[$v] AND $matches[1][$k] === "->" )
				$replacements[] = "<a href='" . generer_url_entite($mots[$v], 'mot') . "'>" . $v . "</a>";
			elseif ( $mots[$v] AND $matches[1][$k] === "!" )
				$replacements[] = "<strong class=\"hashtag\" data-id-mot=\"$mots[$v]\">" . $v . "</strong>";
			elseif ( $mots[$v] )
				$replacements[] = "<span class=\"hashtag\" data-id-mot=\"$mots[$v]\">" . $v . "</span>";
			else
				$replacements[] = $v;

		return preg_replace($patterns,$replacements,$str);
	}

	return $str;
}

function nettoyer_raccourcis_hashtags($flux,$option=""){
	return preg_replace("~#(&nbsp;| )?(!->|->|!)?~u",$option,$flux);
}