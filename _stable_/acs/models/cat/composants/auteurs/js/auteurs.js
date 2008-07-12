function init_auteurs() {
  jQuery(".auteurs a.lien_pagination").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/auteurs/inc-auteurs"), "ajax_auteurs", init_auteurs);
        return true;
      }
      obj.href = unwrapUrl(obj.href, "composants/auteurs/inc-auteurs", pageUrl());
    }
  );
}

jQuery(document).ready(
  function() {
    init_auteurs();
  }
);
