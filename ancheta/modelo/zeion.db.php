<?php
/*
 +-----------------------------------------------------------------------+
 | zeion.db.php                                                          |
 |                                                                       |
 | Este libreria provee rutinas para el acceso a bases de datos MySQL.   |
 |                                                                       |
 | Parte de este código lo extraje de Round Cube que a su vez utilizaba  |
 | PEAR (si no recuerdo mal), lo que hice fue tomar las rutinas que me   |
 | parecían interesantes y las adapté a un modelo que a mí me pareció    |
 | funcional. Ellos publican bajo GPL, razón por la que tengo que        |
 | publicarlas también bajo esa licencia.                                |
 |                                                                       |
 | Publicado bajo una licencia GNU GPL                                   |
 |                                                                       |
 | Este software se proporciona "tal cual", sin ninguna garantía expresa |
 | o implícita. En ningún caso los autores serán responsables de los     |
 | daños derivados de la utilización de este software.                   |
 |                                                                       |
 +-----------------------------------------------------------------------+
 | Parte del código por: RoundCube Dev. - Switzerland                    |
 | Desarrollado por: Eduardo Ferron <eduardo.ferron@zeion.net>           |
 +-----------------------------------------------------------------------+

 2010-02-04 21:42 eferron
*/

/*
 * Tabla de codigos para las excepciones que manejaremos en este modulo
 *		- 520		-> Error al abrir una conexion a un servidor MySQL
 *		- 521		-> Error al seleccionar una base de datos
 */
 

/**
 * Esta clase encapsula los metodos para la recuperacion de registros
 */
class zeion_db_result
{
	private static $nextId = 0;
	
	public $id;
	public $result;
	public $mode;
	
	
	/**
	 * Constructor
	 *
	 * @history
	 *		2010.02.06	00:50		eferron
	 */
	public function __construct( $res, $mode )
	{
		$this->id = self::$nextId++;
		
		$this->result = $res;
		$this->mode   = $mode;
	}
	
	
	/**
	 * Recupera el numero de registros para el resultset especificado.
	 *
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->select('alumnos');
	 *		if ( $result->num_rows() > 0 )
	 *			...
	 *
	 * @history
	 *		2010.02.06	00:26		eferron
	 */
	function num_rows( ) 
	{
		return mysqli_num_rows( $this->result );    
	}


	/**
	 * Recupera el numero de registros afectados con esta consulta.
	 *
	 * @remarks
	 *		Esto en realidad no es util, porque este tipo de consultas van sobre la conexion
	 *		empleada, por lo que es mas eficiente obtener esta infomacion directo del objeto
	 *		de conexion utilizado.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->update('alumnos', array('nombre' => $nombre), array('id' => $id));
	 *		if ( $result->affected_rows() > 0 )
	 *			...
	 *
	 * @history
	 *		2010.02.06	00:52		eferron
	 */
	function affected_rows()
	{
		return zeion_db::get_instance()->affected_rows();
	}


	/**
	 * Recupera un arreglo con los datos del siguiente registro.
	 *
	 * @return 
	 *		Devuelve un arreglo con los datos o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->select('alumnos');
	 *		while ( $alumno = $result->fetch_named_row() )
	 *		{
	 *			$id = $alumno['id'];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	00.58		eferron
	 */
	function fetch_named_row()
	{
		return mysqli_fetch_assoc( $this->result );
	}


	/**
	 * Get an index array for one row
	 * If no query handle is specified, the last query will be taken as reference
	 *
	 * @param  mode 
	 *		Define el modo en el que se deberan recuperar los datos de los campos:
	 *		- MYSQL_BOTH hace que se recuperen tanto por nombre como por indice
	 *		- MYSQL_NUM hace que se recuperen los campos deacuerdo a su indice
	 *		- MYSQL_ASSOC hace que se recuperen los campos utilziando sus nombres
	 * @return 
	 *		Devuelve un arreglo con los datos o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->select('alumnos');
	 *		while ( $alumno = $result->fetch_row() )
	 *		{
	 *			$id = $alumno['id'];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.03	01:04		eferron
	 */
	function fetch_row( $mode=MYSQL_BOTH )
	{
		return mysqli_fetch_array( $this->result, $mode );
	}


	/**
	 * Recupera los campos del siguiente registro, utilizando sus indices
	 * para asociarlos campos.
	 *
	 * @return 
	 *		Devuelve un arreglo con los datos o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->select('alumnos');
	 *		while ( $alumno = $result->fetch_indexed_row() )
	 *		{
	 *			$id = $alumno[0];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	01:13		eferron
	 */
	function fetch_indexed_row()
	{
		return mysqli_fetch_row( $this->result );
	}


	/**
	 * Recupera el ultimo valor autogenerado en la consulta anterior.
	 *
	 * @return 
	 *		Devuelve un entero con el numero de ID auto generado, o FALSE si no hay alguno.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->insert('alumnos', array('nombre' => $nombre, 'edad' => $edad));
	 *		if ( $result->affected_rows() > 0 )
	 *		{
	 *			$id = $result->last_insert_id();
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.12	01:30		eferron
	 */
	function last_insert_id()
	{ 
		return zeion_db::get_instance()->last_insert_id( $this->mode );
	}


