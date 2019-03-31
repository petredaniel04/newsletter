<?php

namespace app\models;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Mail;

/**
 * This is the model class for table "newsletter".
 *
 * @property string $id
 * @property string $email
 * @property string $name
 * @property string $added
 *
 * @property Newsletter[] $newsletter
 */
class Newsletter extends \yii\db\ActiveRecord
{
    const SEND_NEWSLETTER_EMAIL = "%ORGANIZATION% - Subscribe newsletter<!--SUBJECT-->
Hello <b>%NAME%</b>,

<p style=\"font-size: 14px;\">Thank you for subscribe to our newsletter.</a>.

<b>%ORGANIZATION% Team</b>
</p>

<p style=\"font-size: 12px;\">DISCLAIMER: This electronic message, including all contents and attachments, contains information that may be privileged, confidential or otherwise protected from disclosure. The information is intended for the addressee only (<b>%EMAIL%</b>). If you are not the addressee, then any disclosure, copying, distribution or use of this message or any of its contents or attachments is prohibited. If you have received this message in error, please notify the sender immediately and destroy the original message and all copies. Neither %ORGANIZATION% nor any of its affiliates accept any liability for loss, damage or consequence, whether caused by our own negligence or resulting, directly or indirectly, from the use of any attached files.</p>
<p style=\"color: #008000; font-size: 12px;\">Please think again before printing this e-mail. Help protecting the environment!</p>
";

    public $pageSize = 10;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'newsletter';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_newsletter');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['name'], 'string', 'max' => 255],
            [['email'], 'required'],
            [['email'], 'unique'],
            [['email'], 'email'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'email' => 'E-mail',
            'name' => 'Name',
            'added' => 'Added',
        ];
    }

    public function search()
    {
        $condition = "id >= 1";
        $query = Newsletter::find()->where($condition)->orderBy([
            'added' => SORT_DESC
        ]);

        $pagination = [
            'pagesize' => $this->pageSize
        ];

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => $pagination
        ]);
    }

    public function setPageSize()
    {
        if (!empty(Yii::$app->params['pageSize'])) {
            $this->pageSize = Yii::$app->params['pageSize'];
        }
        if (!empty($_GET['per-page'])) {
            $this->pageSize = $_GET['per-page'];
        }
    }

    public function sendNewsletterEmail()
    {
        // default values
        $params = array(
            // required
            'FROM' => EMAIL_FROM,
            'CONTACT' => EMAIL_CONTACT,
            'ORGANIZATION' => COMPANY_NAME,
            'EMAIL' => $this->email,
            'NAME' => $this->name,
        );

        // send the e-mail
        $sent = Mail::sendTMail(self::SEND_NEWSLETTER_EMAIL, $params);
        return $sent;
    }
}
