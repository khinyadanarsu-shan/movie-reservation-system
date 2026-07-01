<?php
return [
  'db' => [
    'host'   => '127.0.0.1',
    'name'   => 'cinemax',
    'user'   => 'root',
    'pass'   => '', // XAMPP default
    'charset'=> 'utf8mb4',
  ],
  'mail' => [
    'host'      => 'smtp.gmail.com',
    'port'      => 587,
    'secure'    => 'tls',                 // <— use string, not class constant
    'username'  => 'yourgmail@gmail.com',
    'password'  => 'your_gmail_app_password_here',
    'from_email'=> 'yourgmail@gmail.com',
    'from_name' => 'CineMax Tickets',
  ],
];