	/**
	 * Recupera el modo de conecion empleado en la consulta.
	 *
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$result = $db->select('alumnos', array('id', 'nombre', 'edad'));
	 *		if ( $result->isValid() )
	 *			...
	 *
	 * @history
	 *		2010.02.12	01:30		eferron
	 */
	function isValid()
	{
		return $this->result ? true : false;
	}


	/**
	 * Libera los recursos utilizados por este handler.
	 *
	 * @history
	 *		2010.11.24	00:47		eferron
	 */
	function free()
	{
		if ( $this->result ) mysqli_free_result( $this->result );
	}
	
} // end of class: zeion_db_result



class zeion_db
{
	static private $instance;
	
	// atributos de esta clase:
	private $settings    = array();
	private $connections = array();
	private $databases 	 = array();
	private $persistent  = false;
	private $debug_mode  = false;
	
	private $resultCount = 0;
	private $results = array();
	private $lastResult = false;
	
	private $db_error = false;
	private $db_error_msg = '';
	
	
	
	/**
	 * Esta funcion regresa la instancia unica de la aplicación.
	 *
	 * @remarks
	 *		Genera una instancia si no se ha creado una, utilizando para esto la información en el archivo de configuración.
	 * @return 
	 *		Devuelve la instancia de la clase.
	 * @example
	 *		El siguiente ejemplo muestra la forma de utilizarla:
	 *		
	 *		$db = zeion_db::get_instance();
	 *
	 * @history
	 *		2010.02.03	22:20	eferron
	 */
	public static function get_instance()
	{
		if ( !self::$instance ) 
		{
			self::$instance = new zeion_db();
		}
		return self::$instance;
	}

	
	/**
	 * Crea una instancia para administrar las conexiones a la base de datos.
	 *
	 * @remarks
	 *		La librería está diseñada de tal forma que podamos manejar hasta dos conexiones
	 *		a la base de datos, una para operaciones en modo lectura y otra para escritura.
	 *		Esto es así por los bloqueos en las tablas.
	 *
	 *		Supongamos que tienes una aplicación que realiza muchas (muchísimas) consultas pero
	 *		muy pocas son de escritura. Te conviene entonces separar tus conexiones para que evites
	 *		bloquear el menor tiempo posible las tablas, mientras que las dejas disponibles siempre
	 *		que sean lecturas.
	 *
	 *		En todo caso puedes hacer que la librería utilice tan solo una conexión proporcionando 
	 *		únicamente la información para la primera cadena de conexión.
	 *
	 *		== IMPORTANTE ==
	 *		Si deseas utilizar el archivo de configuración necesitarás importar a tu proyecto la
	 *		librería zeion.config.php
	 *
	 *		Las conexiones en modo persistente son aquellas donde la conexión al servidor no se cierra
	 *		terminada la consulta. Piensa no en transacciones, sino a nivel de sockets, la conexión
	 *		por red al servidor. Esto ayuda a optimizar muchos procesos, pero también trae a colación
	 *		detalles que deberás mantener vigiladas, como conexiones colgadas por ejemplo.
	 *
	 * @example
	 *		El siguiente ejemplo muestra la forma de utilizarla:
	 *		
	 *		// para crear una conexión utilizando el archivo de configuración
	 *		$db = new zeion_db();
	 *		
	 *		// para crear una conexión utilizando una cadena de conexión
	 *		$db = new zeion_db('usuario:contraseña@servidor/basededatos');
	 *		
	 *		// para crear una conexión en modo persistente
	 *		$db = new zeion_db('usuario:contraseña@servidor/basededatos', true);
	 *		
	 *		// para crear dos conexiones en modo persistente
	 *		$db = new zeion_db(
	 *			'usuario_escritura:contraseña@servidor/basededatos', 
	 *			'usuario_lectura:contraseña@servidor/basededatos', 
	 *			true);
	 *
	 *
	 * @history
	 *		2010.02.04	21:50		eferron
	 *		2012.11.14	07:21		eferron		Se modifica para que la libreria de configuracion sea opcional
	 */
	public function __construct( $dsnw='', $dsnr='', $persistent=false, $debugMode=false )
	{	
		// iniciaiza los objetos de conexion
		$this->connections =
			array(
				'r' => false,
				'w' => false
				);
			
		// recupera la informacion de conexion
		if ( empty( $dsnw ) ) 
		{
			// recuperamos la instancia de la libreria de configuración
			$CONFIG = zeion_config::get_instance();
			
			// con ella obtenemos el valor establecido para la cadena de conexion predeterminada
			$dsnw = $CONFIG->get( 'db_dsnw', '' );
			$dsnr = $CONFIG->get( 'db_dsnr', '' );
			$persistent = $CONFIG->get( 'db_persistent', false );
			$debugMode = $CONFIG->get( 'db_debug_mode', false );
			
			// si no se especifica una cadena para modo lectura, tomamos la misma que la de escritura
			if ( empty( $dsnr ) )
				$dsnr = $dsnw;
		}
		// si el segundo parametro es booleano, significa que no se especifico una segunda cadena de conexion
		elseif ( is_bool( $dsnr ) )
		{
			$persistent = $dsnr;
			$dsnr = $dsnw;
		}
		elseif ( empty( $dsnr ) ) 
			$dsnr = $dsnw;
		
		// evaluamos los parametros y guardamos la configuracion correspondiente
		$this->settings['r'] = $this->parseDSN( $dsnr );
		$this->settings['w'] = $this->parseDSN( $dsnw );
			
		// parametros de configuracion
		$this->persistent = $persistent ? true : false;
		$this->debug_mode = $debugMode ? true : false;
		
		// reiniciamos nuestras banderas:
		$this->lastresult = false;
	}
	
	
	/**
	 * Recupera las opciones de conexion de una cadena en formado DSN.
	 *
	 * @remarks
	 *		El formato DSN que utilizaremos es user:password@host/database
	 * @history
	 *		2010.02.04	21:50		eferron
	 */
	private function parseDSN( $dsn )
	{
		$parsed = 
			array(
				'server' 	 => 'localhost',
				'database' => 'sin_especificar',
				'username' => '',
				'password' => ''
				);
		if ( empty( $dsn ) ) return $parsed;
		
		// recupera el nombre del usuario y la contraseña
		if ( ( $at = strrpos( $dsn, '@' ) ) !== false ) 
		{
			$str = substr( $dsn, 0, $at );
			$dsn = substr( $dsn, $at + 1 );
			if ( ( $pos = strpos( $str, ':' ) ) !== false ) 
			{
				$parsed['username'] = rawurldecode( substr( $str, 0, $pos ) );
				$parsed['password'] = rawurldecode( substr( $str, $pos + 1 ) );
			} 
			else
				$parsed['username'] = rawurldecode($str);			
		}
		
		// recupera el nombre del usuario y la contraseña
		if ( !empty($dsn) ) 
		{
			if ( ( $at = strrpos( $dsn, '/' ) ) !== false ) 
			{
				$parsed['server']   = rawurldecode( substr( $dsn, 0, $at ) );
				$parsed['database'] = rawurldecode( substr( $dsn, $at + 1 ) );			
			}
			else
				$parsed['database'] = rawurldecode( $dsn );
		}
		
		return $parsed;
	}
	
	
	/**
	 * Reinicializa las banderas con informacion de excepcion.
	 *
	 * @history
	 *		2010.02.06	00:01		eferron
	 */
	function resetError()
	{
		$this->db_error = false;
		$this->db_error_msg = '';
	}
	
	
	/**
	 * Establece las banderas y mensaje de excepcion.
	 *
	 * @history
	 *		2010.02.06	00:11		eferron
	 */
	function setError( $code, $msg, $terminate=false, $file=__FILE__, $line=__LINE__ )
	{
		$this->db_error = true;
		$this->db_error_msg = $msg;
		
		raise_error(
			array(
				'code' => $code, 
				'type' => 'db',
				'message' => $msg, 
				'line' => $line,
				'file' => $file					
				), 
			$terminate
			);
	}
	
	
	/**
	 * Recupera un set de configuracion a utilizar en el modo especificado.
	 *
	 * @remarks
	 *		Si no se ha especificado el modo indicado, se toma el siguiente disponible.
	 * @history
	 *		2010.02.12	03:05		eferron
	 */
	private function get_settings( $mode )
	{	
		if ( $this->settings[ $mode ] ) 
			return $this->settings[ $mode ];
		
		// esta rutina devuelve el primer registro en el arreglo
		// (raro ¿no?, pero como el arreglo tiene un índice en modo texto no sé 
		// cual es la posición del primer elemento, así es más fácil)
		foreach( $this->settings as $setting )
			if ( $setting ) return $setting;
		
		return FALSE;
	}
	
	
	/**
	 * Iniciamos una sesion con el servidor MySQL.
	 *
	 * @param mode
	 *		Especifica el modo de conexion a la base de datos: 'r' para lectura y 'w' para escritura.
	 * @return
	 *		True, si la conexion se lleva a cabo de forma satisfactoria.
	 * @exceptions
	 *		Se lanza una excepcion con codigo 520 si no es posible abrir la conexion.
	 *		Se lanza una excepcion con codigo 521 si no es posible seleccionar la base de datos.
	 * @history
	 *		2010.02.04	22:37	eferron
	 *		2012.11.14	07:27	eferron		Se pone como predeterminada la conexion en modo escritura.
	 */
	function open( $mode = 'w' )
	{
		$this->lastresult = false;
		$this->resetError();
		
		// si la conexion ya esta abierta
		if ( $this->connections[ $mode ] ) return true;
		
		// recupera los datos de la conexión para el modo especificado
		if ( !( $settings = $this->get_settings( $mode ) ) )
		{			
			$this->setError( 524, 'No se ha especificado la informacion de la cadena de conexion a utilizar', false, __FILE__, __LINE__ );
			return false;
		}
		
		// intentamos establecer un enlace con el servidor de datos, en caso de error:
		$this->connections[ $mode ] = 
			$this->persistent ?
				mysqli_connect(
					"p:$settings[server]", 
					$settings['username'], 
					$settings['password']
					) 
				:
				mysqli_connect(
					$settings['server'], 
					$settings['username'], 
					$settings['password']
					)
				;
		if ( !$this->connections[ $mode ] )
		{			
			$this->setError(520, mysqli_connect_error(), false, __FILE__, __LINE__);
			return false;
		}
		
		// seleccionamos la base de datos:
		$this->databases[ $mode ] = 
			@mysqli_select_db(
				$this->connections[ $mode ],
				$settings['database']
				);
		if ( !$this->databases[ $mode ] )
		{
			$this->setError(520, mysqli_error($this->connections[ $mode ]), false, __FILE__, __LINE__);
			return false;
		}
		
		// cambiamos el modo a UTF8
		@mysqli_query("SET NAMES 'utf8'");
		
		// estas son variables por default:
		$this->rows = 0;
		
		// de otra forma, devolvemos el id del enlace:
		return true;
	}
	
	
	/**
	 * Cerramos la conexion especificada.
	 *
	 * @param mode
	 *		Especifica el modo de conexion a la base de datos: 'r' para lectura y 'w' para escritura.
	 * @history
	 *		2010.02.05	02:20	eferron
	 *		2012.11.14	07:27	eferron		Se pone como predeterminada la conexion en modo escritura.
	 */
	function close( $mode = 'w' )
	{
		$this->resetError();
		
		// si ya se ha iniciado una sesion, la cerramos:
		if ( $this->connections[ $mode ] )
		{
			mysqli_close( $this->connections[ $mode ] );
		
			// actualizamos nuestras banderas:
			$this->connections[ $mode ] = false;
			$this->lastresult = false;
		}
	}
	
	
	/**
	 * Activa/deactiva el modo debug
	 *
	 * @param boolean True si se desea grabar las sentencias en el log.
	 * @history
	 *		2010.02.05	02:35	eferron
	 */
	function set_debug($dbg = true)
	{
		$this->debug_mode = $dbg;
	}


