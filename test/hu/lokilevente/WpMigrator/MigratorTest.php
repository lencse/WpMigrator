<?php


namespace hu\lokilevente\WpMigrator;


class MigratorTest extends \PHPUnit_Framework_TestCase {

   public function testMigration() {
      $postTitle = 'Hello world! - ' . uniqid();

      $db = new \PDO('mysql:host=localhost;dbname=kahuna', 'lencse', 'lencse');
      $db->exec("UPDATE wptest_posts SET post_title = '$postTitle' WHERE id = 1");

      $source = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress',
         'kahuna',
         'lencse',
         'lencse'
      );
      $target = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress2',
         'test',
         'lencse',
         'lencse'
      );
      $wpm = new Migrator($source, $target, 'wptest_');
      $wpm->migrate();

      $db = new \PDO('mysql:host=localhost;dbname=test', 'lencse', 'lencse');
      $newTitle = $db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();

      $this->assertEquals($postTitle, $newTitle);

      $postTitle = 'Hello again, world! - ' . uniqid();

      $db = new \PDO('mysql:host=localhost;dbname=test', 'lencse', 'lencse');
      $db->exec("UPDATE wptest_posts SET post_title = '$postTitle' WHERE id = 1");

      $source = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress2',
         'test',
         'lencse',
         'lencse'
      );
      $target = new Instance(
         'http://phpmyadmin.local/',
         'http://localhost/wordpress',
         'kahuna',
         'lencse',
         'lencse'
      );
      $wpm = new Migrator($source, $target, 'wptest_');
      $wpm->migrate();

      $db = new \PDO('mysql:host=localhost;dbname=kahuna', 'lencse', 'lencse');
      $newTitle = $db->query("SELECT post_title FROM wptest_posts  WHERE id = 1")->fetchColumn();

      $this->assertEquals($postTitle, $newTitle);
   }

} 