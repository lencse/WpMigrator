<?php


namespace Lencse\WpMigrator\Test;

use Lencse\WpMigrator\Migrator as Migrator;
use Lencse\WpMigrator\Instance as Instance;


class MigratorTest extends \PHPUnit_Framework_TestCase {

   /**
    * @param TestInstance $src
    * @param TestInstance $trgt
    */
   private function executeTest(TestInstance $src, TestInstance $trgt) {
      $postTitle = 'Hello world! - ' . uniqid();
      $src->updatePostTitle($postTitle);
      $wpm = new Migrator($src->getWp(), $trgt->getWp(), 'wptest_');
      $wpm->migrate();
      $this->assertEquals($postTitle, $trgt->queryPostTitle());
   }

   public function testMigration() {
      $test1 = new TestInstance(
         new \PDO('mysql:host=localhost;dbname=kahuna', 'lencse', 'lencse'),
         new Instance(
            'http://phpmyadmin.local/',
            'http://localhost/wordpress',
            'kahuna',
            'lencse',
            'lencse'
         )
      );
      $test2 = new TestInstance(
         new \PDO('mysql:host=localhost;dbname=test', 'lencse', 'lencse'),
         new Instance(
            'http://phpmyadmin.local/',
            'http://localhost/wordpress2',
            'test',
            'lencse',
            'lencse'
         )
      );
      $this->executeTest($test1, $test2);
      $this->executeTest($test2, $test1);
   }

} 