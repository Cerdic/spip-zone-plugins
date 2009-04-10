function init_breves() {
  jQuery(".breves a.lien_pagination").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/breves/inc-breves"), "ajax_breves", init_breves);
        return true;
      }
      obj.href = unwrapUrl(obj.href, "composants/breves/inc-breves", pageUrl());
    }
  );
}

jQuery(document).ready(
  function() {
    init_breves();
  }
);
