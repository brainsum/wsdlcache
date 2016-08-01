<?php

// @see sample at https://github.com/laravel/laravel/blob/master/config/mail.php

return array(
  "driver" => env("MAIL_DRIVER", "smtp"),
  "host" => env("MAIL_HOST", "mailtrap.io"),
  "port" => env("MAIL_PORT", 2525),
  "from" => array(
    "address" => env("MAIL_FROM_ADDRESS", "from@example.com"),
    "name" => env("MAIL_FROM_NAME", "Example")
  ),
  "to" => array(
    "address" => env("MAIL_TO_ADDRESS", "from@example.com"),
    "name" => env("MAIL_TO_NAME", "Example")
  ),
  "username" => env("MAIL_USERNAME", null),
  "password" => env("MAIL_PASSWORD", null),
  "sendmail" => "/usr/sbin/sendmail -bs",
  "pretend" => false
);