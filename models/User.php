<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;


class User extends ActiveRecord implements IdentityInterface
{
    const LOGIN_USER = 'LOGIN_USER';
    const REGISTER_USER = 'REGISTER_USER';

    public static function tableName(){
        return 'user';
    }

    public function rules(){
        return [            
            ['email','email']
        ];
    }

    public function hashPassword($password)
    {
        return Yii::$app->getSecurity()->generatePasswordHash($password);
    }

    public function findUserByEmail($email)
    {
        return User::find()->where(['email' => $email])->one();
    }

    public static function loginUser($email,$password)
    {
        $user = User::find()->where(['email' => $email])->one();

        if($user && Yii::$app->getSecurity()->validatePassword($password, $user->password)){
            Yii::$app->user->login($user);
            return $user;
        }

        return null;

    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->ID;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return bool if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}
