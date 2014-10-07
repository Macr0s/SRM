<?php
namespace Rest;

	/**
	* Classe che implementa la gestione del server RestFul: Questa classe è nata per
	* essere usata non solo in applicazione dove è necessario l'uso di server Rest ma
	* in qualunque tipo di gestione di url che sono collegate a delle endpoint specifiche
	* 
	* @version 0.3
	* @author Matteo Filippi aka Macros <info@mfilippi.guru>
	* @see RestSession
	* @see RestRequest
	* @see RestResponse
	* @see UrlParser
	* @package Rest
	*/
	class RestServer{

		/**
		* Variabile che contiene il gestore delle risposte dal server
		*/
		private $response;

		/**
		* Variabile che contiene il gestore delle richieste al server
		*/
		private $request;

		/**
		* Variabile che contiene il gestore della sessione dell'utente
		*/
		private $session;

		/**
		* Variabile che contiene le enpoint condivise da tutte tutti i profile
		*/
		private $endpoint;
		
		/**
		* Variabile che contiene il risultato delle endpoint
		*/
		private $info;

		/**
		* Variabile che contiene i vari profili delle endpoint
		*/
		private $profile;

		/**
		* Variabile che contiene il parser degli indirizzi o comunque dei dati
		*/
		private $parse;

		/**
		* Variabile che contiene un array di manager vari
		*/
		private $manager;

		/**
		* Costruttore della classe che ha come parametri tutti i vari gestori del server
		*
		* @param RestRequest $restrequest il gestore delle richieste
		* @param RestResponse $restresponse il gestore delle risposte
		* @param RestSession $restsession il gestore delle sessioni
		* @param UrlParser $parse il parse degli indirizzi
		* @package Rest
		*/
		public function __construct(RestRequest $restrequest, 
			RestResponse $restresponse, 
			RestSession $restsession, 
			UrlParser $parse){
			$this->request = $restrequest;
			$this->response = $restresponse;
			$this->session = $restsession;
			$this->parse = $parse;

			$restsession->setServer($this);
			$restsession->init($this->request->getKey());
			$restresponse->setServer($this);
			$restrequest->setServer($this);

			$this->endpoint = array();
			$this->profile = array();
			$this->manager = array();
		}

		/**
		* Metodo che imposta il risultato delle endpoint
		* 
		* @param int $status imposta lo stato della endpoint
		* @param mixed $data il contenuto informativo del contenuto della risposta della endpoint
		* @param mixed $error il messaggio d'errore o altre informazioni sull'errore che l'endpoint ha riscontrato
		* @return void
		*/
		public function setInfo($status, $data = null, $error = null){
			$i = array();
			$i['status'] = $status;
			$i['data'] = $data;
			$i['error'] = $error;
			$this->info = (object) $i;
		}

		/**
		* Metodo che restituire il gestore della risposta
		*
		* @return RestResponse il gesotre della risposta
		*/
		public function getResponse(){
			return $this->response;
		}

		/**
		* Metodo che restituisce il gestore delle richieste al server
		*
		* @return RestRequest il gestore della richiesta al server
		*/
		public function getRequest(){
			return $this->request;
		}

		/**
		* Metodo che restituisce il gestore della sessione
		*
		* @return RestSession il gestore della sessione
		*/
		public function getSession(){
			return $this->session;
		}

		/**
		* Metodo che aggiunge delle enpoint generiche che non dipendono dal profilo
		* di utilizzo
		*
		* @param string $url l'indirizzo dell'endpoint
		* @param RestEndpoint $class la classe che gestisce l'endpoint
		* @return string il codice che identifica l'endpoint appena aggiunta
		*/
		public function addEndpoint($url, RestEndpoint $class){
			$this->endpoint[$url] = $class;
			$class->setServer($this);
			$class->load($this);
			$class->setUrl($url);
			return sha1($url);
		}

		/**
		* Metodo che aggiunge un profilo di endpoint al server
		* 
		* @param RestProfile $profile il profilo da aggiungere al server
		* @return string il nome del profilo
		*/
		public function addProfile(RestProfile $profile){
			$this->profile[$profile->getName()] = $profile;
			$profile->setServer($this);
			return $profile->getName();
		}

		/**
		* Metodo che restituisce il profilo con il nome $name
		*
		* @param string $name il nome del profilo
		* @return RestProfile il profile delle endpoint
		*/
		public function getProfile($name){
			return (array_key_exists($name, $this->profile))?$this->profile[$name]: new RestProfile($name);
		}

		/**
		* Metodo che rimuove il profile con quel nome
		*
		* @param string $name il nome del profilo
		* @return true|false true se il profilo è stato rimosso altrimenti false
		*/
		public function delProfile($name){
			if (array_key_exists($name, $this->profile)){
				unset($this->profile[$name]);
				return true;
			}
			return false;
		}

		/**
		* Metodo che rimuove l'endpoint con quel id
		* 
		* @param string $id l'id dell'endpoint da rimuovere
		* @return true|false true se l'endpoint è stata rimossa altrimenti false
		*/
		public function delEnpoint($id){
			foreach($this->endpoint as $url => $class){
				if ($id == sha1($url)){
					unset($this->endpoint[$url]);
					return true;
				}
			}
			return false;
		}

		/**
		* Metodo che esegue l'elaborazione del Rest Server
		*
		* @return string il risultato finale del server
		*/
		public function run(){
			$method = $this->request->getMethod();
			$url = $this->request->getUrl();
			$data = $this->request->getData();

			$endpointProfile = $this->getProfile($this->request->getProfile())->getEndpoints();
			$endpoint = array_merge($this->endpoint, $endpointProfile);
			//$endpoint = $this->endpoint;
			
			foreach($endpoint as $end => $class){
				if ($this->session->isValid() || $class->isPublic()){
					$parse = $this->parse->parse($url, $class->getUrl());
					if ($parse != null){
						$class->$method((object) array_merge((array)$data, (array) $parse));	
						$r = $this->response->encode($this->info);	
						$this->session->close();
						return $r;
					}
				}
			}

			return $this->response->encode(array(
				"status" => 404,
				"data" => null,
				"error" => array("method" => $method, "url"=>$url)
				));
		}

		/**
		* Questo metodo serve per eseguire il server da un file JSON
		*
		* @param string|array $json indirizzo del file json
		* @return string il risultato finale del server
		*/

		public function runJSON($json){
			$method = $this->request->getMethod();
			$url = $this->request->getUrl();
			$data = $this->request->getData();
			$nameProfile = $this->request->getProfile();

			if (is_string($json)){
				try{
					$json = json_decode(file_get_contents($url), true);
				}catch(Expection $e){
					return $this->response->encode(array(
						"status" => 500,
						"data" => $e,
						"error" => array("method" => $method, "url"=>$url)
						));
				}
			}elseif(!is_array($json)){
				return $this->response->encode(array(
					"status" => 500,
					"data" => "no array",
					"error" => array("method" => $method, "url"=>$url)
					));
			}

			if (array_key_exists("endpoint", $json)){
				$endpoint = $json['endpoint'];
			}else{
				$endpoint = array();
			}

			if (array_key_exists("profile", $json) && array_key_exists($nameProfile, $json['profile'])){
				$endpoint = array_merge($endpoint, $json["profile"][$nameProfile]);
			}

			foreach ($endpoint as $end => $path) {
				$parse = $this->parse->parse($url, $end); 
				if ($parse != null){
					$data = (object) array_merge((array)$data, (array) $parse);
					if (file_exists($path)){
						$before = get_declared_classes();
						require_once($path);
						$new = array_diff(get_declared_classes(), $before);
						foreach ($new as $value) {
							if (is_subclass_of("$value", "Rest\RestEndpoint")){
								$class = new $value();
								if ($this->session->isValid() || $class->isPublic()){
									$class->load($this);
									$class->setServer($this);
									$class->$method($data);	
									$r = $this->response->encode($this->info);	
									$this->session->close();
									return $r;
								}
							}
						}
					}
				}
			}

			return $this->response->encode(array(
				"status" => 404,
				"data" => null,
				"error" => array("method" => $method, "url"=>$url)
				));
		}

		/**
		* Metodo che implementa le operazioni che devono essere eseguite quando il server conclude l'elaborazione
		* dello scrypt
		*/
		public function __destruct(){
			unset($this->request);
			unset($this->response);
			unset($this->session);
			unset($this->parse);
			unset($this->profile);
		}

		/**
		* Questa funzione magica riesce a gestire l'aggiunta di nuovi manager al sistema 
		* e anche la restituzione dei nuovi manager dal sistema
		*/
		public function __call($name,$param){
			$type = substr($name, 0, 3);
			switch ($type) {
				case "get":
				return (array_key_exists(substr($name, 3), $this->manager))?$this->manager[substr($name, 3)]:null;
				break;
				case "set":
				$name = substr($name, 3);
				$this->manager[$name] = $param[0];
				return true;
				break;
			}
		}

		public function getIP(){
			$ip = $_SERVER['REMOTE_ADDR'];
			if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)) {
				$ip = array_pop(explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']));
			}
			return $ip;
		}
	}

	?>