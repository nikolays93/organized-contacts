/* global tinymce */
( function() {
	tinymce.PluginManager.add( 'company_shortcode', function( editor ) {
		editor.addButton( 'company_shortcode', {
			title: 'Вставить шорткод контактов',
			icon: 'contacts dashicons-admin-home',
			onclick: function() {
				wp.mce.company_shortcode.InsertContacts(editor);
			}
		});
	});
})();