	/**
	 * Verifica si hay algun error en la ultima consulta.
	 *
	 * @return  
	 *		En caso de error devuelve el ultimo mensaje, de otro modo FALSE.
	 * @history
	 *		2010.02.05	02:35	eferron
	 */
	function is_error()
	{
		return $this->db_error ? $this->db_error_msg : FALSE;
	}


	/**
	 * Recupera el codigo de error en la ultima consulta.
	 *
	 * @history
	 *		2010.02.05	02:35	eferron
	 */
	function get_error_code()
	{
		return $this->db_error;
	}
	
	
	/**
	 * Recupera el mensaje de error en la ultima consulta.
	 *
	 * @history
	 *		2010.02.05	02:35	eferron
	 */
	function get_error_message()
	{
		return $this->db_error_msg;
	}
	
	
	/**
	 * Obten la infomacion de la excepcion en formado JSON.
	 *
	 * @history
	 *		2010.02.25	18:49	eferron
	 */
	function get_error_json()
	{
		return "{ 'code' : $this->db_error, 'message' : '$this->db_error_msg' }";
	}
    

	/**
	 * Verifica si la conexion esta activa.
	 *
	 * @return
	 *		True si la conexion esta activa.
	 * @history
	 *		2010.02.05	02:35	eferron
	 */
	function is_connected( $mode = 'w' )
	{
		return ( $this->connections[ $mode ] );
	}


