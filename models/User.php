<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $name
 * @property string $mobile
 * @property string $password
 * @property string $firsttime
 * @property string $updatetime
 * @property string $ip
 * @property string $identification
 * @property string $address
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['mobile', 'password', 'firsttime', 'updatetime'], 'required'],
            [['firsttime', 'updatetime'], 'safe'],
            [['name', 'mobile', 'address'], 'string', 'max' => 16],
            [['password'], 'string', 'max' => 64],
            [['ip', 'identification'], 'string', 'max' => 32],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'password' => 'Password',
            'firsttime' => 'Firsttime',
            'updatetime' => 'Updatetime',
            'ip' => 'Ip',
            'identification' => 'Identification',
            'address' => 'Address',
        ];
    }


	public function register(){
		$this->firsttime= date("Y-m-d H:i:s");
		$this->updatetime= date("Y-m-d H:i:s");
		return $this->save();
	}

	public static function login($loginForm){
		$user = self::find()->where([
			'mobile'	=> $loginForm->mobile,
			'password'	=> md5($loginForm->password),
		])->one();
		if($user){
			Yii::$app->session->set('userid', $user->id);
			return $user->id;
		}
		else{
			return false;
		}
	}


	// 验证给定的手机号是否可用
	// 可用返回 true
	// 不可用（已存在）返回 false
	public static function validateMobile($mobile){
		return !self::find()->where(['mobile' => $mobile])->one();
	}

    public static function validatePassword($mobile, $password){
    	return self::find()->where([
    		'mobile'	=> $mobile,
    		'password'	=> md5($password),
    	])->one();
    }
}
