<?php


namespace hu\lokilevente\WpMigrator;


class SourceConnection extends Connection{

   protected function getInstance() {
      return $this->migrator->getSource();
   }

} 