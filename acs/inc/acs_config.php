<?php
#              ACS
#          (Plugin Spip)
#     http://acs.geomaticien.org
#
# Copyright Daniel FAIVRE, 2007-2011
# Copyleft: licence GPL - Cf. LICENCES.txt

include_spip('inc/actions');
include_spip('inc/acs_version');
include_spip('inc/acs_presentation');

function inc_acs_config() {
  global $spip_lang_right;
  
  $r = '<input type="hidden" name="changer_config" value="oui">
    <table>
      <tr>
        <td>
          <input name="ACS_PREVIEW_BACKGROUND" type="text" class="palette forml" style="width:60px;" value="'.$GLOBALS['meta']['ACS_PREVIEW_BACKGROUND'].'" /></td><td>&nbsp;</td><td>'._T('acs:preview_background').'</td><td>'.acs_help_call('help_ACS_PREVIEW_BACKGROUND').'
        </td>
      </tr>
    </table>'.
  acs_help_div('help_ACS_PREVIEW_BACKGROUND', _T('acs:preview_background_help')).'
  <input name="ACS_VOIR_ONGLET_VARS" type="checkbox"'.($GLOBALS['meta']['ACS_VOIR_ONGLET_VARS'] ? ' checked' : '').' />'.
  _T('acs:voir_onglet_vars').acs_help_call('help_voir_onglet_vars').acs_help_div('help_voir_onglet_vars', _T('acs:voir_onglet_vars_help')).
  '<br />
  <input name="ACS_VOIR_PAGES_COMPOSANTS" type="checkbox"'.($GLOBALS['meta']['ACS_VOIR_PAGES_COMPOSANTS'] ? ' checked' : '').' />'.
  _T('acs:voir_pages_composants').acs_help_call('help_ACS_VOIR_PAGES_COMPOSANTS').acs_help_div('help_ACS_VOIR_PAGES_COMPOSANTS', _T('acs:voir_pages_composants_help')).
  '<br />
  <input name="ACS_VOIR_PAGES_PREVIEW" type="checkbox"'.($GLOBALS['meta']['ACS_VOIR_PAGES_PREVIEW'] ? ' checked' : '').' />'
  ._T('acs:voir_pages_preview_composants').acs_help_call('help_ACS_VOIR_PAGES_PREVIEW').acs_help_div('help_ACS_VOIR_PAGES_PREVIEW', _T('acs:voir_pages_preview_composants_help')).
  '<hr /> 
  <table>
    <tr>
      <td><input name="ACS_SPIP_ADMIN_FORM_STYLE" type="text" class="forml" value="'.$GLOBALS['meta']['ACS_SPIP_ADMIN_FORM_STYLE'].'" /></td>
      <td>&nbsp;</td><td>'._T('acs:spip_admin_form_style').'</td>
      <td>'.acs_help_call('help_ACS_SPIP_ADMIN_FORM_STYLE').'</td>
    </tr>
  </table>'.acs_help_div('help_ACS_SPIP_ADMIN_FORM_STYLE', _T('acs:spip_admin_form_style_help')).
  '<input name="ACS_CACHE_SPIP_OFF" type="checkbox"'.($GLOBALS['meta']['ACS_CACHE_SPIP_OFF'] ? ' checked' : '').' />'
  .($GLOBALS['meta']['ACS_CACHE_SPIP_OFF'] ? '<span class="alert" style="text-decoration:blink">'._T('acs:cache-spip_off').'</span>' : _T('acs:cache-spip_on')).acs_help_call('help_ACS_CACHE_SPIP_OFF').acs_help_div('help_ACS_CACHE_SPIP_OFF', _T('acs:cache-spip_help')).'
  <br /><br />
  <hr />'.
  _T('acs:acsDerniereModif').' '.date("Y-m-d H:i:s", lire_meta("acsDerniereModif")).
  '<hr /><br />'.
  _T('version').' <a style="color: black">ACS '.acs_version().'</a> '.(acs_release() ? '('.acs_release().')' : '').
  '<br />'.
  _T('acs:documentation').': <a href="http://acs.geomaticien.org" target="_new"><img src="'._DIR_PLUGIN_ACS.'images/acs_32x32_help.gif" alt="?" style="vertical-align: middle"/></a><br />';

  return ajax_action_post('acs_config', 0, 'acs', 'onglet=adm', $r, _T('acs:valider'), 'class="fondo visible" id="valider_acs_config"', ' style="float: '.$spip_lang_right.';"');
}
?>