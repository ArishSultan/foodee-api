<?php
return array(
    'driver' => 'smtp',
    'host' => 'smtp.gmail.com',
    'port' => 587,
    'from' => array('address' => 'Foodeeapp5@gmail.com', 'Foodee' => 'amit'),
    'encryption' => 'tls',
    'username' => 'Foodeeapp5@gmail.com',
    'password' => 'foodee123',
    'sendmail' => '/usr/sbin/sendmail -bs',
    'pretend' => false,

);
