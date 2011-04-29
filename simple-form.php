<?php
/*
* @name: SimpleForms
* @author Ared Irawsuk
* @version 1.0
* @link http://dilhamsoft.com
* @license GNU General Public License
*/

if( !class_exists('SimpleForm') ):
	class SimpleForm{
		
		var $auto_id = false;
		var $prefix_id = 'sf_';
		var $fields = array();
		var $same_value = array();
		var $exclude_atts = array();
		var $exclude_fast_atts = array();

		var $wrap_field = '%s';
		var $wrap_label = '%s';
		var $child_template = '%1$s %2$s <br/>'; // %1$s = Label, %2$s Field
		var $child_checkbox_template = '%2$s %1$s <br/>'; // %1$s = Label, %2$s Field
		var $radio_template = '%2$s <span>%1$s</span><br/>'; // %1$s = Label, %2$s Field
		
		function SimpleForms($settings=array()){			
			$this->exclude_atts = array();
			$this->exclude_fast_atts = array('textarea', 'select', 'option', 'optgroup', 'name_', 'get_option', 'childs', 'child_template');
			$this->same_value = array('checked', 'selected', 'disabled', 'readonly', 'required');
		}
		
		function adds($fields){
			foreach ($fields as $field){
				$this->add($field);
			}
		}
		
 		function add($atts){
		$childs = $atts['childs'];
		$type = $atts['type'];
		$get_option = $atts['get_option'];
		
		
		$atts['id'] = $this->get_id($atts);
/* 		$atts['value'] = $this->get_value($atts); */		


		$output_label = $this->label(array('for' => $atts['id'] ), $atts['label']);
		$output_child_label = $this->label(false, $atts['label']);
		
		unset($atts['label']);
		
		if (is_array($childs)){
		
			$child_output = '';
			foreach ($childs as $child_fields){
				if (!isset($child_fields['name']))
					$child_fields['name'] = $this->_name_child($atts['name'], $child_fields['name_']);
				
				$child_fields['id'] = $this->get_id($child_fields);
				
				$childs_proccess = array_merge($atts, $child_fields);
				
				if (!isset($child_fields['get_option']))
					$childs_proccess['get_option'] = $get_option;
					
				$childs_proccess['get_option'] = $this->get_value($childs_proccess['name'], $childs_proccess['get_option']);
				
				$child_output_label = $this->label(array('for' => $childs_proccess['id'] ), $childs_proccess['label']);
				$childs_proccess['label'] = false;
				
				$child_output_field = $this->proccess_field($childs_proccess);
				$template = (isset($childs_proccess['child_template'])) ? $childs_proccess['child_template'] : $this->child_template;
				
					if ($childs_proccess['type'] == 'checkbox')
						$template = (isset($childs_proccess['child_template'])) ? $childs_proccess['child_template'] : $this->child_checkbox_template;
				
				$child_output .= sprintf($template, $child_output_label, $child_output_field);
			}
			
			return $this->fields[] = array( 'label' => $output_child_label, 'html' => $child_output);
		}

			$output_field = $this->proccess_field($atts);

			return $this->fields[] = array( 'data' => $atts, 'label' => sprintf($this->wrap_label, $output_label), 'html' => sprintf($this->wrap_field, $output_field) );
		}
		
		function proccess_field($atts=array()){
		$type = $atts['type'];
		$get_option = $atts['get_option'];
		
			switch ($type){
				case 'textarea':
					unset($atts['type'], $atts['value']);
					$output = $this->textarea($atts, $get_option, true);
				break;
				
				case 'select':
					unset($atts['type'], $atts['value']);
					$output = $this->select($atts);
				break;

				default:
					$defaults = array( 'value' => $get_option);
					$fields = array_merge($defaults, $atts);
					
					if (!isset($atts['value'])){
						
						$atts['value'] = $get_option;
						
						if($type == 'checkbox')
							$atts['value'] = 1;	

					}
					
					if($type == 'checkbox' || $type == 'radio')
						$atts['checked'] = $this->checked($get_option, $atts['value']);
					
					$output = $this->input($atts);
				break;
			}
			
			return $output;
		}
		
		function get_fields(){
			
			$output = '';
			foreach ($this->fields as $out)
				$output .= $out;
			
			return $output;
		}

		function print_fields(){
			echo $this->get_fields();
		}
				
		function _name_child($name, $child_name){
			return $name . '[' . $child_name . ']';
		}
		
		function input($atts=array()){
			$get_option = $atts['get_option'];
			$option = $atts['option'];
			$type = $atts['type'];
			
			if ($type == 'radio' && is_array($option)){
				$option_html = '';
				foreach ($option as $opt_val => $opt_lbl){
					unset($atts['id']);
					$atts['value'] = $opt_val;
					$atts['checked'] = $this->checked($get_option, $opt_val);
					$atts['id'] = ($this->get_id($atts)) ? $this->get_id($atts) . '-' . $opt_val : null;
					$radio_label = $this->label(array('for' => $atts['id']), $opt_lbl);
					$radio_field = $this->html_tag('input', $atts, false);
					$option_html .= sprintf($this->radio_template, $radio_label, $radio_field);
				}
				return $option_html;
			}
			
			return $this->html_tag('input', $atts, false);
		}
		
		function button($atts=array(), $html=''){
			return $this->html_tag('button', $atts, true, $html);
		}
		
		function label($atts=array(), $html=''){
			return $this->html_tag('label', $atts, true, $html);
		}
		
		function textarea($atts=array(), $html='', $stripcslashes=false){
			if ($stripcslashes)
				$html = stripcslashes($html);
			
			return $this->html_tag('textarea', $atts, true, $html);
		}
		
		function select($atts=array(), $html=''){
		$get_option = $atts['get_option'];
		$option = $atts['option'];
		$optgroup = $atts['optgroup'];
		
			if (is_array($option)){
			
				$option_html = '';
				foreach ($option as $opt_val => $opt_lbl)
					$option_html .= $this->option(array('value' => $opt_val, 'selected' =>  $this->selected($get_option, $opt_val)), $opt_lbl);

			 }
			 
			 if (is_array($optgroup)){
			 
				$optgroup_html = '';
				 foreach ($optgroup as $opt_group_label => $optionx){
					if (!is_array($optionx)) continue;
					
					$html_option = '';
					foreach ($optionx as $opt_valx => $opt_lblx)
						$html_option .= $this->option(array('value' => $opt_valx, 'selected' =>  $this->selected($get_option, $opt_valx)), $opt_lblx);
					
					$optgroup_html .= $this->optgroup(array('label' => $opt_group_label), $html_option);
				}
				
			 }
			 
			$html = $html . $option_html . $optgroup_html;
			
			return $this->html_tag('select', $atts, true, $html);
		}
		
		function option($atts=array(), $html=''){
			return $this->html_tag('option', $atts, true, $html);
		}
		
		function optgroup($atts=array(), $html=''){
			return $this->html_tag('optgroup', $atts, true, $html);
		}

		function html_tag($tag, $atts=array(), $close_tag=true, $html='', $activate_exlude_atts=true){
			
			$html_atts = $this->html_attributes($atts, $activate_exlude_atts);
			
			if (!$close_tag)
				$html = '<' . $tag . $html_atts . '/>';
			else
				$html = '<' . $tag . $html_atts . '>' . $html . '</' . $tag . '>';
				
			return $html;
		}
		
		function html_attributes($atts = array(), $activate_exlude_atts=true){

			if (!is_array($atts) || empty($atts))
				return null;

			$output = '';
			foreach ($atts as $att => $att_v){
			
				if (empty($att_v))
					continue; /// jika ada attribute selain value yang kosong, maka tidak perlu ditulis.
					
				if (in_array($att, $this->exclude_atts) || in_array($att, $this->exclude_fast_atts))
					continue;
					
				if (in_array($att, $this->same_value) && $att_v == true)
					$att_v = $att; /// jika ada attribute yang sama dengan value
					
				
				$output .= " $att=\"$att_v\"";
			}
			return $output;
		}
				
		function checked($value, $compare){
			return $this->value_helper($value, $compare, 'checked');
		}
		function selected($value, $compare){
			return $this->value_helper($value, $compare, 'selected');
		}
		function value_helper( $helper, $current, $type ) {
			return ( (string) $helper === (string) $current ) ? $type : false;
		}

		function get_id($atts = array()){
			$name = $atts['name'];
			$id = $atts['id'];
			
			if ($this->auto_id && !isset($id)){
				$id = $this->prefix_id . $this->sanitize_id($name);
			}
			return $id;
		}
		
		function sanitize_id($string){
			$replaces = array(' ' => '-', '[' => '-', ']' => '');
			$output = str_replace(array_keys($replaces), array_values($replaces), $string);
			return $output;
		}
		
		function _walk_value($name, $values){
			if (!is_array($name))
				return $values;
				
			foreach ($name as $a => $b)
				$output = $this->_walk_value($b, $values[$a]);
				
			return $output;
		}
		
		function get_value($name, $value){
			
			if (!is_array($value) && !strpos($name, '[') )
				return $value;
			
			parse_str($name, $_name);
				
			$output = $this->_walk_value($_name, $value);

			return $output;
		}
		
	}
	
endif;

?>