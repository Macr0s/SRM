<?php
	namespace Rest\Example;
	use Rest;
	
	class BaseResponse implements Rest\RestResponse{
		
		public function encode($info){
			return json_encode($info);
		}

		public function setServer($server){
			
		}
	}
?>