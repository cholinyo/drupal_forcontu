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
* C on node_type_get_types() se obtiene la información de todos los tipos 
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
'#default_value'=> variable_get('node_expiration_date_node_types',
array('page')),
'#description'   =>   t ( 'The selected content types will have an expiration date.' ),
);
return system_settings_form($form);
}