<?php

namespace App\Http\Controllers\Project;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use EasyWeChat;

class WechatController extends Controller{
    
    public function server(){
    	$server = EasyWeChat::server();

    	$server->setMessageHandler(function($message){

    		switch ($message->MsgType) {
	            case 'subscribe':
	                return '欢迎订阅';
	                break;
	            case 'text':
	            	$content = $message->Content;
	            	$arr = explode(' ', $content);
	            	$controllerName = '\\App\\Http\\Controllers\\Project\\' . ucfirst(array_shift($arr)) . 'Controller';
	            	$controller = app()->make($controllerName);
	            	$result = app()->call([$controller, 'index'], [$arr]);
	            	return $result;
	            	break;
	            default:
	                # code...
	                break;
	        }
    	});

    	return $server->serve();
    }
}
