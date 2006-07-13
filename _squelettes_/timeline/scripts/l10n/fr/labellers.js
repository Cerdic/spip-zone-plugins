/*==================================================
 *  Localization of labellers.js
 *==================================================
 */

Timeline.GregorianDateLabeller.monthNames["fr"] = [
    "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"
];
Timeline.GregorianDateLabeller.dayNames["fr"] = [
    "Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"
];

Timeline.GregorianDateLabeller.labelPreciseFunctions["fr"] = function(date) {
    var text;
    var emphasized = false;

    var date2 = Timeline.DateTime.removeTimeZoneOffset(date, this._timeZone);
    text = Timeline.GregorianDateLabeller.dayNames["fr"][date2.getUTCDay()];
    text = text + " " + date2.getUTCDate();
    text = text + " " + Timeline.GregorianDateLabeller.getMonthName(date2.getUTCMonth(), this._locale);
    text = text + " " + date2.getUTCFullYear();
    text = text + ", " + date2.getUTCHours() + "h" + date2.getUTCMinutes();
    
    return text;
};

Timeline.GregorianDateLabeller.labelIntervalFunctions["fr"] = function(date, intervalUnit) {
    var text;
    var emphasized = false;

    var date2 = Timeline.DateTime.removeTimeZoneOffset(date, this._timeZone);
    
    switch(intervalUnit) {
    case Timeline.DateTime.DAY:
        text = date.getUTCDate() + " " + Timeline.GregorianDateLabeller.getMonthName(date.getUTCMonth(), this._locale);
        break;
    case Timeline.DateTime.WEEK:
        text = date.getUTCDate() + " " + Timeline.GregorianDateLabeller.getMonthName(date.getUTCMonth(), this._locale);
        break;
    default:
        return this.defaultLabelInterval(date, intervalUnit);
    }
    
    return { text: text, emphasized: emphasized };
};
