<?php

// @see sample at https://github.com/laravel/laravel/blob/master/config/mail.php

return array(
  "driver" => "smtp",
  "host" => "mailtrap.io",
  "port" => 2525,
  "from" => array(
    "address" => "from@example.com",
    "name" => "Example"
  ),
  "username" => "7c2729dd4a4c97",
  "password" => "58a4a5f7348225",
  "sendmail" => "/usr/sbin/sendmail -bs",
  "pretend" => false
);