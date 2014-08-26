jQuery(window).load(function(){
    jQuery('table.dataTable').on('click', 'a.btn', function(e){
        e.preventDefault();
        $this = jQuery(this);
        $td = $this.parent('td');

        $td.css({opacity: '0.5'});
        jQuery.ajax($this.attr('href'), {})
        .done(function(data){
            $td.html(data);
            $td.css({opacity: '1'});
        });

        return false;
    });
});

jQuery(function () {

    tableOptions["fnFooterCallback"] = function (row, data, start, end, display) {
        var api = this.oApi;
        var data;

        // Remove the formatting to get integer data for summation
        var intVal = function (i) {
            return typeof i === 'string'
                ? i.replace(/[\$,]/g, '')*1
                : typeof i === 'number'
                    ? i
                    : 0;
        };

        var count = {current: 0, peak: 0, opers: 0};
        for (var i=0; i < data.length; i++) {
            // calc the current users across the nodes
            count['current'] += intVal(data[i]['currentusers']);

            // calc the peak users across the nodes
            count['peak'] += intVal(data[i]['maxusers']);

            // calc the peak users across the nodes
            count['opers'] += intVal(data[i]['opers']);
        }

        jQuery('table.dataTable tfoot td.currentusers').text(count['current']);
        jQuery('table.dataTable tfoot td.maxusers').text(count['peak']);
        jQuery('table.dataTable tfoot td.opers').text(count['opers']);
    };
});