	/**
	 * Eejecutar una consulta limitando los resultados.
	 *
	 * @param  string  
	 * 		La consulta a ejecutar.
	 * @param  number  
	 *		El offset para los resultados de esta consulta.
	 * @param  number  
	 *		La cantidad de registros que se desea.
	 * @param  mixed   
	 *		Agregar todos los parametros necesarios.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @history
	 *		2010.02.05	02:51	eferron
	 */
	function limitquery($query, $numrows, $offset = false)
	{
		$this->resetError();

		// si se ha especificado un limite de registros
		$limit = '';
		if ( $numrows )
		{
			$limit = 'limit ' . ( $offset ? "$offset, " : '' ) . $numrows;
		}

		// tenemos toda la informacion,
		// ejecuta la consulta
		$query = "$quey $limit";


		return $this->query( $query );
	}

	
	/**
	 * Ejecuta una sentencia SQL.
	 *
	 * @param  string  
	 *		la sentencia a ejecutar
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$alumnos = $db->query("select * from alumnos where materia='$materia';");
	 *
	 * @history
	 *		2010.02.12	03:23		eferron
	 */
	function query( $query ) 
	{
		// verifica cual conexion debemos utilizar
		$mode = strtolower( substr( trim( $query ), 0, 6) ) == 'select' ? 'r' : 'w';
		if ( !$this->open( $mode ) ) return FALSE;

		// si estamos en modo DEBUG, loggeamos la sentencia
		if ( $this->debug_mode )
			trace( "Query: $query" );

		$result = mysqli_query( $this->connections[ $mode ], $query );
		if ( !$result )
		{
			$this->setError( 523, mysqli_error($this->connections[ $mode ]), false, __FILE__, __LINE__  );
			return false;
		}

		// agregamos el resultado a la coleccion
		return $this->add_result( $result, $mode );
	}

	
	/**
	 * Ejecuta un procedimiento almacenado.
	 *
	 * @param  procedure
	 * 		El nombre del procedimiento almacenado a ejecutar.
	 * @param params
	 *		La coleccion de parametros del procedimiento. Aqui es posible
	 *		indicar un string con la lista deseada, aunque lo mejor es pasar un arreglo con la 
	 *		lista de los campos deseados, para que estos sean validados.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$res_id = $db->call(
	 *			'obtenerMovimientos',
	 *			array( 'A', true )
	 *			);
	 *
	 * @history
	 *		2010.10.07	06:49	eferron
	 */
	function call($procedure, $params=NULL)
	{
		// el driver de mysqli (hasta el momento que escribo esta rutina) no soporta dos llamadas
		// de procedimiento almacenado consecutivas, mientras esto ocurra tendremos que cancelar
		// la conexion anterior antes de hacer una nueva llamada
		$this->close();
		
		// si se especificaron parametros, creamos un string para contener los argumentos
		$args = '';
		if ( !empty( $params ) )
		{			
			if ( is_array( $params ) )
			{
				$list = '';
				$count = 0;
				foreach ( $params as $name => $value )
				{
					$list .= ( $count ? ',' : '' ) . $this->quote( $value );
					$count++;
				}
				$args = $list;
			}
			else
				$args = $params;
		}

		// finalmente armamos el query
		$query = "call $procedure($args);";
		
		return $this->query( $query );
	}
	
	
	/**
	 * Ejecuta una consulta de tipo SELECT deacuerdo a los parametros recibidos.
	 *
	 * @param  table
	 * 		El nombre de la tabla a consultar.
	 * @param  fields
	 *		Especifica los campos a recuperar. Aqui es posible pasar un string
	 *		con la lista de los campos deseados o un arreglo con los nombres de dichos campos.
	 *		La diferencia esta en que si pasas un string, se utiliza sin validarlo. Si pasas
	 *		un arreglo, este se valida antes de que forme parte del query.
	 * @param params
	 *		La coleccion de parametros de la consulta para formar el segmento WHERE. Aqui es posible
	 *		indicar un string con la lista deseada en el where. Lo mejor es pasar un arreglo con la 
	 *		lista de los campos deseados, para que estos sean validados.
	 * @param groupBy
	 *		Los campos a utilizar para formar los grupos. Como en los otros argumentos, es posible
	 *		pasar un string con el valor deseado, pero lo mejor es pasar un arreglo con los nombres
	 *		de los campos deseados.
	 * @param orderBy
	 *		La lista de los campos mediantes los cuales se ordenaran los registros. Es posible pasar un
	 *		string con los nombres de los campos, o un arreglo con la informacion de los mismos.
	 * @param numrows
	 *		El maximo de registros a recuperar. Este valor se utliza para formar el segmento LIMIT.
	 * @param offset
	 *		El numero de registros a utilizar para el salto. Solo se aplica si se utiliza un $numrows.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$res_id = $db->select(
	 *			'tbl_movimientos',
	 *			array( 'id', 'description', 'available' ),
	 *			array( 
	 *				'available > 0',						// es posible especificar un string
	 *				array( 'provider', '<>', 'Microsoft' ) 	// o un arreglo con las opciones
	 *				),
	 *			array( 'city', 'state' ),
	 *			'date',										// es posible un arreglo, pero puse un string :)
	 *			100,										// con un maximo de 100 registros
	 *			1000										// recuperar resultados a partir del registro 1,000
	 *			);
	 * 
	 * @history
	 *		2010.02.05	02:49	eferron
	 */
	function select($table, $fields='*', $params=NULL, $groupBy=NULL, $orderBy=NULL, $numrows=0, $offset=0)
	{
		// ajusta la cadena a utilizar para el filtro de campos
		if ( is_array( $fields ) )
		{
			$list = '';			
			foreach( $fields as $i => $field )
				$list .= ( $i ? ',' : '' ) . $this->quote_identifier( $field );
				
			$fields = $list;
		}

		// si se especificaron parametros, creamos un segmento WHERE
		$where = '';
		if ( !empty( $params ) )
		{			
			if ( is_array( $params ) )
			{
				$list = '';
				$count = 0;
				foreach ( $params as $name => $value )
				{
					if ( is_array( $value ) )
					{
						$list .=
							( $count ? ',' : '' ) . 
							$this->quote_identifier( $value[0] ) . 
							( 
								count( $value ) > 2 ? 
									$value[1] . $this->quote( $value[2] ) : 
									'=' . $this->quote( $value[1] )
							);
					}
					else
						$list .= ( $count ? ',' : '' ) . $name . '=' . $this->quote( $value );
					$count++;
				} // next param
				
				$where = "WHERE $list";
			}
			else
				$where = "WHERE $params";
		}

		// si se ha especificado crear grupos
		$groups = '';
		if ( !empty( $groupBy ) )
		{
			if ( is_array( $groupBy ) )
			{
				$list = '';
				foreach( $groupBy as $i => $group )
					$list .= ( $i ? ',' : '' ) . $this->quote_identifier( $group );
				
				$groups = "GROUP BY $list";
			}
			else
				$groups = "GROUP BY $groupBy";
		}

		// si se ha especificdo ordenar los registros
		$order = '';
		if ( !empty( $orderBy ) )
		{
			if ( is_array( $orderBy ) )
			{
				$list = '';
				$count = 0;
				foreach( $orderBy as $field => $asc )
				{
					$list .= ( $count ? ',' : '' ) . $this->quote_identifier( $field ) . ( $asc ? ' ASC' : ' DESC' );
					$count++;
				} // next field
				
				$order = "ORDER BY $list";
			}
			else
				$order = "ORDER BY $orderBy";
		}

		// si se ha especificado un limite de registros
		$limit = '';
		if ( $numrows )
		{
			$limit = 'limit ' . ( $offset ? "$offset, " : '' ) . $numrows;
		}

		// sinalmente armamos el query
		$query = "SELECT $fields FROM $table $where $groups $order $limit";

		return $this->query( $query );
	}
	
	
	/**
	 * Ejecuta una consulta de tipo INSERT de acuerdo a los parametros recibidos.
	 *
	 * @param  table
	 * 		El nombre de la tabla en donde se insertaran los registros.
	 * @param  values
	 *		Especifica los valores a insertar en la tabla. Cuando se especifica un arreglo,
	 *		la clave debe ser el nombre del campo, de tal forma que sea posible armar el filtro
	 *		de campos en la consulta.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$res_id = $db->insert(
	 *			'tbl_movimientos',
	 *			array( 
	 *				'available' => 100,
	 *				'provider'  => 'Microsoft'
	 *				)
	 *			);
	 * 
	 * @history
	 *		2010.02.11	00:38	eferron
	 */
	function insert($table, $params)
	{
		// ajusta la cadena a utilizar para el filtro de campos
		$fields = '';
		$values = '';
		if ( is_array( $params ) )
		{
			$fields = '(';
			$count = 0;
			foreach( $params as $field => $value )
			{
				$fields .= ( $count ? ',' : '' ) . $this->quote_identifier( $field );
				$values .= ( $count ? ',' : '' ) . $this->quote( $value );
				$count++;
			}				
			$fields .= ')';
		}
		else
			$values = $params;

		// finalmente armamos el query
		$query = "INSERT INTO $table $fields VALUES ($values);";
				
		return $this->query( $query );
	}
	
	
	/**
	 * Ejecuta una consulta de tipo UPDATE deacuerdo a los parametros recibidos.
	 *
	 * @param  table
	 * 		El nombre de la tabla en donde se actualizaran los registros.
	 * @param  params
	 *		Especifica los valores a actualizar en la tabla. Cuando se especifica un arreglo,
	 *		la clave debe ser el nombre del campo, de tal forma que sea posible armar el filtro
	 *		de campos en la consulta.
	 * @param wheres
	 *		La lista de valores a utlilizar en los filtros. Si se pasa un string, este se
	 *		utilizara en el segmento WHERE. Si se especifica un arreglo, se creara y validaran
	 *		los valores para formar el filtro adecuado.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$res_id = $db->update(
	 *			'tbl_movimientos',
	 *			array( 
	 *				'available' => 100,
	 *				'provider'  => 'Microsoft'
	 *				),
	 *			array( 
	 *				'id' => 150
	 *				)
	 *			);
	 *
	 * @history
	 *		2010.02.11	00:38	eferron
	 */
	function update($table, $params, $wheres='')
	{
		// ajusta la cadena a utilizar para el filtro de campos
		$fields = '';
		if ( is_array( $params ) )
		{
			$count = 0;
			foreach( $params as $field => $value )
			{
				$fields .= ( $count ? ',' : '' ) . $this->quote_identifier( $field ) . '=' . $this->quote( $value );
				$count++;
			}				
		}

		// establece los filtros para la actualizacion 
		$where = '';
		if ( !empty( $wheres ) )
		{
			$where = 'WHERE ';
			if ( is_array( $wheres ) )
			{
				$count = 0;
				foreach( $wheres as $field => $value )
				{
					$where .= ( $count ? ',' : '' ) . $this->quote_identifier( $field ) . '=' . $this->quote( $value );
					$count++;
				}
			}
			else
				$where .= $wheres;
		}
		
		// finalmente armamos el query
		$query = "UPDATE $table SET $fields $where;";

		return $this->query( $query );
	}
	
	
	/**
	 * Ejecuta una consulta de tipo DELETE deacuerdo a los parametros recibidos.
	 *
	 * @param  table
	 * 		El nombre de la tabla en donde se eliminaran los registros.
	 * @param wheres
	 *		La lista de valores a utlilizar en los filtros. Si se pasa un string, este se
	 *		utilizara en el segmento WHERE. Si se especifica un arreglo, se creara y validaran
	 *		los valores para formar el filtro adecuado.
	 * @return zeion_db_result instance 
	 *		Handler con el resultado de la consulta.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$res_id = $db->delete(
	 *			'tbl_movimientos',
	 *			'id = 1'
	 *			);
	 *
	 * @history
	 *		2010.02.27	19:56	eferron
	 */
	function delete($table, $wheres='')
	{
		// ajusta la cadena a utilizar para el filtro de campos
		$where = '';
		if ( !empty( $wheres ) )
		{
			$where = 'WHERE ';
			if ( is_array( $wheres ) )
			{
				$count = 0;
				foreach( $wheres as $field => $value )
				{
					$where .= ( $count ? ',' : '' ) . $this->quote_identifier( $field ) . '=' . $this->quote( $value );
					$count++;
				}
			}
			else
				$where .= $wheres;
		}

		// finalmente armamos el query
		$query = "DELETE FROM $table $where;";

		return $this->query( $query );
	}
	

