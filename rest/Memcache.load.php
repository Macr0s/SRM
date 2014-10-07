<?php
	namespace PlayBonus;
	use Rest\RestSession;
	use Tools\DB;

	class MemcacheSession extends RestSession{
		private $id;
		private $new;
		private $dati;
		private $mem;

		public function __construct($server, $port){
			$this->mem = new \Memcached();
			$this->mem->addServer($server, $port);
		}

		public function init($key){
			$this->id = $key;
			if ($key!=null && $this->new != $key){
				$data = $this->mem->get($key);
				if(!$data){
					$this->id = null;
				}else{
					$this->dati = json_decode(base64_decode($data),true);
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
			return $this->id != null;
		}

		public function close(){
			if ($this->id != null){
				$key = $this->id;
				$value = base64_encode(json_encode($this->dati));
				$this->mem->set($key, $value, 0, 3600);
			}
		}
	}
?>