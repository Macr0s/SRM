<?php
	namespace Rest;
	
	/**
	* Questa classe unificate tutti i possibili gestori della sessione
	* 
	* @author Matteo Filippi aka Macr0s<info@mfilippi.guru>
	* @see RestServer
	* @version 0.3
	* @package Rest
	*/
	abstract class RestSession{
		
		/**
		* Variabile che tiene una temporanea degli elementi che vengono salvati nella sessione
		* di cache
		*/
		private $data;
		
		/**
		* Variabile che permette di accedere alla gestione del server dalla classe corrente
		*/
		private $server;

		/**
		* Costruittore che inizializza la variabile $data come un array di generico
		*/
		public function __construct(){
			$this->data = array();
		}

		/**
		* Metodo che inizializza la sessione con la chiave corrente
		* 
		* @param string $key la chiave della sessione
		* @return void
		*/
		abstract public function init($key);

		/**
		* Metodo che crea una nuova sessione restituendo la sua chiave
		*
		* @return string La chiave della sessione
		*/
		abstract public function create();

		/**
		* Metodo che restituisce tutto il contenuto della sessione
		*
		* @return object|null il contenuto della session se la sessione è stata inizializzata correttamente altrimenti null
		*/
		abstract public function get();

		/**
		* Metodo che salva il catenuto di $data come contenuto della sessione corrente
		* 
		* @param mixed[] $data il contenuto della sessione da salvare
		* @return true|false true in caso che il salvataggio è stato completato altrimenti false
		*/
		abstract public function save($data);

		/**
		* Metodo che calcella la sessione corrente
		*
		* @return true|false true se la sessione è stata cancellata altrimenti false
		*/
		abstract public function del();

		/**
		* Metodo che prende solo un campo della sessione corrente
		* 
		* @param string $name il nome del parametro in cache
		* @return object|null object nel caso di avvenuta lettura altrimenti null
		*/
		abstract public function getField($name);

		/**
		* Metodo che salva il campo nella cache corrente
		* 
		* @param string $name il nome del campo da salvare
		* @param mixed $data il dato da salvare
		* @return true|false true se è stato salvato altrimenti false
		*/
		abstract public function saveField($name, $data);

		/**
		* Metodo che cancella il campo dalla cache corrente
		* 
		* @param string $name il nome del campo
		* @return true|false true se il campo è stato cancellato con successo altrimenti false
		*/
		abstract public function delField($name);

		/**
		* Metodo che verifica la validatà della sessione
		*
		* @return true|false true se la sesione è valida altrimenti false
		*/
		abstract public function isValid();

		/**
		* Metodo che viene eseguito quando il sistema viene terminato
		*
		* @return void
		*/
		abstract public function close();

		/**
		* Metodo che salva internamente temporaneamente dei dati dentro il sistema
		*
		* @param string $id l'id del dato
		* @param mixed $data il dato da salvare
		* @return true restiuisce sempre true perchè è un valtaggio sempre eseguito
		*/
		public function setData($id, $data){
			$this->data[$id] = $data;
			return true;
		}

		/**
		* Metodo che prende i dati internamente salvati temporaneamente
		* 
		* @param string $id l'id del dato
		* @return mixed|false mixed se il dato è stato trovato altrimenti false
		*/
		public function getData($id){
			return (in_array($this->data, $id))?$this->data[$id]:false;
		}

		/**
		* Metodo che collega la classe con la classe RestServer
		*
		* @param RestServer $server il server
		* @return void
		*/
		public function setServer($server){
			$this->server = $server;
		}

		/**
		* Metodo che restistuisce il server collegato con la classe corrente
		*
		* @return RestServer il server
		*/
		public function getServer(){
			return $this->server;
		}
	}
?>