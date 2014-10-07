<?php
	namespace Rest;

	use Rest\RestServer;

	/**
	 * Classe che unifica tutte le endpoint 
	 *
	 * @author Matteo Filippi <info@mfilippi.guru>
	 * @see RestServer
	 * @see RestSession
	 * @version 0.3
	 * @package Rest
	 */
	abstract class RestEndpoint{
		/**
		* Variabile che contiene il collegamento al server
		*/
		private $server = null;

		/**
		* Variabile che contiene l'indirizzo dell'endpoint
		*/

		private $url;

		/**
		* Metodo che collega l'endpoint con il server
		* 
		* @param RestServer $server il server
		* @return void
		*/
		public function setServer(RestServer $server){
			$this->server = $server;
		}

		/**
		* Metodo che restituisce il gestore della sessione
		* 
		* @return RestSession il gestore della sessione
		*/
		public function getSession(){
			return $this->server->getSession();
		}

		/**
		* Metodo che restituisce il server
		*
		* @return RestServer il server
		*/
		public function getServer(){
			return $this->server;
		}

		/**
		* Metodo che gestisce l'endpoint nel caso che il metodo non può essere
		* dalla classe stessa
		*/
		public function __call($method, $parameter){
			$this->server->setInfo(404, null, array("method"=>$method, "data"=>$parameter[0]));
		}

		/**
		* Questo metodo restituisce l'indirizzo dell'endpoint
		* @return string l'url
		*/

		public function getUrl(){
			return $this->url;
		}

		/**
		* Questo metodo imposta l'indirizzo dell'endpoint
		* @param string $url l'indirizzo
		*/
		public function setUrl($url){
			$this->url = $url;
		}

		/**
		* Questo metodo definisce se l'endpoint è pubblica oppure no
		*
		* @return true|false true se è pubblica false se no
		*/
		public function isPublic(){
			return false;
		}

		/**
		* Questo metodo permette di caricare l'endpoint da file json
		*
		* @param RestServer $rest il server
		*/
		public function load(RestServer $rest){
			
		}
	}
?>