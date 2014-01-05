<?php


namespace hu\lokilevente\WpMigrator;


class TargetConnection extends Connection{

   protected function getInstance() {
      return $this->migrator->getTarget();
   }

}