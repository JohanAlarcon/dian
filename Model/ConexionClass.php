<?php

class ConexionClass
{

	private   $Conex;
	private   $Host;
	private   $User;
	private   $Pass;
	private   $Dbname;
	protected $Rdbms;

	public function SetConexion($ip = 'localhost')
	{

		$this->Host   = $ip;
		$this->User   = "root";
		$this->Pass   = "";
		$this->Dbname = 'db-gmt';
		$this->Rdbms  = 'MYSQL';
	}

	public function GetConexion()
	{

		$this->Conex = mysqli_connect("$this->Host", "$this->User", "$this->Pass", $this->Dbname);

		return $this->Conex;
	}
}
