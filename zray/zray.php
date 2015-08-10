<?php

namespace Bewotec;

require __DIR__.DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Cms.php';

// Create new extension - disabled
$zre = new \ZRayExtension('Bewotec');
$zre->setEnabledAfter('Zend\Mvc\Application::init');

$cms = new Cms();

// @TODO: Adjust the class name
$zre->traceFunction('Application\Controller\IndexController::indexAction', 
                     function($context, &$storage) {}, 
                     array($cms, 'onLeaveIndexAction')
);

// Collect information for the requests
// @TODO: Adjust the class name
//$zre->traceFunction('<ActualClassName>::doRequest',
$zre->traceFunction('Application\Service\Remote::doRequest',
                    function($context, &$storage) {},
                    array($cms, 'onLeaveDoRequest')
);

// Summarize that information
$zre->traceFunction('Bewotec\Cms::shutdown', 
                     function($context, &$storage) {},
                     array($cms, 'onLeaveShutdown')
);

register_shutdown_function(array($cms,'shutdown'));

$zre->setMetadata(array(
    array(
        'logo' => __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'logo.png',
    )
));