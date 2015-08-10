<?php

namespace Bewotec;

use Zend\Json\Json;

require_once __DIR__.'/LSS.php';

class Cms {
    protected $requests;
    protected $requestCount = 0;
    
    /**
     * This method is called right after the finish of the indexAction 
     * @param unknown $context
     * @param unknown $storage
     */
    public function onLeaveIndexAction($context, &$storage) {
        $storage['CMS'][] = array (
            'SiteCode' => $context['locals']['settings']['sitecode'],
            'Settings'     => isset($context['locals']['settings']) ?
                                    $context['locals']['settings']    : array() ,
            'GlobalOffers' => isset($context['locals']['globalOffers']) ?
                                    $context['locals']['globalOffers'] : array()
        );
        
        $storage['meinereisedaten'] = array(
            $context['locals']['agencyData']
        );
    } 
    
    /**
     * This method is called once a request is executed
     * @param unknown $context
     * @param unknown $storage
     */
    public function onLeaveDoRequest($context, &$storage)
    {
        $client = $context['locals']['client'];
        
        /*
         * @var Zend\Http\Request
         */
        $response = $client->getResponse();
        
        /*
         * @var Zend\Http\Response
         */
        $request  = $client->getRequest();
        $mediaType = $response->getHeaders()->get('Content-Type')->getMediaType();
        $body = $response->getBody();
        switch ($mediaType) {
            case 'application/json':
                // turn JSON into array
                $body = Json::decode($body, Json::TYPE_ARRAY);
                break;
            // @TODO: Adjust the XML media type 
            case 'text/xml':
            case 'application/atom+xml':
                // turn XML into an array
                $body = \LSS\XML2Array::createArray($body);
                break;
                
            default:
                break;
        }
        
        $trace=debug_backtrace();
        $caller=$trace[2];
        $callerName = $caller['function'];
        if (isset($caller['class'])) {
            $callerName .= " @ ({$caller['class']})";
        }
        // collect the data here
        $this->requests[$callerName][] =
        array(  
                'uri' => $context['functionArgs'][0],
                'request' => array(
                    'url' => $request->getUri()->toString(),
                    'method' => $request->getMethod(),
                    'headers' => $request->getHeaders()->toArray(),
                    'content' => substr($request->getContent(),0,50).'...',
                ),
                'response' => array (
                    'code' => $response->getStatusCode(),
                    'media-type'=> $mediaType,
                    'body' => $body
                ),
                'time (ms)' => $this->formatTime($context['durationInclusive']),
        );
        
        $this->requestCount++;
        
        /*
        $storage['RequestsAndTime'][] = array (
            'caller' => $callerName,
            'uri' => $context['functionArgs'][0],
            'method' => $request->getMethod(),
            'responseCode' => $response->getStatusCode(),
            'ResponseMedia' => $mediaType,
            'time (ms)' => $this->formatTime($context['durationInclusive']),
        );
        */
        
    }
    
    /**
     * This method summarizes all requests
     * @param unknown $context
     * @param unknown $storage
     */
    public function onLeaveShutdown($context, &$storage)
    {
        $storage['webrequests'][] = array(
           'Count' => $this->requestCount,
           'Requests' => $this->requests
        );
    }
 
    /**
     * Empty shutdown handler
     */
    public function shutdown() 
    {
        
    }
    
    private function formatTime($ms) {
        //$uSec = $input % 1000;
        $input = floor($ms / 1000);
        return $input;
    }
    
}