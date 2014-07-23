<?php
 return array (
  'components' => 
  array (
    'db' => 
    array (
      'class' => 'CDbConnection',
      'connectionString' => 'mysql:host=localhost;dbname=ors;port=3306',
      'username' => 'root',
      'password' => 'root',
      'emulatePrepare' => true,
      'charset' => 'utf8',
      'enableParamLogging' => false,
      'enableProfiling' => false,
      'schemaCachingDuration' => 7200,
      'tablePrefix' => 'ore_',
    ),
  ),
  'language' => 'ru',
) ;
?>