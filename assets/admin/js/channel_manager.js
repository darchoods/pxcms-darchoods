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
