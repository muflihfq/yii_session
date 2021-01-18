<?php

namespace app\controllers;

use Yii;
use app\models\Session;
use yii\rest\Controller;
use yii\filters\AccessControl;



class SessionController extends Controller 
{
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['create','update','delete'],
                'denyCallback' => function($rule,$action){
                    return $this->response('You need to Login first',401);
                },
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create','update','delete'],
                        'roles' => ['@']
                    ]
                ]
            ]
        ];
    }
    
    public function actionList(){

        $request = Yii::$app->request;
        
        if(!$request->isGet){
            return $this->response('Method not allowed',405);
        }

        // $sessions = Session::find()->all();

        $sessions =  (new \yii\db\Query())
                ->select(['id', 'name'])
                ->from('session')
                ->all();
                
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $sessions;

        return $response;
    }

    public function actionDetail($id){
        
        $request = Yii::$app->request;

        if(!$request->isGet){
            return $this->response('Method not allowed',405);
        }

        $session = Session::findOne($id);

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $session;

        return $response;
    }

    public function actionCreate(){
        $request = Yii::$app->request;

        if(!$request->isPost){
            return $this->response('Method not allowed',405);
        }

        $start = $request->getBodyParam('start');

        if(!isset($start)){
            return $this->response('Bad request',400);
        }

        $session = new Session();
        $session->scenario = Session::CREATE_SESSION;
        $session->userID = Yii::$app->user->identity->ID;
        
        $session->attributes = $request->bodyParams;
        $session->start = date('Y-m-d H:i:s',strtotime($request->getBodyParam('start')));
        $session->created = date('Y-m-d H:i:s');
        $session->updated = date('Y-m-d H:i:s');

      
        if(!$session->validate()){
            return $this->response('Bad request',400);
        }
        
        $session->save();
        return $this->returnSession($session);
    }

    public function actionUpdate($id){
        $request = Yii::$app->request;

        if(!$request->isPut){ 
            return $this->response('Method not allowed',405);
        }

        
        $session = Session::findOne($id);
        
        if(!$session){
            return $this->response('Not found',404);
        }
        
        if(Yii::$app->user->identity->ID != $session->userID){
            return $this->response('Unauthorized',401);
        }

        $start = $request->getBodyParam('start');
        $session->attributes = $request->bodyParams;
        if (isset($start)) {
            $session->start = date('Y-m-d H:i:s',strtotime($start));
        }
        $session->updated = date('Y-m-d H:i:s');
    
        $session->save();

        return $this->returnSession($session);

    }

    public function actionDelete($id){
        $request = Yii::$app->request;

        if(!$request->isPut){
            return $this->response('Method not allowed',405);
        }

        $session = Session::findOne($id);

        if(!$session){
            return $this->response('Not found',404);
        }
        
        if(Yii::$app->user->identity->ID != $session->userID){
            return $this->response('Unauthorized',401);
        }

        $session->delete();

        return $this->response('Session deleted',200);

    }

    private function response($message,$code){
        $response = Yii::$app->response;
        $response->statusCode = $code;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['message' => $message];

        return $response;
    }

    private function returnSession(Session $session)
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $session;
        return $response;
    }

    
}