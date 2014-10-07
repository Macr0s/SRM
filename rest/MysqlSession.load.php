<?php
	namespace PlayBonus;
	use Rest;
	use Tools\DB;

	class MysqlSession extends Rest\RestSession{
		private $id;
		private $new;
		private $dati;
		private $db;

		public function __construct($db){
			$this->dati = array();
			$this->db = $db;
		}

		public function init($key){
			$this->id = $key;
			if ($key!=null && $this->new != $key){
				$temp = $this->db->go("SELECT * FROM session WHERE name LIKE '$key'");
				if (count($temp) == 1){
					$this->dati = json_decode(base64_decode($temp[0]['value']),true);
				}
			}
		}

		public function create(){
			$key = uniqid();
			$this->new = $key;
			return $key;
		}

		public function get(){
			if (!$this->isValid()) return null;
			return $this->dati;
		}

		public function save($dati){
			if (!$this->isValid()) return false;
			$this->dati = $dati;
			return true;
		}

		public function del(){
			if (!$this->isValid()) return false;
			$this->dati = array();
			return true;
		}

		public function getField($name){
			if (!$this->isValid()) return null;
			return (array_key_exists($name, $this->dati))?$this->dati[$name]:null;
		}

		public function saveField($name, $dati){
			if (!$this->isValid()) return false;
			$this->dati[$name] = $dati;
			return true;
		}

		public function delField($name){
			if (!$this->isValid()) return false;
			unset($this->dati[$name]);
			return true;
		}

		public function isValid(){
			return $this->id != null && is_array($this->dati);
		}

		public function close(){
			if ($this->isValid()){
				$key = $this->id;
				$value = base64_encode(json_encode($this->dati));
				if ($key == $this->new){
					$time = time();
					$this->db->go("INSERT INTO session (name,value,created) VALUES('$key','$value', $time)");
				}else{
					$this->db->go("UPDATE session SET value ='$value' WHERE name = '$key'");
				}
			}
		}
	}
?>