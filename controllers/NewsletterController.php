<?php

namespace app\controllers;

use app\models\Newsletter;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\Http;

class NewsletterController extends Controller
{
    public function actionIndex()
    {
        $title = 'Subscribe to our newsletter';
        $model = new Newsletter();

        $model->setPageSize();

        return $this->render('index', [
            'model' => $model,
            'title' => $title
        ]);
    }

    public function actionSubscribe()
    {
        $model = new Newsletter();

        $model->setPageSize();

        return $this->render('subscribe', [
            'model' => $model
        ]);
    }

    public function actionSaveNewsletter()
    {
        $json = array(
            'message' => ''
        );

        if (empty($_POST['email'])) {
            $json['message'] = 'E-mail cannot be blank';
            Http::setHttpHeader(400);
            echo json_encode($json);
            Yii::$app->end(1);
        }

        if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
            $json['message'] = 'Not valid e-mail address. ';
            Http::setHttpHeader(400);
            echo json_encode($json);
            Yii::$app->end(1);
        }

        $emailExist = Newsletter::find()->where("email = '{$_POST['email']}'")->one();
        if ($emailExist !== null) {
            $json['message'] = "E-mail {$_POST['email']} has already been taken";
            Http::setHttpHeader(400);
            echo json_encode($json);
            Yii::$app->end(1);
        }

        $model = new Newsletter();
        $model->email = $_POST['email'];
        $model->name = explode('@', $_POST['email'])[0];
        $model->added = date('Y-m-d H:i:s');

        if (!$model->validate()) {
            $json['message'] = 'An error occurred during validation the e-mail';
            Http::setHttpHeader(400);
            echo json_encode($json);
            Yii::$app->end(1);
        };

        if (!$model->insert()) {
            $json['message'] = 'The registration could not be saved';
            Http::setHttpHeader(400);
            echo json_encode($json);
            Yii::$app->end(1);
        };

        $model->sendNewsletterEmail();

        $json['message'] = 'You subscribed to the newsletter successfully';
        Http::setHttpHeader(200);
        echo json_encode($json);
        Yii::$app->end(1);

    }

    /**
     * Validate newsletter e-mail
     */
    public function actionValidate()
    {
        $model = new Newsletter();

        if (Yii::$app->request->isAjax && $model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }

    /**
     * Save new record in DB and send confirmation e-mail
     */
    public function actionSave()
    {
        $model = new Newsletter();
        $request = \Yii::$app->getRequest();
        if ($request->isPost && $model->load($request->post())) {
            \Yii::$app->response->format = Response::FORMAT_JSON;
            $model->added = date('Y-m-d H:i:s');
            $name = explode('@', $_POST['Newsletter']['email']);
            $model->name = $name[0];

            $emailSaved = false;

            if ($model->save()) {
                $emailSaved = true;
                $model->sendNewsletterEmail();
            }

            return ['success' => $emailSaved];
        }

        return $this->renderAjax('index', [
            'model' => $model,
        ]);
    }

}