	/**
	 * Recupera el resultset especificado.
	 *
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @history
	 *		2010.02.06	01:47		eferron
	 */
	function get_result( $res_id=NULL ) 
	{
		if ( $res_id && ( $item = $this->results[ $res_id ] ) )
			return $item;
			
		elseif ( $this->lastResult )
			return $this->lastResult;

		return FALSE;
	}
		
	
	/**
	 * Agrega un elemento a la coleccion de resultados.
	 *
	 * @result
	 *		Regresa el ID del resultado en la coleccion.
	 * @history
	 *		2010.02.06	02:45		eferron
	 */
	private function add_result( $result, $mode )
	{
		$this->lastResult = new zeion_db_result( $result, $mode );

		$this->results[ $this->lastResult->id ] = $this->lastResult;
		return $this->lastResult;
	}
	
	
	/**
	 * Recupera el numero de registros para el resultset especificado.
	 *
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$alumnos = $db->select('alumnos');
	 *		if ( $db->num_rows() > 0 )
	 *			...
	 *
	 * @history
	 *		2010.02.06	00:26		eferron
	 */
	function num_rows( $res_id=NULL ) 
	{
		if ( $result = $this->get_result( $res_id ) )
		return $result->num_rows();			
		return FALSE;
	}


	/**
	 * Recupera el numero de registros afectados en el ultimo resultset.
	 *
	 * @remarks
	 *		Esta consulta es a nivel conexion, por lo que recupera los registros
	 *		afectados en la ultima sentencia INSER, UPDATE, DETELE o REPLACE.
	 *
	 *		El modo es W porque solo en esta modalidad se permiten modificaciones a la base de datos.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$db->update('alumnos', array('nombre' => $nombre), array('id' => $id));
	 *		if ( $db->affected_rows() > 0 )
	 *			...
	 *
	 * @history
	 *		2010.02.06	00:31		eferron
	 */
	function affected_rows()
	{
		return (int) mysqli_affected_rows( $this->connections['w'] );
	}


