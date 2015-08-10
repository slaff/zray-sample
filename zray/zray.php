<?php

require __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Cms.php';

// Create new extension - disabled
$zre = new ZRayExtension('Cms');

$cms = new Bewotec_Cms();

// start tracing only when 'your_application_initial_method' is called, e.g. 'Mage::run()'
$zre->setEnabledAfter('Zend\Mvc\Application::init');

// Trace the 'traced_method' function
// zrayExtension::traceFunction($pattern, $onenter, $onleave);
$zre->traceFunction('Application\controller\SiteController::indexAction', 
                     function() {}, 
                     array($cms, 'onLeaveIndexAction'));

$zre->setMetadata(array(
    array(
        'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'logo.png',
    )
));