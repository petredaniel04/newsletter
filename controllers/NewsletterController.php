<?php

namespace app\controllers;

use app\models\Newsletter;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;

class NewsletterController extends Controller
{
    public function actionIndex()
    {
        $title = 'Subscribe to our newsletter';
        $model = new Newsletter();
        if (!empty($_GET['Newsletter'])) {
            foreach ($_GET['Newsletter'] as $name => $value) {
                if (!$model->hasAttribute($name)) {
                    continue;
                }
                $model->$name = $value;
            }
        }

        $model->setPageSize();

        return $this->render('index', [
            'model' => $model,
            'title' => $title
        ]);
    }

    public function actionSubscribe()
    {
        return $this->render('subscribe');
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

            if($model->save()) {
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