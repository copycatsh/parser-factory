<?php
$variables = [
    'MY_APP_TOKEN' => '<MY_APP_TOKEN>', // token from https://freelancehunt.com/my/api2
    'DB_HOST' => '<mysql-container-name>', //docker name of mysql container.
    'DB_USER_NAME' => 'root', // db userName
    'DB_PASSWORD' => 'root', // db password
    'DB_NAME' => 'parserdb', // db name
];

foreach ($variables as $key => $value) {
    putenv("$key=$value");
}
?>