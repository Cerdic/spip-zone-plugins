/*
 *  widgets.js (c) Fil , toggg 2006 -- licence GPL
 */

// le prototype configuration de Widgets
function cfgWidgets(options)
{
  this.url_widgets_html = 'spip.php?action=widgets_html';
  this.img = {
    'searching':{'file':'searching.gif','txt':'En attente du serveur ...'},
    'edit':{'file':'crayon.png','txt':'Editer'},
    'img-changed':{'file':'changed.png','txt':'Deja modifie'}
  };
  this.txt = {
  };
  for (opt in options) {
    this[opt] = options[opt];
  }
}
cfgWidgets.prototype.mkimg = function(what) {
  return '<img class="widget-' + what +
    '" src="' + this.imgPath + '/' + this.img[what].file +
    '" title="' + this.img[what].txt + '" />';
}
cfgWidgets.prototype.iconclick = function(c) {

  // le + qui passe en prive pour editer tout si classe type--id
  var link = c.match(/\b(\w+)--(\d+)\b/);
  link = link ? 
    '<a href="ecrire/?exec=' + link[1] + 's_edit&id_' + link[1] + '=' + link[2] +
    '">' + this.mkimg('edit') + '</a><br />' : '';

  var cray = c.match(/\b\w+-\w+-\d+\b/);
  cray = !cray ? '' : this.mkimg('crayon') + '<br />';

  return "<span class='widget-icones'><span>" +
      cray + link +
      this.mkimg('img-changed') +
    "</span></span>";
}

function entity2unicode(txt)
{
  var reg = txt.split(/&#(\d+);/i);
  for (var i = 1; i < reg.length; i+=2) {
  	reg[i] = String.fromCharCode(parseInt(reg[i]));
  }
  return reg.join('');
}

function uniAlert(txt)
{
  alert(entity2unicode(txt));
}

function uniConfirm(txt)
{
  return confirm(entity2unicode(txt));
}

// ouvre un widget
$.fn.openwidget = function(evt) {
  if (evt.stopPropagation) {
    evt.stopPropagation();
  }
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
      .append(configWidgets.mkimg('searching')); // icone d'attente
      var me=this;
      $.getJSON(configWidgets.url_widgets_html,
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
            uniAlert(c.$erreur);
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
      .ajaxForm({"dataType":"json",
			"after":function(d){
        $(me)
          .find("img.widget-searching")
            .remove();
        if (d.$erreur > '') {
          if (d.$annuler) {
            if (d.$erreur > ' ') {
              uniAlert(d.$erreur);
            }
            $(me)
              .cancelwidget();
          } else {
              uniAlert(d.$erreur+'\n'+configWidgets.txt.error);
              $(me)
                .find(".widget-boutons")
                  .show(); // boutons de validation
          }
          return false;
        }

        $(me)
        .prev()
          .html(
            d[$('input.widget-id', me).val()]
          )
          .iconewidget();
        $(me)
          .cancelwidget();
      }}).onesubmit(function(){
        $(this)
        .append(configWidgets.mkimg('searching')) // icone d'attente
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
          .ancestors("form").eq(0)
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
      .each(function(){
        // rendre les boutons visibles (cf. plugin jquery/dimensions.js)
        var buttonpos = (this.offsetTop || 0) + $(this).height();
        var scrolltop = window.pageYOffset ||
          jQuery.boxModel && document.documentElement.scrollTop  ||
          document.body.scrollTop || 0;
        var scrollleft = window.pageXOffset || 
          jQuery.boxModel && document.documentElement.scrollLeft ||
          document.body.scrollLeft || 0;
        var h = window.innerHeight;
        if (buttonpos - h + 20 > scrolltop) {
          window.scrollTo(scrollleft, buttonpos - h + 30);
        }
      })
    .end();
  });
}

// insere les icones dans l'element
$.fn.iconewidget = function(){
  return this.each(function() {
    $(this).prepend(configWidgets.iconclick(this.className))
    .find('.widget-crayon') // le crayon a clicker lui-meme
      .click(function(e){
        $(this).ancestors('.widget').eq(0).openwidget(e);
      });
    });
}

// initialise les widgets (cree le clone actif)
$.fn.initwidget = function(){
  this
  .addClass('widget-autorise')
  .click(function(e){
    e.stopPropagation();
  })
  .dblclick(function(e){
    $(this).openwidget(e);
  })
  .iconewidget();

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

  // sortie, demander pour sauvegarde si oubli
  if (configWidgets.txt.sauvegarder) {
    $(window).unload(function(e) {
      var chg = $(".widget-changed");
      if (chg.length && uniConfirm(configWidgets.txt.sauvegarder)) {
        chg.next().find('form').submit();
      }
    });
  }

  // .filter(array) fonctionne mal (jQuery 1.0.3), on le fait a la main
  // $(".widget").filter(configWidgets.droits).initwidget();
  $($.grep(
    $(".widget"),
    function(e) {
      for (var i=0; i<configWidgets.droits.length; i++) {
        if ($(e).is(configWidgets.droits[i])) return true;
      }
      return false;
    }
  )).initwidget();

  // fermer tous les widgets ouverts
  $("html")
  .click(function() {
    $(".widget.widget-has:hidden")
    .hidewidget();
  });
});

function dump(elt)
{var txt=''; for (var prop in elt) {txt += prop+'='+elt[prop]+'/';} alert(txt);}

