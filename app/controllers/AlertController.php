<?php

namespace NodacWeb\Controllers;

use NodacWeb\Core\Controller;
use NodacWeb\Core\View; 

class AlertController extends Controller{
    
    public static function getSuccess($message){
        return View::render('components/alerts/success', [
            'message' => $message,
        ]);
    }

    public static function getError($message){
        return View::render('components/alerts/error', [
            'message' => $message,
        ]);
    }
    
}