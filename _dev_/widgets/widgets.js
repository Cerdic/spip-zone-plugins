/*
 *  widgets.js (c) Fil & Toggg 2006 -- licence GPL
 */

// le prototype configuration de Widgets
function configWidgets(options)
{
    for (opt in options) {
        this[opt] = options[opt];
    }
}

// ouvre un widget
$.fn.openwidget = function() {
  return this
  .each(function(){
    // verifier que je suis un widget
    if (!$(this).is('.widget'))
      return;

    // voir si je dispose deja du widget comme voisin
    if ($(this).is('.widget-has')) {
      $(this)
      .hide()
      .next()
        .show();
    }
    // sinon charger le formulaire
    else {
      $(this)
      .append(SEARCHING); // icone d'attente
      var me=this;
      $.getJSON(url_widgets_html,
        {
          'w': $(this).width(),
          'h': $(this).height(),
          'wh': window.innerHeight,
          'em': $(this).css('fontSize'),
          'class': me.className
        },
        function (c) {
          $(me)
          .find("img.widget-searching")
            .remove();
          if (c.$erreur) {
            alert(c.$erreur);
            return false;
          }
          $(me)
          .hide()
          .addClass('widget-has')
          .after('<div>'+c.$html+'</div>')
          .next()
            .activatewidget();
        }
      );
    }
  });
}

// annule le widget ouvert (fonction destructive)
$.fn.cancelwidget = function() {
  return this.prev()
  .filter('.widget-has')
  .show()
  .removeClass('widget-has')
  .removeClass('widget-changed')
  .next()
    .remove();
}

// masque le widget ouvert
$.fn.hidewidget = function() {
  return this
  .filter('.widget-has')
  .show()
  .next()
    .hide()
    .removeClass('widget-hover');
}

// active un widget qui vient d'etre charge
$.fn.activatewidget = function() {
  return this
  .click(function(e){
    e.stopPropagation();
  })
  .each(function(){
    var me = this;
    var w,h;
    $(me)
    .find('form')
      .ajaxForm(function(d){
        $(me)
          .find("img.widget-searching")
            .remove();
        eval('d=' + d.responseText + ';');
        if (d.$erreur > '') {
          alert(d.$erreur);
          if (d.$annuler) {
            $(me)
            .cancelwidget();
          } else {
            $(me)
            .find(".widget-boutons")
              .show(); // boutons de validation
          }
          return false;
        }
        $(me)
        .prev()
          .html(
            d[$('form', me).find('.widget-id').val()]
          )
          .prepend(ICONCLICK);
        $(me)
          .cancelwidget();
      }).onesubmit(function(){
        $(this)
        .append(SEARCHING) // icone d'attente
        .find(".widget-boutons")
          .hide(); // boutons de validation
      }).keyup(function(){
        $(this)
        .find(".widget-boutons")
          .show();
        $(me)
        .prev()
          .addClass('widget-changed');
      })
      .find(".widget-active")
        .css({
            'fontSize': $(me).prev().css('fontSize'),
            'fontFamily': $(me).prev().css('fontFamily'),
            'fontWeight': $(me).prev().css('fontWeight'),
            'lineHeight': $(me).prev().css('lineHeight'),
            'color': $(me).prev().css('color'),
            'backgroundColor': $(me).prev().css('backgroundColor')
        })
        .each(function(n){
          if (n==0)
            this.focus();
        })
        .keypress(function(e){
          if (e.keyCode == 27) {
            $(me)
            .cancelwidget();
          }
        })
      .end()
      .find(".widget-submit")
        .click(function(e){
          e.stopPropagation();
          $(this)
          .ancestors("form")
          .submit();
        })
      .end()
      .find(".widget-cancel")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .cancelwidget();
        })
      .end()
      .find(".widget-hide")
        .click(function(e){
          e.stopPropagation();
          $(me)
          .prev()
          .hidewidget();
        })
      .end()
    .end();
  });
}

// initialise les widgets (cree le clone actif)
$.fn.initwidget = function(){
  this
  .addClass('widget-autorise')
  .prepend(ICONCLICK)
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    e.stopPropagation();
    $(this).openwidget();
  });

  // :hover pour MSIE
  if (jQuery.browser.msie) {
    this.hover(
      function(){
        $(this).addClass('widget-hover');
      },function(){
        $(this).removeClass('widget-hover');
      }
    );
  }

  return this;
}

// demarrage
$(document).ready(function() {
  if (!configWidgets.droits) return;
  if (!jQuery.getJSON) return; // jquery >= 1.0.2
  url_widgets_html = 'spip.php?action=widgets_html';
  SEARCHING = "<img class='widget-searching' src='" + configWidgets.imgPath + "/searching.gif' style='float:right;' />";
  ICONCLICK = "<span style='float:right;z-index:100;'><img class='widget-edit' onclick='event.stopPropagation();$(this).parent().parent().openwidget();' src='" + configWidgets.imgPath + "/crayon.png' title='" + configWidgets.txtEditer + "' /><img class='widget-img-changed' src='" + configWidgets.imgPath + "/changed.png' title='" + configWidgets.txtChanged + "' /></span>";

  // sortie, demander pour sauvegarde si oubli
  $(window).unload(function(e) {
    var chg = $(".widget-changed");
    if (chg.length && confirm("Sauvegarder les modifications ?")) {
      chg.next().find('form').submit();
    }
  });

  $(".widget")
  .filter(configWidgets.droits)
  .initwidget();

  // fermer tous les widgets ouverts
  $("html")
  .click(function() {
    $(".widget.widget-has:hidden")
    .hidewidget();
  });
});

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}

