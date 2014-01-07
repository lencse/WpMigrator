<?php


namespace Lencse\WpMigrator;


class File {

   /**
    * @var string
    */
   private $fileName;

   public function __construct($content) {
      $this->fileName = self::tempFileNameWithFullPath('wpm_sql_' . uniqid() . '.sql');
      file_put_contents($this->fileName, $content);
   }

   public static function tempFileNameWithFullPath($fileName) {
      return dirname(__FILE__) . str_repeat(DIRECTORY_SEPARATOR . '..', 3) .
         DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $fileName;
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