<?php

include "../modelo/zeion.db.php";

// recuperamos el criterio de la busqueda
$criterio = strtolower($_GET["term"]);
if (!$criterio) return;

print '['; // <<<< observa que aquí comienza la definición de nuestro resultado

// creamos una conexión a la base de datos en modo persistente, de esta manera
// las siguientes consultas tardarán menos en ejecutarse
// (los datos de la cadena de conexión son usuario:contraseña@servidor/basededatos)
$db = new zeion_db('development:development@localhost/demos', true);


		
	$db-> mysql_query("INSERT INTO `comentarios`.`tblcomentario` (`id`, `nombre`, `email`, `comentario`) VALUES (NULL, '$this->nombre', '$this->correo', '$this->comentario');");
						


// ejecutamos nuestra consulta para recuperar todos los registros de cédula
// que cumplan con el criterio especificado
if ( $db->select('tbl_dulces', '*', "nombre like '%" . $db->safe( $criterio ) . "%'") )
{
	$contador = 0;
	while ( $cedula = $db->fetch_row() ) // recupera el siguiente registro
	{
		if ($contador++ > 0) print ", "; // agregamos una coma entre cada registro
			
		// las rutinas de autcompletado de jQuery esperan que cada registro contenga los siguientes datos:
		// registro {
		//   label : "el texto que se desea desplegar en el control",
		//   value : { un objeto o cadena con los datos reales del registro }
		// }
		print "{ \"label\" : \"$cedula[nombre]\", \"value\" : { \"id\" : $cedula[id], \"descripcion\" : \"$cedula[nombre]\" } }";
	} // siguiente registro
}

print ']'; // <<<< observa que aquí termina la definición de nuestro resultado
?>