function init_blocForums() {
  jQuery(".blocForums a.lien_pagination").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/forums/inc-forums"), "ajax_blocForums", init_blocForums);
        return true;
      }
      obj.href = unwrapUrl(obj.href, "composants/forums/inc-forums", pageUrl());
    }
  );
}

jQuery(document).ready(
  function() {
	  init_blocForums();
  }
);