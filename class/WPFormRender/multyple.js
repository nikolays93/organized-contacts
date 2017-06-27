jQuery(document).ready(function($) {
	function setRemoveEvent(){
        $('.multyple + .dashicons-minus').each(function(index, el) {
            $(this).show();
            $(this).on('click', function(event) {
                $(this).parent().remove();
            });
        });
	}

    $('.multyple + .dashicons-plus').each(function(index, el) {
    	$(this).show();
    	$(this).on('click', function(event) {
    		event.preventDefault();
    		
    		var $cloned = $(this).parent().clone().attr('class', 'cloned');
    		$(this).parent().parent().append( $cloned );
    		$cloned.find('.dashicons').addClass('dashicons-minus');
    		setRemoveEvent();
    	});
    });
    setRemoveEvent();
});