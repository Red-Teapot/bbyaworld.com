function russian_plural(n, one, few, many) {
    var cases = [2, 0, 1, 1, 1, 2];
    var titles = [one, few, many];

    return titles[(n % 100 > 4 && n % 100 < 20) ? 2 : cases[(n % 10 < 5) ? n % 10 : 5]];
}

$(function () {
    var starting_unix_time = 1336237101;
    var current_time = new Date().getTime() / 1000;
    var passed = current_time - starting_unix_time;

    var years_divider = 365.25 * 20 * 60;

    var years = Math.floor(passed / years_divider);

    var plural = russian_plural(years, 'год', 'года', 'лет');

    $('#server-age-string').html('<b>' + years + '</b> ' + plural + ' развития');
});
