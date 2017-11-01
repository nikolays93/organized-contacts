/* global tinymce */
( function() {
    tinymce.PluginManager.add( 'company', function( editor ) {
        editor.addButton( 'company', {
            title: company_localize.addButton_title, // 'Вставить шорткод контактов',
            icon: 'contacts dashicons-admin-home',
            onclick: function() {
                wp.mce.company.InsertContacts( editor );
            }
        });
    });
})();
