<?php
// auto-generated by LoggingConfigHandler
// date: 05/05/2015 12:54:02
$myapacheloglayout = new \Mojavi\Logging\ApacheLogLayout;
$myapacheloglayout->initialize(null);
$productionappender = new \Mojavi\Logging\StderrAppender;
$productionappender->initialize(array('priority' => '\\Mojavi\\Logging\\Logger::ERROR'));
$productionappender->setLayout($myapacheloglayout);
$prod = new \Mojavi\Logging\Logger;
$prod->setAppender("ProductionAppender", $productionappender);
$prod->setPriority(\Mojavi\Logging\Logger::ERROR);
\Mojavi\Logging\LoggerManager::setLogger("prod", $prod);
?>