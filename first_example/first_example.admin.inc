/**
* @file
* Resultado de llamadas a p�ginas de administraci�n para el m�dulo 
* Node First Example.
*/
/**
* Constructor del formulario para configurar el m�dulo.
*
*/
function first_example_admin_settings() {
/**
* C on node_type_get_types() se obtiene la informaci�n de todos los tipos 
* de contenido, cada uno como un objeto, por lo que hay que seleccionar 
* s�lo los nombres de los tipo de contenido para mostrarlos en las opciones.
*/
$content_types_list = node_type_get_types();
foreach ($content_types_list as $key => $type) {
$list[$key] = $type->name;
}
$form['first_example_admin_settings'] = array(
'#type' => 'textfield',
'#title' => t('Add an message'),
'#options' => $list,
//la variable usada para guardar el valor de la configuraci�n 
//es aconsejable que se llame igual al campo del formulario 
//usado, en este caso 'node_first_example_admin_settings'.
'#default_value'=> variable_get('first_example_message',
array('Welcome')),
'#description'   =>   t ( 'See the message for block.' ),
);
return system_settings_form($form);
}