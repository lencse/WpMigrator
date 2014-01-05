<?php


namespace hu\lokilevente\WpMigrator;


class Curl {

   private $curl;

   public function __construct() {
      $this->curl = curl_init();
      curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($this->curl, CURLOPT_HEADER, false);
      curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($this->curl, CURLOPT_COOKIEFILE, dirname(__FILE__) . DIRECTORY_SEPARATOR . 'cookie_dir' .
         DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid());
   }

   /**
    * @param $url string
    * @return mixed
    */
   public function get($url) {
      curl_setopt($this->curl, CURLOPT_URL, $url);
      curl_setopt($this->curl, CURLOPT_HTTPGET, true);
      return curl_exec($this->curl);
   }

   /**
    * @param $url string
    * @param $params mixed
    * @return mixed
    */
   public function post($url, $params) {
      curl_setopt($this->curl, CURLOPT_URL, $url);
      curl_setopt($this->curl, CURLOPT_POST, true);
      curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
      return curl_exec($this->curl);
  }

   function __destruct() {
      curl_close($this->curl);
   }

}