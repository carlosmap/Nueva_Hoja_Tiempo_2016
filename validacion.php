<?
	/********** FUNCIONES DE VALIDACION DE USUARIO ********/
	// Comprueba que el usuario pertenezca al grupo de gerentes
	function esgerente($usuario) {
		$sql="select grupo from grupos where responsable='$usuario'";
		$resultado=mssql_query($sql);
		while ($filas=mssql_fetch_array($resultado)) {
			if (strtolower(trim($filas[grupo]))=="gerente") {
				return 1;
				break;
			}
		}
		return 0;
	}
	
	// Comprueba si el usuario es coordinador de algun proyecto
	function escoordinador($usuario,$proyecto="") {
		$sql="select nombreproyecto from proyectos where coordinador='$usuario' and estadoproyecto='a'";
		if ($proyecto<>"") $sql=$sql." and nombreproyecto='$proyecto'";
		
		$resultado=mssql_query($sql);
		$bien=0;
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=1;
			break;
		}
		return $bien;
	}
	
	/* Comprueba si el usuario $usuario es gerente del proyecto $proyecto
	   si $proyecto=="", solo se comprueba si el usuario es director o coordinador de algun proyecto
		si el usuario es gerente, es director de todos los proyectos */
	function esdirector($usuario,$proyecto="") {
		$sql="select nombreproyecto from proyectos
				where (responsable = '$usuario') and (estadoproyecto = 'a')";
		if ($proyecto<>"") $sql.=" and nombreproyecto='$proyecto'";
		$sql.=" or (coordinador = '$usuario') and (estadoproyecto = 'a')";
		if ($proyecto<>"") $sql.=" and nombreproyecto='$proyecto'";
		$resultado=mssql_query($sql);
		$bien=0;
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=1;
			break;
		}
		if ($bien==0 and esgerente($usuario)) $bien=1;
		return $bien;
	}
	
	/* Comprueba si el usuario $usuario es cliente del proyecto $proyecto
	   si $proyecto=="", solo se comprueba si el usuario es cliente de algun proyecto */
	function escliente($usuario,$proyecto="") {
		$sql="select nombreproyecto from proyectos where cliente='$usuario' and estadoproyecto='a'";
		if ($proyecto<>"") $sql=$sql." and nombreproyecto='$proyecto'";
		
		$resultado=mssql_query($sql);
		$bien=0;
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=1;
			break;
		}
		return $bien;
	}
	
	// Consulta si una actividad tiene asignado un responsable
	function tiene_responsable($proyecto,$idactividad) {
		$sql="select msp_text_fields.text_value from msp_text_fields inner join
			msp_tasks on msp_text_fields.text_ref_uid = msp_tasks.task_uid
			inner join msp_projects on msp_tasks.proj_id = msp_projects.proj_id and
			msp_text_fields.proj_id = msp_projects.proj_id where (msp_text_fields.text_category =0) and
			(msp_text_fields.text_field_id=188743792) and (msp_tasks.task_uid= '$idactividad') and
			msp_projects.proj_name='$proyecto'";
		$resultado=mssql_query($sql);
		$bien="";
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=$filas[text_value];
			break;
		}
		return $bien;
	}
	
	function nombre_responsable($proyecto,$idactividad) {
		$login=tiene_responsable($proyecto,$idactividad);
		if ($login=="") 
			return "";
		else {
			$sql="select rtrim(usuario.nombreusuario)+' '+rtrim(usuario.apellidos) as nombre
					from usuario where username='$login'";
			$resultado=mssql_query($sql);
			$bien="";
			while ($filas=mssql_fetch_array($resultado)) {
				$bien=$filas[nombre];
				break;
			}
			return $bien;
		}
	}
	
	/********** FUNCIONES DE VALIDACION DE ACTIVIDADES ********/
	function NombreActividad($uidtarea,$nombreproyecto) {
		$sql="select msp_tasks.task_name,msp_tasks.task_outline_num from msp_tasks inner join msp_projects on msp_tasks.proj_id = msp_projects.proj_id 
			where msp_tasks.task_uid='$uidtarea' and msp_projects.proj_name='$nombreproyecto'";
		$resultado=mssql_query($sql);
		$bien="";
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=$filas[task_outline_num]." - ".$filas[task_name];
		}
		return $bien;
	}
	
	// Busca el nombre completo del usuario conocida su unidad
	function nombreuser($loginname) {
		$sql="select nombreusuario,apellidos from usuario where username='$loginname'";
		$resultado=mssql_query($sql);
		$bien="";
		while ($filas=mssql_fetch_array($resultado)) {
			$bien=trim($filas[nombreusuario])." ".trim($filas[apellidos]);
		}
		return $bien;
	}
	
	// Devuelve la lista de actividades del proyecto $nombreproyecto de las cuales el usuario $loginname es responsable separadas por comas
	function listaactiv($loginname,$nombreproyecto) {
		$sql="select msp_tasks.task_uid
				from msp_projects inner join msp_text_fields inner join msp_tasks on msp_text_fields.text_ref_uid = msp_tasks.task_uid and 
					msp_text_fields.proj_id = msp_tasks.proj_id on msp_projects.proj_id = msp_tasks.proj_id 
				where (msp_text_fields.text_category = 0) and (msp_text_fields.text_field_id = 188743792) and (msp_text_fields.text_value = '$loginname') and 
					(msp_tasks.task_work > 0) and (msp_projects.proj_name = '$nombreproyecto') and (msp_tasks.task_is_summary = 0)";
		$resultado=mssql_query($sql);
		$lista="";
		while ($filas=mssql_fetch_array($resultado)) {
			$lista.=$filas[task_uid].",";
		}
		if (substr($lista,-1)==",") $lista=substr($lista,0,strlen($lista)-1);
		return "(".$lista.")";
	}
?>