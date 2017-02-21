<?php


namespace rezident\attachfile\controllers;


use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\views\RawView;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class AttachFileController extends Controller
{
    public function actionGet($md5Hash, $settings)
    {
        $attachedFileView = AttachedFileView::find()->byAttachedFileMd5Hash($md5Hash)->bySettings($settings)->one();
        if($attachedFileView == null) {
            throw new NotFoundHttpException();
        }

        /** @var RawView $view */
        $view = $attachedFileView->attachedFile->getView()->bySettings($settings);
        return $view->getContent($attachedFileView->attachedFile->getOriginalFilePath());
    }

}