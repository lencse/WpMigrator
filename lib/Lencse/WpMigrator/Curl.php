<?php


namespace Lencse\WpMigrator;


interface Curl {

   /**
    * @param $url string
    * @return mixed
    */
   public function get($url);

   /**
    * @param $url string
    * @param $params mixed
    * @return mixed
    */
   public function post($url, $params);

} 