	/**
	 * Recupera el ultimo ID autogenerado (para campos de tipo autoincremental).
	 * 
	 * @remarks
	 *		El modo es W porque solo en esta modalidad se permiten modificaciones a la base de datos.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$db->insert('alumnos', array('nombre' => $nombre, 'edad' => $edad));
	 *		if ( $db->affected_rows() > 0 )
	 *		{
	 *			$id = $db->last_insert_id();
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	01:43		eferron
	 */
	function last_insert_id()
	{
		if ( !$this->open( 'w' ) )
			return FALSE;

		return mysqli_insert_id( $this->connections[ 'w' ] );
	}


	/**
	 * Recupera los campos del siguiente registro asociandolos con sus nombres correspondientes.
	 *
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @return
	 *		Devuelve un arreglo con los campos del registro o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$db->select('alumnos');
	 *		while ( $alumno = $db->fetch_named_row() )
	 *		{
	 *			$id = $alumno['id'];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	01:56		eferron
	 */
	function fetch_named_row( $res_id=NULL )
	{
		if ( $result = $this->get_result( $res_id ) )
			return $result->fetch_named_row();
		return FALSE;
	}


	/**
	 * Recupera los campos del siguiente registro asociandolos con sus nombres correspondientes.
	 *
	 * @param  mode 
	 *		Define el modo en el que se deberan recuperar los datos de los campos:
	 *		- MYSQL_BOTH hace que se recuperen tanto por nombre como por indice
	 *		- MYSQL_NUM hace que se recuperen los campos deacuerdo a su indice
	 *		- MYSQL_ASSOC hace que se recuperen los campos utilziando sus nombres
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @return
	 *		Devuelve un arreglo con los campos del registro o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$db->select('alumnos');
	 *		while ( $alumno = $db->fetch_row() )
	 *		{
	 *			$id = $alumno['id'];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	01:56		eferron
	 */
	function fetch_row( $res_id=NULL, $mode=MYSQL_BOTH )
	{
		if ( $result = $this->get_result( $res_id ) )
			return $result->fetch_row();			
		return FALSE;
	}


