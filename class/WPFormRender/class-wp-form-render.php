<?php
/**
 * Class Name: WPForm ( :: render )
 * Class URI: https://github.com/nikolays93/WPForm
 * Description: render forms as wordpress fields
 * Version: 1.3
 * Author: NikolayS93
 * Author URI: https://vk.com/nikolays_93
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) )
  exit; // disable direct access

function _isset_default(&$var, $default, $unset = false){
  $result = $var = isset($var) ? $var : $default;
  if($unset) $var = FALSE;
  return $result;
}
function _isset_false(&$var, $unset = false){ return _isset_default( $var, false, $unset ); }
function _isset_empty(&$var, $unset = false){ return _isset_default( $var, '', $unset ); }

class WPForm {
  static protected $clear_value;

  /**
   * EXPEREMENTAL!
   * Get ID => Default values from $render_data
   * @param  array() $render_data
   * @return array(array(ID=>default),ar..)
   */
  public static function defaults( $render_data ){
    $defaults = array();
    if(empty($render_data))
      return $defaults;

    if( isset($render_data['id']) )
        $render_data = array($render_data);

    foreach ($render_data as $input) {
      if(isset($input['default']) && $input['default']){
        $input['id'] = str_replace('][', '_', $input['id']);
        $defaults[$input['id']] = $input['default'];
      }
    }

    return $defaults;
  }
  
  /**
   * EXPEREMENTAL! todo: add recursive handle
   * @param  string  $option_name      
   * @param  string  $sub_name         $option_name[$sub_name]
   * @param  boolean $is_admin_options recursive split value array key with main array
   * @return array                     installed options
   */
  public static function active($option_name, $sub_name = false, $is_admin_options = false, $postmeta = false){
    global $post;

    if( $postmeta ){
      if( !is_int($postmeta) && !isset($post->ID) )
        return false;

      $post_id = is_int($postmeta) ? $postmeta : $post->ID;

      $active = get_post_meta( $post_id, $option_name, true );
    }
    else {
      $active = get_option( $option_name, array() );
    }
    
    if( $sub_name && isset($active[$sub_name]) && is_array($active[$sub_name]) )
      $active = $active[$sub_name];
    elseif( $sub_name && !isset($active[$sub_name]) )
      return false;

    if(!is_array($active))
        return false;

    if( $is_admin_options === true ){
      $result = array();
      foreach ($active as $key => $value) {
        if( is_array($value) ){
          foreach ($value as $key2 => $value2) {
            $result[$key . '_' . $key2] = $value2;
          }
        }
        else {
          $result[$key] = $value;
        }
      }

      return $result;
    }

    return $active;
  }

  // * EXPEREMENTAL!
  protected static function set_defaults( $args, $is_table ){
    $default_args = array(
      'admin_page'  => false, // set true for auto detect
      'item_wrap'   => array('<p>', '</p>'),
      'form_wrap'   => array('<table class="table form-table"><tbody>', '</tbody></table>'),
      'label_tag'   => 'th',
      'hide_desc'   => false,
      'clear_value' => 'false'
      );
    $args = array_merge($default_args, $args);

    self::$clear_value = $args['clear_value'];

    if( $args['item_wrap'] === false )
      $args['item_wrap'] = array('', '');

    if($args['form_wrap'] === false)
      $args['form_wrap'] = array('', '');

    if( $args['label_tag'] == 'th' && $is_table == false )
      $args['label_tag'] = 'label';

    return $args;
  }

  /**
   * EXPEREMENTAL!
   * change names for wordpress options
   * @param  array  $inputs      rendered inputs
   * @param  string $option_name name of wordpress option ( @see get_option() )
   * @return array               filtred inputs
   */
  protected static function admin_page_options( $inputs, $option_name = false ){
    if( ! is_string( $option_name ) && !empty($_GET['page']) )
      $option_name = $_GET['page'];

    if( ! $option_name )
      return $inputs;

    foreach ( $inputs as &$input ) {
      $multyple = ( isset($input['multyple']) && $input['multyple'] !== false ) ? '[]' : '';

      if( isset($input['name']) )
        $input['name'] = "{$option_name}[{$input['name']}]" . $multyple;
      else
        $input['name'] = "{$option_name}[{$input['id']}]" . $multyple;

      $input['check_active'] = 'id';
    }
    return $inputs;
  }

  /**
   * check if is checked ( called( $value, $active_value, $default ) )
   * @param  mixed         $value   ['value'] array setting (string || boolean)(!isset ? false)
   * @param  string||false $active  value from $active option
   * @param  mixed         $default ['default'] array setting (string || boolean)(!isset ? false)
   * 
   * @return boolean       checked or not
   */
  private static function is_checked( $value, $active, $default ){
    if( $active === false && $value )
      return true;

    $checked = ( $active === false ) ? false : true;
    if( $active === 'false' || $active === 'off' || $active === '0' )
      $active = false;

    if( $active === 'true'  || $active === 'on'  || $active === '1' )
      $active = true;

    if( $active || $default ){
      if( $value ){
        if( is_array($active) ){
          if( in_array($value, $active) )
            return true;
        }
        else {
          if( $value == $active || $value === true )
            return true;
        }
      }
      else {
        if( $active || (!$checked && $default) )
          return true;
      }
      return false;
    }
  }

  /**
   * Render form items
   * @param  boolean $render_data array with items ( id, name, type, options..)
   * @param  array   $active      selected options from form items
   * @param  boolean $is_table    is a table
   * @param  array   $args        array of args (item_wrap, form_wrap, label_tag, hide_desc) @see $default_args
   * @param  boolean $is_not_echo true = return, false = echo
   * @return html                 return or echo
   */
  public static function render(
    $render_data = false,
    $active = array(),
    $is_table = false,
    $args = array(),
    $is_not_echo = false){

    wp_enqueue_style( 'form-render-css', ORG_URL . '/class/WPFormRender/form-render.css', false, '1.0' );
    wp_enqueue_script( 'form-render-css', ORG_URL . '/class/WPFormRender/multyple.js', array('jquery'), '1.0', true );

    $html = $hidden = array();

    if( empty($render_data) ){
      if( function_exists('is_wp_debug') && is_wp_debug() )
        echo '<pre> Параметры формы не были переданы </pre>';
      return false;
    }
    
    if( isset($render_data['id']) )
        $render_data = array($render_data);

    if($active === false)
      $active = array();
    
    $args = self::set_defaults( $args, $is_table );
    if( $args['admin_page'] )
      $render_data = self::admin_page_options( $render_data, $args['admin_page'] );

    /**
     * Template start
     */
    if($is_table)
        $html[] = $args['form_wrap'][0];

    foreach ( $render_data as $input ) {
      $before  = _isset_empty($input['before'], 1);
      $after   = _isset_empty($input['after'], 1);
      $label   = _isset_false($input['label'], 1);
      $default = _isset_false($input['default'], 1);
      $multyple = _isset_false($input['multyple'], 1);
      $value   = _isset_false($input['value']);
      $check_active = _isset_false($input['check_active'], 1);
      
      if( $input['type'] != 'checkbox' && $input['type'] != 'radio' )
        _isset_default( $input['placeholder'], $default );

      if( isset($input['desc']) ){
        $desc = $input['desc'];
        $input['desc'] = false;
      }
      elseif( isset( $input['description'] ) ) {
        $desc = $input['description'];
        $input['description'] = false;
      }
      else {
        $desc = false;
      }

      if( !isset($input['name']) )
          $input['name'] = _isset_empty($input['id']);

      $input['id'] = str_replace('][', '_', $input['id']);
      
      /**
       * set values
       */
      $active_name = $check_active ? $input[$check_active] : str_replace('[]', '', $input['name']);
      $active_value = ( is_array($active) && sizeof($active) > 0 && isset($active[$active_name]) ) ?
         $active[$active_name] : false;

      $entry = '';
      if($input['type'] == 'checkbox' || $input['type'] == 'radio'){
        $entry = self::is_checked( $value, $active_value, $default );
      }
      elseif( $input['type'] == 'select' ){
        $entry = ($active_value) ? $active_value : $default;
      }
      else {
        // if text, textarea, number, email..
        $entry = $active_value;
        $placeholder = $default;
      }

      $input_html = '';
      switch ($input['type']) {
        case 'checkbox':
        case 'radio':

          if( empty($input['value']) )
            $input['value'] = 'on';

          if( $entry )
            $input['checked'] = 'true';

          // if $clear_value === false dont use defaults (couse default + empty value = true)
          $cv = self::$clear_value;
          if( false !== $cv )
            $input_html .= "<input name='{$input['name']}' type='hidden' value='{$cv}'>\n";

          $input_html .= "<input";
          foreach ($input as $attr => $val) {
            if( $val )
              $input_html .= ' ' . esc_attr($attr) . '="' . esc_attr($val) . '"';
          }
          $input_html .= ">";

          $input_html .= (!$is_table && $label) ? '<label for="'.$input['id'].'">'.$label.'</label>' : '';

          break;

        case 'select':

          $input_html .= (!$is_table && $label) ? '<label for="'.$input['id'].'">'.$label.'</label>' : '';
          $options = _isset_false($input['options'], 1);
          if(! $options )
            break;

          $input_html .= '<select';
          foreach ($input as $attr => $val) {
            if( $val )
              $input_html .= ' ' . esc_attr($attr) . '="' . esc_attr($val) . '"';
          }
          $input_html .= '>';

          foreach ($options as $value => $option) {
            $active_str = ($active_id == $value) ? " selected": "";
            $input_html .= "<option value='{$value}'{$active_str}>{$option}</option>";
          }
          $input_html .= "</select>";
          // if( isset($multyple) && $multyple !== false )
          //   $input_html .= "<span class='dashicons dashicons-plus multyple' style='display: none;'></span>";


          break;

        case 'textarea':
          
          $type = $input['type'];
          _isset_default($input['rows'], 5);
          _isset_default($input['cols'], 40);
          unset($input['type']);
          $input_html .= (!$is_table && $label) ? '<label for="'.$input['id'].'">'.$label.'</label>' : '';
        case 'button':

          $entry = ( empty($input['content']) ) ? $entry : $input['content'];
          unset($input['content']);

          $input_html .= '<' . $type;
          foreach ($input as $attr => $val) {
            if( $val )
              $input_html .= ' ' . esc_attr($attr) . '="' . esc_attr($val) . '"';
          }
          $input_html .= '>' . $entry . '</'.$type.'>';
          
          if( isset($type) )
            $input['type'] = $type;
          
          break;

        case 'html':

          $input_html .= $input['value'];

          break;
        
        default: // text, hidden, submit, number, email

          $input['class'] = (isset($input['class'])) ? $input['class'] . ' multyple' : 'multyple';
         
          if(!$is_table && $label)
            $input_html .= '<label for="'.$input['id'].'">'.$label.'</label>';

          $dash_class = 'plus';
          if( is_array($entry) ){
            foreach ($entry as $val) {

              if( $dash_class != 'plus' )
                $input_html .= $args['item_wrap'][1].$args['item_wrap'][0];

              if( $entry )
                $input['value'] = $val;

              $input_html .= self::input_template( $input, $multyple, $dash_class );

              $dash_class = 'minus';
            }
          }
          else {
            $input_html .= self::input_template( $input, $multyple, $dash_class );
          }

          break;
      }
      
      /**
        * @todo: set tooltip
        */
      if( $desc ){
        $hidden = ( isset($args['hide_desc']) && $args['hide_desc'] === true ) ? "style='display: none;'" : '';
        
        $desc_html = "<span class='description'{$hidden}>{$desc}</span>";
      } else {
        $desc_html = '';
      }
      
      if(!$is_table){
        $html[] = $before . $args['item_wrap'][0] . $input_html . $args['item_wrap'][1] . $after . $desc_html;
      }
      elseif( $input['type'] == 'hidden' ){
        $hidden[] = $before . $input_html . $after;
      }
      elseif( $input['type'] == 'html' ){
        $html[] = $args['form_wrap'][1];
        $html[] = $before . $input_html . $after;
        $html[] = $args['form_wrap'][0];
      }
      else {
        $item = $before . $args['item_wrap'][0]. $input_html .$args['item_wrap'][1] . $after;

        $html[] = "<tr id='{$input['id']}'>";
        $html[] = "  <{$args['label_tag']} class='label'>{$label}</{$args['label_tag']}>";
        $html[] = "  <td>";
        $html[] = "    " .$item;
        $html[] = $desc_html;
        $html[] = "  </td>";
        $html[] = "</tr>";
      }
    } // endforeach
    if($is_table)
      $html[] = $args['form_wrap'][1];

    $result = implode("\n", $html) . "\n" . implode("\n", $hidden);
    if( $is_not_echo )
      return $result;
    else
      echo $result;
  }

  static public function input_template( $input, $multyple, $dash_class ){
    $input_html = '';
    $input_html .= "<input";
    foreach ($input as $attr => $val) {
      if( $val )
        $input_html .= ' ' . esc_attr($attr) . '="' . esc_attr($val) . '"';
    }
    $input_html .= ">";
    if( isset($multyple) && $multyple !== false && $input['type'] !== 'submit' )
      $input_html .= "<span class='dashicons dashicons-{$dash_class} multyple' style='display: none'></span>";

    return $input_html;
  }
}