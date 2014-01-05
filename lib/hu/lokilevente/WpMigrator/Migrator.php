<?php


namespace hu\lokilevente\WpMigrator;


class Migrator {

   /**
    * @var Instance
    */
   private $source;

   /**
    * @var Instance
    */
   private $target;

   /**
    * @var string
    */
   private $tablePrefix;

   public function __construct(Instance $source, Instance $target, $tablePrefix) {
      $this->source = $source;
      $this->target = $target;
      $this->tablePrefix = $tablePrefix;
   }

   public function migrate() {
      $src = new Connection($this->source, $this->tablePrefix);
      $src->exportSql();

      $trgt = new Connection($this->target, $this->tablePrefix);
      $trgt->loadSql($src->getSqlFileName());

      unlink($src->getSqlFileName());
   }

} 