	/**
	 * Recupera los campos del siguiente registro asociandolos con su indice correspondiente.
	 *
	 * @remarks
	 *		Si no se especifica un id de resultado, se tomara el ultimo procesado.
	 * @return
	 *		Devuelve un arreglo con los campos del registro o False si ya no hay mas registros.
	 * @example
	 *		El siguiente ejemplo utiliza todos los argumentos de esta rutina:
	 *		
	 *		$db->select('alumnos');
	 *		while ( $alumno = $db->fetch_indexed_row() )
	 *		{
	 *			$id = $alumno[0];
	 *			...
	 *		}
	 *
	 * @history
	 *		2010.02.06	01:56		eferron
	 */
	function fetch_indexed_row($result, $mode)
	{
		if ( $result = $this->get_result( $res_id ) )
			return $result->fetch_indexed_row();			
		return FALSE;
	}


	/**
	 * Aplica comillas a un valor, de tal manea que sea seguro utilizarlo.
	 *
	 * @remarks
	 *		Elimina incluso valores que resultarian peligrosos para una consulta.
	 * @history
	 *		2010.02.06	02:08		eferron
	 */
	function quote( $value )
	{
		$value = 
			preg_replace( 
				array('/\'/', '/\"/'),
				array("\\\'", "\\\""),
				$value
				);
		return "'$value'";
	}
	
	
	/**
	 * Aplica una expresion regular de tal manea que sea seguro utilizar el parametro especificado.
	 *
	 * @remarks
	 *		Elimina valores que resultarian peligrosos para una consulta.
	 * @history
	 *		2010.03.01	10:08		eferron
	 */
	function safe( $value )
	{
		return
			preg_replace( 
				array( '/\'/', '/\"/', '/;/', '/--/' ),
				'',
				$value
				);
	}


