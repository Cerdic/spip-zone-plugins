<?php
/*
 * Google Maps in SPIP plugin
 * Insertion de carte Google Maps sur les éléments SPIP
 *
 * Auteur :
 * Fabrice ALBERT
 * (c) 2009 - licence GNU/GPL
 *
 * Page de paramétrage du plugin
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres'); // pour entites_html
include_spip('inc/gmap_presentation');
include_spip('inc/gmap_config_utils');
include_spip('inc/gmap_db_utils');

//
// Définition des types de marqueurs
//

function configuration_markers_dist()
{
	$corps = "";
	
	// Si on a le résultat d'un traitement, l'afficher ici
	$corps .= gmap_decode_result("msg_result");

	// Créer le code HTML
	$types = gmap_get_all_types();
	$corps .= '
	<table class="edit_types" id="edit_types">
		<tbody>
			<tr class="header"><th>'._T('gmap:edit_types_objet').'</th><th>'._T('gmap:edit_types_nom').'</th><th>'._T('gmap:edit_types_descriptif').'</th><th>'._T('gmap:edit_types_visible').'</th><th>'._T('gmap:edit_types_priorite').'</th><th>'._T('gmap:edit_types_usage').'</th><th>&nbsp;</th></tr>
			<tr class="hidden" id="edit_types_template">
				<td>
					<select name="objet[]" class="objet" size="1">
						<option value="" selected="selected">'._T('gmap:edit_types_objet_tous').'</option>
						<option value="rubrique">'._T('gmap:edit_types_objet_rubrique').'</option>
						<option value="article">'._T('gmap:edit_types_objet_article').'</option>
						<option value="breve">'._T('gmap:edit_types_objet_breve').'</option>
						<option value="document">'._T('gmap:edit_types_objet_document').'</option>
						<option value="mot">'._T('gmap:edit_types_objet_mot').'</option>
						<option value="auteur">'._T('gmap:edit_types_objet_auteur').'</option>
					</select>
				</td>
				<td><input type="text" name="nom_0" class="nom" value="nom"></td>
				<td><input type="text" name="descriptif_0" class="descriptif" value="descriptif"></td>
				<td>
					<select name=visible[]" class="visible" size="1">
						<option value="oui" selected="selected">'._T('gmap:oui').'</option>
						<option value="non">'._T('gmap:non').'</option>
					</select>
				</td>
				<td><input type="text" name="priorite[]" class="priorite" value="10"></td>
				<td>&nbsp;</td>
				<td><input type="hidden" name="id[]" class="id" value="template"><input type="hidden" name="oper[]" clas="oper" value="create"><span class="btn_delete" /></td>
			</tr>';
	foreach ($types as $type)
	{
		$nom = entites_html($type['nom']);
		$descriptif = entites_html($type['descriptif']);
		if (($nom == "defaut") || ($nom == "centre"))
			$corps .= '
			<tr class="type">
				<td><input type="hidden" name="objet[]" value="'.$type['objet'].'">'._T('gmap:edit_types_objet_tous').'</td>
				<td><input type="hidden" name="nom_'.$type['id'].'" value="'.$nom.'">'.$nom.'</td>
				<td><input type="text" name="descriptif_'.$type['id'].'" class="descriptif" value="'.$descriptif.'"></td>
				<td><input type="hidden" name="visible[]" value="'.$type['visible'].'">'.(($type['visible'] == "oui") ? _T('gmap:oui') : _T('gmap:non')).'</td>
				<td><input type="hidden" name="priorite[]" value="'.$type['priorite'].'">'.$type['priorite'].'</td>
				<td>'.(($type['nb_points'] > 0) ? $type['nb_points'] : '&nbsp;').'</td>
				<td><input type="hidden" name="id[]" class="id" value="'.$type['id'].'"><input type="hidden" name="oper[]" class="oper" value="update"></td>
			</tr>';
		else
			$corps .= '
			<tr class="type">
				<td>
					<select name="objet[]" class="objet" size)"1">
						<option value=""'.(($type['objet'] == "") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_tous').'</option>
						<option value="rubrique"'.(($type['objet'] == "rubrique") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_rubrique').'</option>
						<option value="article"'.(($type['objet'] == "article") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_article').'</option>
						<option value="breve"'.(($type['objet'] == "breve") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_breve').'</option>
						<option value="document"'.(($type['objet'] == "document") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_document').'</option>
						<option value="mot"'.(($type['objet'] == "mot") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_mot').'</option>
						<option value="auteur"'.(($type['objet'] == "auteur") ? ' selected="selected"' : '').'>'._T('gmap:edit_types_objet_auteur').'</option>
					</select>
				</td>
				<td><input type="text" name="nom_'.$type['id'].'" class="nom" value="'.$nom.'"></td>
				<td><input type="text" name="descriptif_'.$type['id'].'" class="descriptif" value="'.$descriptif.'"></td>
				<td>
					<select name=visible[]" class="visible" size="1">
						<option value="oui"'.(($type['visible'] == "oui") ? ' selected="selected"' : '').'>'._T('gmap:oui').'</option>
						<option value="non"'.(($type['visible'] == "non") ? ' selected="selected"' : '').'>'._T('gmap:non').'</option>
					</select>
				</td>
				<td><input type="text" name="priorite[]" class="priorite" value="'.$type['priorite'].'"></td>
				<td>'.(($type['nb_points'] > 0) ? $type['nb_points'] : '&nbsp;').'</td>
				<td><input type="hidden" name="id[]" class="id" value="'.$type['id'].'"><input type="hidden" name="oper[]" class="oper" value="update">'.(($type['nb_points'] == 0) ? '<span class="btn_delete" />' : '').'</td>
			</tr>';
	}
	$corps .= '
			<tr id="edit_types_actions">
				<td colspan="7"><a id="edit_types_new" href="#"><span class="btn_add_type" />&nbsp;'._T('gmap:edit_types_add_type').'</a></td>
			</tr>
		</tbody>
	</table>';
	
	// Ajouter le javascript pour manipuler tout ça
	$corps .= '
<script type="text/javascript">
//<![CDATA[
function EditType()
{
}
EditType.deleteRowHandler = function(event)
{
	event.preventDefault();
	var row = jQuery(this).closest("tr");
	row.addClass("hidden");
	var prevOper = jQuery("input.oper", row).val();
	if (prevOper == "create")
		jQuery("input.oper", row).val("noop");
	else
		jQuery("input.oper", row).val("delete");
};
EditType.newRowHandler = function(event)
{
	event.preventDefault();
	var container = jQuery(this).closest("table.edit_types");
	var numRows = jQuery("tr.type", container).length;
	var newRow = jQuery("#edit_types_template", container).clone();
	newRow.removeClass("hidden").addClass("type").removeAttr("id");
	var id = "new"+numRows;
	jQuery("input.id", newRow).val(id);
	jQuery("input.oper", newRow).val("create");
	jQuery("input.nom", newRow).attr("name", "nom_"+id);
	jQuery("input.descriptif", newRow).attr("name", "descriptif_"+id);
	jQuery(".btn_delete", newRow).click(EditType.deleteRowHandler);
	newRow.insertBefore(jQuery("#edit_types_actions", container));
};
jQuery(document).ready(function()
{
	var container = jQuery("#edit_types");
	jQuery(".btn_delete", container).click(EditType.deleteRowHandler);
	jQuery("#edit_types_new", container).click(EditType.newRowHandler);
});
//]]>
</script>'."\n";

	return gmap_formulaire_ajax('config_bloc_gmap', 'markers', 'configurer_gmap_ui', $corps,
		find_in_path('images/logo-config-markers.png'),
		_T('gmap:configuration_markers'));
}
?>
