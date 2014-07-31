$(document).ready(function() {
	$('#loginButton').click(function(){
   	 $('#Login_mostrar').slideToggle('fast');
	});

	// esta rutina se ejecuta cuando jquery esta listo para trabajar
		$(function() 
		{
			// configuramos el control para realizar la busqueda de los productos
			$("#txtProducto").autocomplete({
				source: "inventario.php", 				/* este es el formulario que realiza la busqueda */
				minLength: 3,									/* le decimos que espere hasta que haya 2 caracteres escritos */
				select: productoSeleccionado,	/* esta es la rutina que extrae la informacion del registro seleccionado */
				focus: productoMarcado
			});
		});
		
		// tras seleccionar un producto, calculamos el importe correspondiente
		function productoMarcado(event, ui)
		{
			var producto = ui.item.value;
			
			// no quiero que jquery maneje el texto del control porque no puede manejar objetos, 
			// asi que escribimos los datos nosotros y cancelamos el evento
			// (intenta comentando este codigo para ver a que me refiero)
			$("#txtProducto").val(producto.descripcion);
			event.preventDefault();
		}
		
		// tras seleccionar un producto, calculamos el importe correspondiente
		function productoSeleccionado(event, ui)
		{
			var producto = ui.item.value;
			var cantidad = $("#txtCantidad").val();
			
			// vamos a validar la cantidad con un procedimiento muy simple
		/*	cantidad = parseInt(cantidad, 10); // convierte este valor en un entero base 10 (un numero cualquiera)
			if (isNaN(cantidad)) cantidad = 0;
			
			var precio = producto.precio;
			var importe = precio * cantidad;
			
			// actualizamos los datos en el formulario
			$("#lblPrecio").text(precio);
			$("#lblImporte").text(importe);
			*/
			// no quiero que jquery maneje el texto del control porque no puede manejar objetos, 
			// asi que escribimos los datos nosotros y cancelamos el evento
			// (intenta comentando este codigo para ver a que me refiero)
			$("#txtProducto").val(producto.descripcion);
			event.preventDefault();
		}