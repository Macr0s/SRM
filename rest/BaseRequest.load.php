<?php
	namespace Rest\Example;
	use Rest;

	class BaseRequest implements Rest\RestRequest{

		public function decode(){

		}

		public function getMethod(){
			return "get";
		}

		public function getUrl(){
			return "prova";
		}

		public function getData(){
			return array("data");
		}

		public function getProfile(){
			
		}

		public function setServer($server){
			
		}

		public function getKey(){

		}
	}
?>