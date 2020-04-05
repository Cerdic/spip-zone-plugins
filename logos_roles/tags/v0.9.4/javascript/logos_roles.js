/* jshint strict: true, undef: true, unused: true, curly: true,
   eqeqeq: true, freeze: true, funcscope: true, futurehostile: true,
   nonbsp: true */
/* globals $, onAjaxLoad */

$(function () {
    "use strict";

    function labels_formulaire_editer_logo() {

        var labels = $('.formulaire_editer_logo .apercu label');

        labels
            .wrapInner('<a href="#"></a>')
            .next().hide();

        labels.find('a').click(function (e) {
            e.preventDefault();

            $(e.target)
                .contents().unwrap()
                .parent('label').next().show('fast');
        });
    }

    labels_formulaire_editer_logo();
    onAjaxLoad(labels_formulaire_editer_logo);
});
