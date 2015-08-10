<?php

class Bewotec_Cms {
    
    public function onLeaveIndexAction($context, &$storage) {
        $storage['CMS']['vars'] = array (
            $context['locals']['sitecode'],
            $context['locals']['settings'],
            $context['locals']['globalOffers']
        );
    }
}