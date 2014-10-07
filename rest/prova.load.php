<?php
	namespace Rest\Example;
	use Rest;

	class Prova extends Rest\RestEndpoint{
		public function get($data){
			$this->getServer()->setInfo(200, $data);
		}

		public function post($data){
			$this->getServer()->setInfo(200, $data);
		}
	}
?>