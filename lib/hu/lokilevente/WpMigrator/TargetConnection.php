<?php


namespace hu\lokilevente\WpMigrator;


class TargetConnection extends Connection{

   protected function getInstance() {
      return $this->migrator->getTarget();
   }

   /**
    * @param $sqlFileName string
    */
   public function loadSql($sqlFileName) {
      $this->curl->post(
         $this->getInstance()->getPhpMyAdminUrl() . "server_import.php",
         $this->getLoginArray()
      );

      $this->curl->post(
         $this->getInstance()->getPhpMyAdminUrl() . "import.php",
         array(
            'db' => $this->getInstance()->getDatabase(),
            'token' => $this->token,
            'import_type' => 'database',
            'charset_of_file' => 'utf-8',
            'allow_interrupt' => 'yes',
            'skip_queries' => '0',
            'format' => 'sql',
            'sql_compatibility' => 'NONE',
            'sql_no_auto_value_on_zero' => 'something',
            'import_file' => '@' . $sqlFileName,
         )
      );
   }

}