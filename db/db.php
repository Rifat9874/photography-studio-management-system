<?php
// Database connection class (same style as class lab)
class DBConnection {
	private $db_host = 'localhost';
	private $db_username = 'root';
	private $db_password = '';
	private $db_name = 'photography_studio';

	public function connect() {
		$connection = new mysqli(
			$this->db_host,
			$this->db_username,
			$this->db_password,
			$this->db_name
		);
		return $connection;
	}
}
