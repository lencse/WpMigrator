<?php


namespace hu\lokilevente\WpMigrator;


class Curl {

   private $curl;

   public function __construct() {
      $this->curl = curl_init();
      $this->setOption(CURLOPT_RETURNTRANSFER, true);
      $this->setOption(CURLOPT_HEADER, false);
      $this->setOption(CURLOPT_FOLLOWLOCATION, true);
      $this->setOption(CURLOPT_COOKIEFILE, dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 4) .
         DIRECTORY_SEPARATOR . 'cookie_dir' . DIRECTORY_SEPARATOR . 'wpm_cookie_' . uniqid());
   }

   /**
    * @param $url string
    * @return mixed
    */
   public function get($url) {
      $this->setOption(CURLOPT_URL, $url);
      $this->setOption(CURLOPT_HTTPGET, true);
      return curl_exec($this->curl);
   }

   /**
    * @param $url string
    * @param $params mixed
    * @return mixed
    */
   public function post($url, $params) {
      $this->setOption(CURLOPT_URL, $url);
      $this->setOption(CURLOPT_POST, true);
      $this->setOption(CURLOPT_POSTFIELDS, $params);
      return curl_exec($this->curl);
   }

   private function setOption($option, $value) {
      curl_setopt($this->curl, $option, $value);
   }

   function __destruct() {
      curl_close($this->curl);
   }

}