<?php
	namespace PlayBonus;
	use Rest;

	class Apache2Session extends Rest\RestSession{
		private $id;

		public function init($key){
			session_start();
			$this->id = $key;
		}

		public function create(){
			$key = uniqid();
			$_SESSION[$key] = array();
			return $key;
		}

		public function get(){
			if (!$this->isValid()) return null;
			return (array_key_exists($this->id, $_SESSION))?(object)$_SESSION[$this->id]:null;
		}

		public function save($data){
			if (!$this->isValid()) return false;
			$_SESSION[$this->id] = $data;
			return true;
		}

		public function del(){
			if (!$this->isValid()) return false;
			unset($_SESSION[$this->id]);
			return true;
		}

		public function getField($name){
			if (!$this->isValid()) return null;
			return (array_key_exists($this->id, $_SESSION))?(array_key_exists($name, $_SESSION[$this->id]))?$_SESSION[$this->id][$name]:null:null;
		}

		public function saveField($name, $data){
			if (!$this->isValid()) return false;
			$_SESSION[$this->id][$name] = $data;
			return true;
		}

		public function delField($name){
			if (!$this->isValid()) return false;
			unset($_SESSION[$this->id][$name]);
			return true;
		}

		public function isValid(){
			return $this->id != null;
		}

		public function close(){
			
		}
	}
?>