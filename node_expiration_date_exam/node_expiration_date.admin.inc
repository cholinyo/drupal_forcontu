<?php
/**
* @file
* Resultado de llamadas a páginas de administración para el módulo
* Node Expiration Date.
*/

/**
* Constructor del formulario para configurar el módulo.
*
*/
function node_expiration_date_admin_settings() {
	/**
	* Con node_type_get_types() se obtiene la información de todos los tipos
	* de contenido, cada uno como un objeto, por lo que hay que seleccionar
	* sólo los nombres de los tipo de contenido para mostrarlos en las opciones.
	*/
	$content_types_list = node_type_get_types();
	
	foreach ($content_types_list as $key => $type) {
		$list[$key] = $type->name;
	}
	
	$form['node_expiration_date_node_types'] = array(
		'#type' => 'checkboxes',
		'#title' => t('Add an expiration date to these content types'),
		'#options' => $list,
		//la variable usada para guardar el valor de la configuración
		//es aconsejable que se llame igual al campo del formulario
		//usado, en este caso 'node_expiration_date_node_types'.
		'#default_value'=> variable_get('node_expiration_date_node_types',array('page')),
		'#description' => t('The selected content types will have an expiration date.'),
	);
	
	/*Actividad 47.9. Inclusión de un campo de tipo fecha.*/
	/*Se establece la fecha por defecto a 1/1/2013*/
	
	// $fecha_expiracion_config = variable_get('default_expiration_date'); 
	
	$fecha_por_defecto = array('year' => 2013, 'month' => 1, 'day' => 1);
	
	$form['default_expiration_date'] = array(
		'#type' => 'date',
		'#title' => t('Default expiration date'),
		//'#default_value' => (isset($fecha_expiracion_config) ? $fecha_expiracion_config : $fecha_por_defecto),
		'#default_value' => variable_get('default_expiration_date', $fecha_por_defecto),
  );
	return system_settings_form($form);
}

// Actividad 47.9. Validación del formulario "node_expiration_date_node_types".
function node_expiration_date_admin_settings_validate($form, &$form_state) {
   //Se recupera la fecha del formulario.
	 $date = $form_state['values']['default_expiration_date'];
   if (!($date['year'] >= 2013 && $date['month'] >= 1 && $date['day'] >= 1))
      form_set_error('default_expiration_date', t('Expiration date must be greater than January 1, 2013'));
}
