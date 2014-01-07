<?php


namespace Lencse\WpMigrator;


class TestInstance {

   /**
    * @var \PDO
    */
   private $db;

   /**
    * @var Instance
    */
   private $wp;

   /**
    * @param \PDO $db
    * @param Instance $wp
    */
   function __construct(\PDO $db, Instance $wp) {
      $this->db = $db;
      $this->wp = $wp;
   }

   /**
    * @param $title string
    */
   public function updatePostTitle($title) {
      $this->db->exec("UPDATE wptest_posts SET post_title = '$title' WHERE id = 1");
   }

   /**
    * @return string
    */
   public function queryPostTitle() {
      return $this->db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();
   }

   /**
    * @return \hu\lokilevente\WpMigrator\Instance
    */
   public function getWp() {
      return $this->wp;
   }

}


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