<?php


namespace hu\lokilevente\WpMigrator;


class SqlFile {

   /**
    * @var string
    */
   private $fileName;

   public function __construct($content) {
      $this->fileName = dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 4) .
         DIRECTORY_SEPARATOR . 'sql' . DIRECTORY_SEPARATOR . 'wpm_sql_' . uniqid() . '.sql';
      file_put_contents($this->fileName, $content);
   }

   /**
    * @return string
    */
   public function getFileName() {
      return $this->fileName;
   }

   function __destruct() {
      unlink($this->fileName);
   }

}