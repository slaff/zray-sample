<?php

class Bewotec_Cms {
    
    public function onLeaveIndexAction($context, &$storage) {
        $storage['CMS']['vars'] = $context['locals'];
    }
}