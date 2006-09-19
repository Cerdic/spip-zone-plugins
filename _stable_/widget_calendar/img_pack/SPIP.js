/*
Copyright (c) 2006 Yahoo! Inc. All rights reserved.
version 0.9.0
*/

/**
 * @class The Spip global namespace
 */
var SPIP = function() {

    return {

        /**
         * Spip presentation platform utils namespace
         */
        util: {},

        /**
         * Spip presentation platform widgets namespace
         */
        widget: {},

        /**
         * Spip presentation platform examples namespace
         */
        example: {},

        /**
         * Returns the namespace specified and creates it if it doesn't exist
         *
         * SPIP.namespace("property.package");
         * SPIP.namespace("SPIP.property.package");
         *
         * Either of the above would create SPIP.property, then
         * SPIP.property.package
         *
         * @param  {String} sNameSpace String representation of the desired
         *                             namespace
         * @return {Object}            A reference to the namespace object
         */
        namespace: function( sNameSpace ) {

            if (!sNameSpace || !sNameSpace.length) {
                return null;
            }

            var levels = sNameSpace.split(".");

            var currentNS = SPIP;

            // SPIP is implied, so it is ignored if it is included
            for (var i=(levels[0] == "SPIP") ? 1 : 0; i<levels.length; ++i) {
                currentNS[levels[i]] = currentNS[levels[i]] || {};
                currentNS = currentNS[levels[i]];
            }

            return currentNS;

        }
    };

} ();

// Spip presentation platform packages.  Hard-coding them into the object
// uses fewer chars than the namespace function does
// SPIP.namespace("util");
// SPIP.namespace("widget");
// SPIP.namespace("example");
