<?php namespace App\Widget\CSS;

trait StyleSheet {
    
    protected function initCSS()
    {
        $this->css->messageColor = [
            'color' => '#1e88e5'
        ];
    }
}