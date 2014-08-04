<?php

$conexion = mysql_connect("localhost","root","");
$bd = mysql_select_db("demos");


$resultado = "";
$producto = addslashes(htmlspecialchars($_POST["producto"]));
$precio = addslashes(htmlspecialchars($_POST["precio"]));
$categoria = addslashes(htmlspecialchars($_POST["list"]));
$codigo = addslashes(htmlspecialchars($_POST["codigo"]));


//aqui es para guardar
if ($producto!= "" && $precio!= "" && $categoria != "")
{
	$checkproducto = mysql_query("SELECT nombre FROM tbl_productos WHERE nombre='$producto'");
	$check_producto = mysql_num_rows($checkproducto);
	
	$checkcodigo = mysql_query("SELECT codigo FROM tbl_productos WHERE codigo='$codigo'");
	$check_codigo = mysql_num_rows($checkcodigo);
	
	if($check_producto>0 || $check_codigo>0)
	{
		echo $resultado = "<p>El Producto:".$producto." o Codigo :".$codigo." ya esta en uso</p>";
	}
	
	else
	{
		mysql_query("INSERT INTO tbl_productos VALUES(null,'$codigo','$producto','$precio','$categoria')");
		echo $resultado = "<p>El Producto: ".$producto." Se Agrego</p>";

	}
}

else
{
	echo $resultado = "<p>Algunos campos estan vacios</p>";
	 
}

?>

