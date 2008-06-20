function init_derniers_commentaires() {
  jQuery(".derniersCommentaires a.lien_pagination").each(
    function(i, obj) {
      obj.onclick = function(e) {
        e.preventDefault();
        charger_id_url(wrapUrl(obj.href, "composants/derniers_commentaires/inc-derniers_commentaires"), "ajax_derniersCommentaires", init_derniers_commentaires);
        return true;
      }
      obj.href = unwrapUrl(obj.href, "composants/derniers_commentaires/inc-derniers_commentaires", pageUrl());
    }
  );
}

jQuery(document).ready(
  function() {
    init_derniers_commentaires();
  }
);