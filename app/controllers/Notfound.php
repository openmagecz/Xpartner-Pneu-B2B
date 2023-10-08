<?php

class Notfound {
    
    public function index(){      
        $f3 = \Base::instance();
        $view = \View::instance();
        
        echo $view->render('NotfoundView.php','text/html');     
    }
    
}