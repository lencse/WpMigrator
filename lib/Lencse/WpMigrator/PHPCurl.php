<?php


namespace Lencse\WpMigrator;


class PHPCurl implements Curl {

   private $curl;

   public function __construct() {
      $this->curl = curl_init();
      $this->setOption(CURLOPT_RETURNTRANSFER, true);
      $this->setOption(CURLOPT_HEADER, false);
      $this->setOption(CURLOPT_FOLLOWLOCATION, true);
      $this->setOption(CURLOPT_COOKIEFILE, File::tempFileNameWithFullPath('wpm_cookie_' . uniqid()));
   }

   public function get($url) {
      $this->setOption(CURLOPT_URL, $url);
      $this->setOption(CURLOPT_HTTPGET, true);
      return curl_exec($this->curl);
   }

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