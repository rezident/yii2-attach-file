<?php


namespace rezident\attachfile\controllers;


use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\views\RawView;
use rezident\attachfile\views\AbstractView;
use yii\helpers\Json;
use yii\helpers\StringHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

class AttachFileController extends Controller
{
    public function actionGet($modelKey, $path, $viewId, $fileName)
    {
        $attachedFileView = AttachedFileView::find()->byId($viewId)->joinWith('attachedFile')->one();
        if ($attachedFileView) {
            throw new NotFoundHttpException();
        }

        if ($attachedFileView->attachedFile->model_key != $modelKey) {
            throw new NotFoundHttpException();
        }

        if (StringHelper::startsWith($attachedFileView->attachedFile->md5_hash, $path) == false) {
            throw new NotFoundHttpException();
        }

        if (pathinfo($fileName, PATHINFO_FILENAME) != pathinfo($attachedFileView->attachedFile->name, PATHINFO_FILENAME)) {
            throw new NotFoundHttpException();
        }

        if(pathinfo($fileName, PATHINFO_EXTENSION) != $attachedFileView->extension) {
            throw new NotFoundHttpException();
        }

        $config = Json::decode($attachedFileView->view_config);
        $config['attachedFile'] = $attachedFileView->attachedFile;
        /** @var AbstractView $view */
        $view = \Yii::createObject($config);
        if (is_a($view, AbstractView::class) == false) {
            throw new ServerErrorHttpException();
        }

        \Yii::$app->getResponse()->getHeaders()->add('Content-Type', $view->getContentType());
        \Yii::$app->getResponse()->format = Response::FORMAT_RAW;

        return $view->getContent($attachedFileView);
    }

}