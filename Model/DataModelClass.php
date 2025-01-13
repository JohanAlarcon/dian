<?php

class DataModel
{

	public $Conex;

	public function __construct()
	{
		/*require_once("../../../framework/clases/ConexionClass.php");
		$Conex = new Conexion();*/

		require_once("ConexionClass.php");
		$Conex = new ConexionClass();
		$Conex->SetConexion();
		$this->Conex['conex'] = $Conex->GetConexion();
		$this->Conex['Rdbms'] = 'MYSQL';
	}

	public function getDataResolucion()
	{

		$select  = "SELECT  * FROM  parametros_factura  WHERE estado = 'A' AND tipo_emision = 'D'";

		$result = $this->DbFetchAll($select, $this->Conex);

		return $result;
	}

	public function getDataEmpresa()
	{

		$select  = "SELECT  e.*, t.*,c.codigo AS codigo_ciiu, u.divipola,u.nombre AS city_name,u2.nombre AS departamento,u2.divipola AS divipola_dpto  FROM  empresa e 
					INNER JOIN tercero t ON e.tercero_id = t.tercero_id
                    LEFT JOIN ubicacion u ON u.ubicacion_id = t.ubicacion_id
                    LEFT JOIN ubicacion u2 ON u.ubi_ubicacion_id = u2.ubicacion_id
					LEFT JOIN codigo_ciiu c ON e.codigo_ciiu_id = c.codigo_ciiu_id";

		$result = $this->DbFetchAll($select, $this->Conex);

		return $result;
	}

	public function getDataAmbiente()
	{

		$select  = "SELECT  * FROM  param_factura_electronica  WHERE estado = 1";

		$result = $this->DbFetchAll($select, $this->Conex);

		return $result;
	}

	public function getDataCliente($cliente_id)
	{

		$select  = "SELECT t.*,cod.codigo AS codigo_ciiu, u.divipola,u.nombre AS city_name,u2.nombre AS departamento,u2.divipola AS divipola_dpto, 
					GROUP_CONCAT(co.codigo SEPARATOR ';') AS obligaciones
					FROM  cliente c 
					INNER JOIN tercero t ON c.tercero_id = t.tercero_id
					INNER JOIN ubicacion u ON u.ubicacion_id = t.ubicacion_id
					INNER JOIN ubicacion u2 ON u.ubi_ubicacion_id = u2.ubicacion_id
					LEFT JOIN tercero_obligacion tob ON tob.tercero_id = t.tercero_id
					LEFT JOIN codigo_obligacion co ON co.codigo_obligacion_id = tob.codigo_obligacion_id
					LEFT JOIN codigo_ciiu cod ON t.codigo_ciiu_id = cod.codigo_ciiu_id 
					WHERE c.cliente_id = $cliente_id";

		//exit($select);

		$result = $this->DbFetchAll($select, $this->Conex);

		return $result;
	}

	public function DbFetchAll($sql, $conex)
	{

		$data  = array();
		$result = mysqli_query($conex['conex'], $sql) or die(mysqli_error($conex['conex']) . "<br>$sql <br>");

		for ($i = 0; $Row = mysqli_fetch_assoc($result); $i++) {
			$data[$i] = $Row;
		}

		return $data;
	}
}
