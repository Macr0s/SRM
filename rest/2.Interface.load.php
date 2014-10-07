<?php
	namespace Rest;
	
	/**
	* Interfacce che collegano le richieste al server
	* @author: Matteo Filippi aka Macros <info@mfilippi.guru>
	* @see \Rest\RestServer
	* @version 0.3
	* @package Rest
	*/
	interface RestRequest{
		/**
		* Metodo che imposta la connessione al server
		* @param RestServer $server il server
		*/
		public function setServer($server);

		/**
		* Metodo che restituisce il tipo di metodo della
		* richiesta al server
		* @return string il tipo di metodo
		*/
		public function getMethod();

		/**
		* Metodo che restituisce l'url della richiesta al server
		* @return string url della richiesta
		*/
		public function getUrl();

		/**
		* Metodo che restituisce i dati della richiesta
		* @return object i dati
		*/
		public function getData();

		/**
		* Metodo che restituisce il profilo di endpoint da usare
		* @return string il nome del profile
		*/
		public function getProfile();

		/**
		* Metodo che restituisce la chiave della sessione
		*
		* @return string la chiave
		*/
		public function getKey();
	}

	/**
	* Questa interfaccia unifica tutte le rispose al server
	* ad una chiamata
	* 
	* @see RestServer
	* @version 0.3
	* @author Matteo Filippi aka Macr0s <info@mfilippi.guru>
	* @package Rest
	*/
	interface RestResponse{

		/**
		* Metodo che prepara il risultato dell'endpoint 
		* per l'invio al cliente
		* @param mixed[] $info i dati dell'endpoint
		* @return string la risposta processa e preparata per il client
		*/
		public function encode($info);

		/**
		* Metodo che collega questa classe con il server
		* @param RestServer $server il server
		* @return void
		*/
		public function setServer($server);
	}

	/**
	* Questa classe unifica tutte le varianti di parser degli url
	* 
	* @version 0.3
	* @see RestServer
	* @see RestEndpoint
	* @author Matteo Filippi aka Macr0s<info@mfilippi.guru>
	* @package Rest
	*/
	interface UrlParser{

		/**
		* Metodo che analizza l'url della richiesta e lo confronta con le endpoint
		*
		* @param string $url l'url della richiesta
		* @param string $r l'endpoint da effettuare il confronto
		* @return object|null Un oggetto nel caso che l'url corrente corrisponde 
		* a quello dell'endpoint con i paramentri nell'indirizzo altrimenti null
		*/
		public function parse($url, $r);
	}
?>