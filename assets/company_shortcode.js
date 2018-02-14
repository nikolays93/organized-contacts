/* global tinyMCE */
(function($){
    var shortcode_string = 'company';
    wp.mce = wp.mce || {};

    wp.mce.company = {
        InsertContacts: function(editor, values, onsubmit_callback){
            values = values || [];

        if( typeof onsubmit_callback !== 'function' ) {
            onsubmit_callback = function( e ) {
                // Insert content when the window form is submitted
                var args = {
                    tag     : shortcode_string,
                    type    : 'single',
                    attrs : {
                        field : e.data.field
                    }
                };

                // defaults
                if(e.data.id) args.attrs.id = e.data.id;
                if(e.data.content_filter && e.data.content_filter != 'the_content')
                    args.attrs.filter = e.data.content_filter;
                if(e.data.before) args.attrs.before = e.data.before;
                if(e.data.after) args.attrs.after = e.data.after;

                editor.insertContent( wp.shortcode.string( args ) );
            };
        }

        var manager_body = [
            {
                type  : 'listbox',
                name  : 'field',
                label : company_localize.insert,
                values: company.fields,
                value : values.field
            },
            {
                type       : 'textbox',
                name       : 'content_filter',
                label      : company_localize.filter,
                placeholder: 'the_content',
                value      : values.content_filter || ''
            },
            {
                type: 'container',
                html: "<span style='font-size: 12px;'>" + company_localize.howdisablefilter + "</span>"
            },
            {
                type: 'textbox',
                name: 'before',
                label: company_localize.htmlbefore,
                value: values.before,
                multiline: true,
                minWidth: 300,
                minHeight: 100
            },
            {
                type: 'textbox',
                name: 'after',
                label: company_localize.htmlafter,
                value: values.after,
                multiline: true,
                minWidth: 300,
                minHeight: 100
            },
        ];

        if( company.organizations.length ) { 
            manager_body.unshift({
                type : 'listbox',
                name: 'id',
                label: company_localize.organization, // Организация
                value: values.id,
                values: company.organizations
            });
        }

        editor.windowManager.open( {
            title: company_localize.about_organization,
            body: manager_body,
            onsubmit: onsubmit_callback
        } );
    }
};
// wp.mce.views.register( shortcode_string, wp.mce.company );
}(jQuery));