	/**
	 * Aplica comillas al nombre de un identificador, de tal manea que sea seguro utilizarlo.
	 *
	 * @history
	 *		2010.02.06	02:08		eferron
	 */
	function quote_identifier( $str )
	{
	return "`$str`";
	}


	/**
	 * Recupera el nombre de la funcion que utilizaremos para recuperar la fecha del servidor.
	 *
	 * @return 
	 *		Devuelve un string con la definicion dde la funcion a utilizar.
	 * @history
	 *		2010.02.06	02:21		eferron
	 */
	function now()
	{
		return "now()";
	}


	/**
	 * Crea un string con los datos del arreglo de tal forma que pueda emplearse en sentencias IN.
	 *
	 * @history
	 *		2010.02.06	02:27		eferron
	 */
	function array2list( $arr )
	{
		if ( !is_array( $arr ) )
			return $this->quote( $arr );

		$res = array();
		foreach ( $arr as $item )
			$res[] = $this->quote( $item );

		return implode(',', $res);
	}


	/**
	 * Recupera una sentencia que se debe utilizar para recuperar una fecha unix
	 * para el campo especificado.
	 *
	 * @history
	 *		2010.02.06	02:30		eferron
	 */
	function unixtimestamp( $field )
	{
		return "UNIX_TIMESTAMP( $field )";
	}


	/**
	 * Recupera una sentencia para recuperar un valor desde un valor unix.
	 *
	 * @history
	 *		2010.02.06	02:32		eferron
	 */
	function fromunixtime( $timestamp )
	{
		return sprintf("FROM_UNIXTIME(%d)", $timestamp);
	}


	/**
	 * Recupera una sentencia para armar una comparacion LIKE.
	 *
	 * @history
	 *		2010.02.06	02:34		eferron
	 */
	function like( $column, $value )
	{
		return $this->quote_identifier( $column ).' LIKE '.$this->quote( $value );
	}
	
	
	/**
	 * Recupera una sentencia para armar una comparacion BETWEEN.
	 *
	 * @history
	 *		2010.02.06	02:34		eferron
	 */
	function between( $column, $start, $end )
	{
		return $this->quote_identifier( $column ).' BETWEEN '.$this->quote( $start ).' AND '.$this->quote( $end );
	}
	
	
	/**
	 * Recupera una sentencia para armar una comparacion IN.
	 *
	 * @history
	 *		2010.02.06	02:34		eferron
	 */
	function in( $value, $options )
	{
		return $this->quote( $value ).' IN ('.$this->array2list( $options ).')';
	}


	/**
	 * Codifica los valores no compatiles a UTF-8.
	 *
	 * @remarks
	 *		Esta rutina es recursiva.
	 * @history
	 *		2010.02.06	02:43		eferron
	 */
	function encode( $input )
	{
		if (is_object($input)) 
		{
			foreach ( get_object_vars( $input ) as $idx => $value )
				$input->$idx = $this->encode($value);
			return $input;
		}
		else if ( is_array( $input ) ) 
		{
			foreach ($input as $idx => $value)
				$input[$idx] = $this->encode( $value );
			return $input;	
		}

		return utf8_encode( $input );
	}


	/**
	 * Decodifica los valores previamente codificados a UTF-8.
	 *
	 * @history
	 *		2010.02.06	02:45		eferron
	 */
	function decode( $input )
	{
		if ( is_object( $input ) ) 
		{
			foreach ( get_object_vars( $input ) as $idx => $value )
				$input->$idx = $this->decode( $value );
			return $input;
		}
		else if (is_array($input)) 
		{
			foreach ( $input as $idx => $value )
				$input[$idx] = $this->decode( $value );
			return $input;	
		}

		return utf8_decode( $input );
	}
	
}// end of class: zeion_db
?>