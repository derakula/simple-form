<!DOCTYPE html>
<html>
<head>
	<title>Simple Forms Demo</title>
</head>
<body>
<?php

require_once('simple-form.php');

$simpleform = new SimpleForm();
	
	$simpleform->auto_id = true;
	
	$fields[] = array(
					'label' => 'Your Phone',
					'name' => 'phone',
					'type' => 'text',
					'get_option' => '',
				);
				
	$fields[] = array(
					'label' => 'Social Media',
 					'name' => 'social_media',
					'type' => 'text',
					'childs' => array(
									array(
										'label' => 'Facebook',
										'name_' => 'facebook',
									),
									array(
										'name_' => 'twitter',
									),
									array(
										'name_' => 'rss',
										'type' => 'textarea',
									),										
								),
					'get_option' => array('social_media' => array('facebook' => 'http://fb.dilhamsoft.com', 'twitter' => 'http://twitter.com/dilhamsoft') ),
				);
				
	$fields[] = array(
					'label' => 'Checkbox',
					'name' => 'checkme',
					'type' => 'checkbox',
				);
	$fields[] = array(
					'label' => 'Checkbox',
					'name' => 'checkme',
					'type' => 'checkbox',
					'childs' => array(
									array(
										'name_' => 'html',
										'label' => 'HTML'
									),
									array(
										'name_' => 'jquery',
										'label' => 'jQuery'
									),
								),
					'get_option' => array('checkme' => array('html' => 1, 'jquery' => 0) ),
				);
	$fields[] = array(
					'label' => 'Radio',
					'name' => 'radio_me',
					'type' => 'radio',
					'option' => array('html5' => 'HTML5', 'jquery' => 'jQuery', 'css' => 'CSS'),
					'get_option' => 'jquery',
				);
				
	$fields[] = array(
					'label' => 'Text Area',
					'name' => 'text_area_me',
					'type' => 'textarea',
					'get_option' => 'test',
				);
				
	$fields[] = array(
					'label' => 'Select',
					'name' => 'select_me',
					'type' => 'select',
					'option' => array(1 => 'Aku', 2 => 'Kamu', 3 => 'Maneh'),
					'get_option' => 2,
				);
	$fields[] = array(
					'label' => 'Select Option Group',
					'name' => 'select_option_group_me',
					'type' => 'select',
					'optgroup' => array('Indonesia' => array('jkt' => 'Jakarta', 'smi' => 'Sukabumi'), 'USA' => array('Florida') ),
					'get_option' => 'smi',
				);
				
	$simpleform->adds($fields);


?>

<?php foreach ($simpleform->fields as $fetch): ?>

<fieldset>
<legend><?php echo $fetch['label']; ?></legend>
<?php
		echo $fetch['html'];

?>
</fieldset>


<?php endforeach; ?>

</body>

</html>