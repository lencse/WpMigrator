### Usage:

```php
<?php

$source = new \Lencse\WpMigrator\Instance(
   'http://phpmyadmin.my-host.com/',
   'http://my-awesome-blog.com',
   'mydatabase',
   'username',
   'password'
);
$target = new \Lencse\WpMigrator\Instance(
   'http://localhost/phpmyadmin',
   'http://dev.my-awesome-blog.com',
   'localdatabase',
   'devuser',
   'devpassword'
);
$wpm = new \Lencse\WpMigrator\Migrator($source, $target, 'myblog_');
$wpm->migrate();

```