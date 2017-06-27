## WPForm::render()
```
/**
 * Class Name: WPForm ( :: render )
 * Class URI: https://github.com/nikolays93/WPForm
 * Description: render forms as wordpress fields
 * Version: 1.1
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
```

You may use as single,
```
$render_data = array(
    'type'      => 'checkbox',
    'id'        => 'check_id',
    'label'     => 'Label some input',
    'desc'      => 'This input is fail for the test',
    'check_active' => 'id'
    );
```
also multiple inputs
```
$render_data = array(
    array(
    'type' => 'select',
    'id'   => 'test-select',
    'label'  => 'choose you class',
    'option' => array(
        'ID' => 'VALUE',
        'second' => 'select my second'
        ),
    'desc' => 'Some checks',
    ),
    array(
        'type' => 'number',
        'id'   => 'year',
        'default' => '2017',
        'before' => 'today is: ',
        'after' => 'Years BC',
        'label'  => 'choose you class',
        'option' => array(
            'ID' => 'VALUE',
            'second' => 'select my second'
            ),
            'desc' => 'Some checks'
            )
    );
```

## How To Use Class: ##
```
 /**
   * Render form items
   * @param  boolean $render_data array with items ( id, name, type, options..)
   * @param  array   $active      selected options from form items
   * @param  boolean $is_table    is a table
   * @param  array   $args        array of args (item_wrap, form_wrap, label_tag, hide_desc) @see $default_args
   * @param  boolean $is_not_echo true = return, false = echo
   * @return html                 return or echo
   */
  $active = array('check_id' => 'on');
  $args = array(
      'item_wrap' => array('<p>', '</p>'), // outside item code ([0] => before, [1] => after)
      'form_wrap' => array('<table class="table form-table"><tbody>', '</tbody></table>'), // outside all items (fieldset, form) code ([0] => before, [1] => after)
      'label_tag' => 'th', // label tag is table
      'hide_desc' => false, // dont show descriptions for small tables (ex. in side box)
      'clear_value' => 'false' // value if not checked checkbox (use bool false for empty)
      );
  WPForm::render($render_data, $active = array(), $is_table = false, $args, $is_not_echo = false);
```
### built in attrs: ###
- id - handle
- label - show input label
- before - html before input
- after - html after input
- default - The default value (to do future: value now write is equal this)
- value - input has regular value
- check_active - Which key to check activity
- placeholder - default input attr (is not radio or checkbox)
- desc || description 
- name (default: id)

use `apply_filters( 'dt_admin_options', $data_render, $option_name )` for admin page

---
all done! enjoy
