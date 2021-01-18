<?php

namespace app\controllers;

use Yii;
use app\models\User;
use yii\rest\Controller;


class UserController extends Controller 
{

    public function actionLogin(){
        $request = Yii::$app->request;

        if(!$request->isPost){
            return $this->response('Method not allowed',405); 
        }

        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password');
        $user = User::loginUser($email,$password);

        if(!$user){
            return $this->response('Email or password wrong',404); 
        } else {
            return $this->returnUser($user);
        }
    }

    public function actionRegister(){
        $request = Yii::$app->request;

        if(!$request->isPost){
            return $this->response('Method not allowed',405);
        }

        $name = $request->getBodyParam('name');
        $email = $request->getBodyParam('email');
        $password = $request->getBodyParam('password');

        if(!isset($name) || !isset($email) || !isset($password)){
            return $this->response('Bad request',400);
        }
        

        $user = new User();
        
        if($user->findUserByEmail($email)){
            return $this->response('Email already exists',404); 
        }

        $user->name = $name;
        $user->email = strtolower($email);
        $user->password = $user->hashPassword($password);
        $user->created = date('Y-m-d H:i:s');
        $user->updated = date('Y-m-d H:i:s');
        $user->save();

        return $this->returnUser($user);
    }

    public function actionLogout(){
        Yii::$app->user->logout();

        return $this->response('Logout succesfully',200); 

    }

    private function returnUser(User $user)
    {
        return array(
            'user' => array(
                'id' => $user->ID,
                'name' => $user->name,
                'email' => $user->email
            ),
            'code' => 200
        );
    }

    private function response($message,$code){
        $response = Yii::$app->response;
        $response->statusCode = $code;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['message' => $message];

        return $response;
    }

}