<?php
	
	/**
	 * Funzione che serve per facilitare le operazioni di carica delle varie parti
	 * del sistema
	 */
	function load($dir){
		foreach(scandir("$dir") as $file){
			$split = explode(".", $file);
			if (is_file("$dir/$file") && count($split) >= 3 && $split[count($split) - 2] == "load"){
				require_once("$dir/$file");
			}
		}
	}

	function loadJSON($json){
		if (array_key_exists("load", $json)){
			foreach ($json['load'] as $value) {
				require_once($value);
			}
		}
	}

	function loadConstant($json){
		if (array_key_exists("constant", $json)){
			foreach ($json['constant'] as $key => $value) {
				define($key, $value);
			}
		}
	}

	require_once("rest.php");

	load("rest");
	// load("class");
	// load("endpoint");
	// load("request");
	// load("response");
	// load("session");
?>