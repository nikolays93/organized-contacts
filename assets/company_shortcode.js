/* global tinyMCE */
(function($){
  var shortcode_string = 'company';
  wp.mce = wp.mce || {};

  wp.mce.company_shortcode = {
    InsertContacts: function(editor, values, onsubmit_callback){
      values = values || [];
      if(typeof onsubmit_callback !== 'function'){
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
          if(e.data.content_filter && e.data.content_filter != 'the_content')
            args.attrs.filter = e.data.content_filter;
          if(e.data.before) args.attrs.before = e.data.before;
          if(e.data.after) args.attrs.after = e.data.after;

          editor.insertContent( wp.shortcode.string( args ) );
        };
      }

      editor.windowManager.open( {
        title: 'Информация о компании',
        body: [
          {
            type  : 'listbox',
            name  : 'field',
            label : 'Вставить',
            values: [
              {text: 'Название организации', value: 'name'},
              {text: 'Адрес', value: 'address'},
              {text: 'Номера телефонов', value: 'numbers'},
              {text: 'E-Mail адрес', value: 'email'},
              {text: 'Режим работы', value: 'time_work'},
              {text: 'Социальные сети', value: 'socials'}
            ],
            value : values.field
          },
          {
            type       : 'textbox',
            name       : 'content_filter',
            label      : 'Фильтр',
            placeholder: 'the_content',
            value      : values.content_filter || ''
          },
          {
            type: 'container',
            html: "<span style='font-size: 12px;'>Отключить стандартный the_content можно значением none</span>"
          },
          {
            type: 'textbox',
            name: 'before',
            label: 'HTML до',
            value: values.before,
            multiline: true,
            minWidth: 300,
            minHeight: 100
          },
          {
            type: 'textbox',
            name: 'after',
            label: 'HTML после',
            value: values.after,
            multiline: true,
            minWidth: 300,
            minHeight: 100
          },
        ],
        onsubmit: onsubmit_callback
      } );
    }
  };
  // wp.mce.views.register( shortcode_string, wp.mce.company_shortcode );
}(jQuery));
