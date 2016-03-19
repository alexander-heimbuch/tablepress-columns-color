(function ($) {
    'use strict';

    var dye = function ($table, columns) {
        var $dataTable = $table.DataTable();

        $.each(columns, function (index, color) {
            var rows = $dataTable.columns(index).nodes(),
                head = $($dataTable.table().header()).find('th')[index] || undefined;

            if (~color.indexOf('.')) {
                var cssClass = color.replace('.', ' ');

                if (head !== undefined) {
                    $(head).addClass(cssClass);
                }

                rows.each(function (elem) {
                    $(elem).addClass(cssClass);
                });
            } else {
                if (head !== undefined) {
                    $(head).css('background-color', color);
                }

                rows.each(function (elem) {
                    $(elem).css('background-color', color);
                });
            }
        });
    };

    $.each(window.TABLE_COLORS, function (tableId, columns) {
        var $table = $('#' + tableId);

        $table.on( 'draw.dt', function () {
            dye($table, columns);
        });
    });
})(jQuery);
