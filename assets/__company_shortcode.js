(function() {
  tinymce.PluginManager.add('company_shortcode', function( editor, url ) {
    editor.addButton('company_shortcode', {
      title: 'Вставить шорткод контактов',
      icon: 'contacts dashicons-admin-home',
      onclick: function() {
        editor.windowManager.open( {
          title: 'Информация о компании',
          body: [
          {
            type: 'textbox', // тип textbox = текстовое поле
            name: 'textboxName', // ID, будет использоваться ниже
            label: 'ID и name текстового поля', // лейбл
            value: 'comment' // значение по умолчанию
          },
          {
            type: 'textbox', // тип textbox = текстовое поле
            name: 'before',
            label: 'Значение текстового поля по умолчанию',
            value: 'Привет',
            multiline: true, // большое текстовое поле - textarea
            minWidth: 300, // минимальная ширина в пикселях
            minHeight: 100 // минимальная высота в пикселях
          },
          {
            type: 'textbox', // тип textbox = текстовое поле
            name: 'after',
            label: 'Значение текстового поля по умолчанию',
            value: 'Привет',
            multiline: true, // большое текстовое поле - textarea
            minWidth: 300, // минимальная ширина в пикселях
            minHeight: 100 // минимальная высота в пикселях
          },
          {
            type: 'listbox', // тип listbox = выпадающий список select
            name: 'listboxName',
            label: 'Заполнение',
            'values': [ // значения выпадающего списка
              {text: 'Обязательное', value: '1'}, // лейбл, значение
              {text: 'Необязательное', value: '2'}
              ]
            }
            ],
        onsubmit: function( e ) { // это будет происходить после заполнения полей и нажатии кнопки отправки
          editor.insertContent( '[textarea id="' + e.data.textboxName + '" value="' + e.data.multilineName + '" required="' + e.data.listboxName + '"]');
        }
      });
      }
    });
  });
})();
