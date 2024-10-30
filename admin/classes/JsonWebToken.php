<?php
	
	namespace Infocob\CrmForms\Admin;
	
	if (!defined('ABSPATH') && !is_admin()) {
		die();
	}
	
	class JsonWebToken {
		/**
		 * @var string
		 */
		protected $token = "";
		/**
		 * @var string
		 */
		protected $secret = "";
		
		/**
		 * @var array|bool
		 */
		protected $header = false;
		/**
		 * @var array|bool
		 */
		protected $payload = false;
		/**
		 * @var string
		 */
		protected $signature = "";
		
		/**
		 * @return string
		 */
		public function getToken() {
			return $this->token;
		}
		
		/**
		 * @param string $token
		 */
		public function setToken($token) {
			$this->token = $token;
		}
		
		/**
		 * @return string
		 */
		public function getSecret() {
			return $this->secret;
		}
		
		/**
		 * @param string $secret
		 */
		public function setSecret($secret) {
			$this->secret = $secret;
		}
		
		/**
		 * @return array|bool
		 */
		public function getHeader() {
			return $this->header;
		}
		
		/**
		 * @param array|bool $header
		 */
		public function setHeader($header) {
			$this->header = $header;
		}
		
		/**
		 * @return array|bool
		 */
		public function getPayload() {
			return $this->payload;
		}
		
		/**
		 * @param array|bool $payload
		 */
		public function setPayload($payload) {
			$this->payload = $payload;
		}
		
		
		public function __construct($secret = null, $token = null) {
			if(!empty($token)) {
				$this->setToken($token);
			}
			
			if(!empty($secret)) {
				$this->setSecret($secret);
			}
		}
		
		public function isValid() {
			if(!empty($this->payload)) {
				$exp = !empty($this->payload["exp"]) ? $this->payload["exp"] : 0;
				if($exp > time()) {
					unset($this->payload["iat"]);
					unset($this->payload["exp"]);
					
					return true;
				}
			}
			
			return false;
		}
		
		public function encode($header = null, $payload = null) {
			if($header === null) {
				$header = $this->header;
			}
			$headerString = json_encode($header);
			$header64     = base64_encode($headerString);
			
			if($payload === null) {
				$payload = $this->payload;
			}
			$payloadString = json_encode($payload);
			$payload64     = base64_encode($payloadString);
			
			return $header64 . "." . $payload64 . "." . $this->generateSignature($header64, $payload64);
		}
		
		public function decode() {
			if(!$this->secret || !$this->token) {
				return false;
			}
			
			if(!preg_match('/^([A-Za-z0-9-_=]+)\.([A-Za-z0-9-_=]+)\.?([A-Za-z0-9-_.+\/=]*)$/mi', $this->token, $matches)) {
				return false;
			}
			
			$headerString64  = !empty($matches[1]) ? $matches[1] : false;
			$payloadString64 = !empty($matches[2]) ? $matches[2] : false;
			$this->signature = !empty($matches[3]) ? $matches[3] : false;
			
			$this->header  = !empty($headerString64) ? json_decode(base64_decode($headerString64), true) : false;
			$this->payload = !empty($payloadString64) ? json_decode(base64_decode($payloadString64), true) : false;
			
			if($this->header && $this->payload && $this->signature) {
				if($this->signatureIsValid($headerString64, $payloadString64, $this->signature)) {
					return true;
				}
			}
			
			$this->header    = false;
			$this->payload   = false;
			$this->signature = false;
			
			return false;
		}
		
		protected function signatureIsValid($header, $payload, $signature) {
			$signature_origin = $this->generateSignature($header, $payload);
			if($signature_origin === $signature) {
				return true;
			}
			
			return false;
		}
		
		protected function generateSignature($header, $payload) {
			return md5($header . "." . $payload . "." . $this->secret);
		}
		
		
	}
