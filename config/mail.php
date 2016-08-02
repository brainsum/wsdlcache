<?php

// @see sample at https://github.com/laravel/laravel/blob/master/config/mail.php

return array(
  "driver" => env("MAIL_DRIVER", null),
  "host" => env("MAIL_HOST", null),
  "port" => env("MAIL_PORT", null),
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
  "sendmail" => env("MAIL_SENDMAIL", null),
  "pretend" => false
);