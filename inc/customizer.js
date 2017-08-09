(function( $ ) {
    wp.customize.bind( 'ready', function() {
        var customize = this;
        var select_primary = customize.control.instance('company_primary_id');
        if( ! select_primary )
            return false;

        select_primary.container.find('select').on('change', function(event) {
        	event.preventDefault();

        	$.ajax({
        		type: 'POST',
        		url: ajaxurl,
        		data: {
        			action: 'get_company_metas',
	        		nonce: contacts_customize.nonce,
	        		contact_id: $(this).val()
	        	},
        		success: function(response){
        			data = JSON.parse(response);
        			customize.control.instance('company_name').container.find('input').val( data.name );

        			customize.control.instance('company_address').container.find('textarea').val( data.address || '' );
        			customize.control.instance('company_numbers').container.find('textarea').val( data.numbers || '' );
        			customize.control.instance('company_email').container.find('input').val( data.email || '' );
        			customize.control.instance('company_time_work').container.find('textarea').val( data['work-time'] || '' );
        			customize.control.instance('company_socials').container.find('textarea').val( data.socials || '' );
        		}
        	}).fail(function() {
                console.log('AJAX Error');
            });

        });
    } );
})( jQuery );