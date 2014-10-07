<?php
	namespace Rest;
	use Rest\RestServer;
	use Rest\RestEndpoint;
	
	/**
	* Questa classe gestisce la creazione dei profili degli
	* endpoint
	* 
	* @version 0.3
	* @author Matteo Filippi aka Macros <info@macros.name>
	* @see RestServer
	* @package Rest 
	*/
	class RestProfile{

		/**
		* Questa variabile contiene il nomde dell'endpoint
		*/
		private $name;

		/**
		* Questa variabile contiene l'insieme delle endpoint utilizzate
		* da questo profilo
		*/
		private $endpoint;

		/**
		* Questa variabile contiene l'istanza del server Rest
		*/
		private $server;

		/**
		* Questo costruttore definisce il nome del profilo e inizzializza
		* l'array di endpoint
		*/
		public function __construct($name){
			$this->name = $name;
			$this->endpoint = array();
		}

		/**
		* Questo metodo permette di aggiungere al profilo delle endpoint che lo definiscono
		* @param string $url l'indirizzo di chiamata dell'endpoint
		* @param RestEndpoint la classe che rappresenta l'endpoint
		*/
		public function addEndpoint($url, RestEndpoint $r){
			$this->endpoint[$url] = $r;
			$r->setServer($this->server);
			$r->load($this->server);
			$r->setUrl($url);
			return sha1($url);
		}

		/**
		* Questo metodo restituisce un array di endpoint
		* @return RestEndpoint[] sono un array associativo dove la chiave è l'url di riferimento e 
		* il valore è il gestore
		*/
		public function getEndpoints(){
			return $this->endpoint;
		}

		/**
		* Questo metodo restituisce il nome del profilo
		* @return string il nome del profilo
		*/
		public function getName(){
			return $this->name;
		}

		/**
		* Questo metodo collega il profilo e ogni endpoint in se all'istanza del server
		* @param RestServer $server il server
		*/
		public function setServer(RestServer $server){
			$this->server = $server;
			foreach ($this->endpoint as $value) {
				$value->load($server);
				$value->setServer($server);
			}
		}
	}
?>