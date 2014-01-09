<?php


namespace Lencse\WpMigrator;


abstract class Connection {

   /**
    * @var Migrator
    */
   protected $migrator;

   /**
    * @var string
    */
   protected $token;

   /**
    * @var Curl
    */
   protected $curl;

   /**
    * @param Migrator $migrator
    * @param Curl $curl
    */
   public function __construct(Migrator $migrator, Curl $curl) {
      $this->migrator = $migrator;
      $this->curl = $curl;

      $dom = new DOMParser($this->curl->get($this->getInstance()->getPhpMyAdminUrl()));
      $this->token = $dom->getInputValue('token');
   }

   /**
    * @return array
    */
   protected function getLoginArray() {
      return array(
         'pma_username' => $this->getInstance()->getUserName(),
         'pma_password' => $this->getInstance()->getPassword(),
      );
   }

   /**
    * @return Instance
    */
   abstract protected function getInstance();

} 