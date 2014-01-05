<?php


namespace hu\lokilevente\WpMigrator;


header('Content-Type: text/plain');

include 'test/autoload.php';

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
