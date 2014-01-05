<?php


namespace hu\lokilevente\WpMigrator;


class Instance {

   /**
    * @var string
    */
   private $phpMyAdminUrl;

   /**
    * @var string
    */
   private $wpUrl;

   /**
    * @var string
    */
   private $database;

   /**
    * @var string
    */
   private $username;

   /**
    * @var string
    */
   private $password;

   function __construct($phpMyAdminUrl, $wpUrl, $database, $username, $password) {
      $this->database = $database;
      $this->password = $password;
      $this->phpMyAdminUrl = $phpMyAdminUrl;
      $this->username = $username;
      $this->wpUrl = $wpUrl;
   }


   /**
    * @return string
    */
   public function getDatabase() {
      return $this->database;
   }

   /**
    * @return string
    */
   public function getPassword() {
      return $this->password;
   }

   /**
    * @return string
    */
   public function getPhpMyAdminUrl() {
      return $this->phpMyAdminUrl;
   }

   /**
    * @return string
    */
   public function getUsername() {
      return $this->username;
   }

   /**
    * @return string
    */
   public function getWpUrl() {
      return $this->wpUrl;
   }

}