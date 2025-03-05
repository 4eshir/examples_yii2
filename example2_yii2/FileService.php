<?php

namespace common\services\general\files;

use common\helpers\files\FilesHelper;
use common\services\general\files\download\FileDownloadServer;
use common\services\general\files\download\FileDownloadYandexDisk;
use DomainException;
use Yii;

class FileService
{
    public function downloadFile($filepath)
    {
        $downloadServ = new FileDownloadServer($filepath);
        $downloadYadi = new FileDownloadYandexDisk($filepath);

        $type = FilesHelper::FILE_SERVER;
        $downloadServ->LoadFile();

        if (!$downloadServ->success) {
            $downloadYadi->LoadFile();
            $type = FilesHelper::FILE_YADI;

            if (!$downloadYadi->success) {
                throw new \Exception('File not found');
            }
        }

        return [
            'type' => $type,
            'obj' => $type == FilesHelper::FILE_SERVER ?
                $downloadServ :
                $downloadYadi
        ];
    }

    /**
     * Функция загрузки файла на сервер или ЯД
     * в $params необходимо передать либо filepath, либо пару tableName + fileType
     * @param $file
     * @param $filename
     * @param $params ['filepath' => %относительный_путь_к_файлу%, 'tableName' => %имя_таблицы%, 'fileType' => %тип_файла%]
     * @return void
     */
    public function uploadFile($file, $filename, $params = '')
    {
        if (array_key_exists('filepath', $params)) {
            $finalPath = $params['filepath'];
        }
        else if (array_key_exists('tableName', $params) && array_key_exists('fileType', $params)) {
            $finalPath = FilesHelper::createAdditionalPath($params['tableName'], $params['fileType']);
        }
        else {
            throw new DomainException('Не были переданы обязательные параметры: filepath или tableName + fileType');
        }

        // тут будет стратегия для загрузки на яндекс диск... потом

        if ($file) {
            $file->saveAs(Yii::$app->basePath . $finalPath . $filename);
        }
    }

    public function deleteFile($filepath)
    {
        // тут будет стратегия для загрузки на яндекс диск... потом

        if (file_exists(Yii::$app->basePath . $filepath)) {
            unlink(Yii::$app->basePath . $filepath);
        }
        else {
            throw new DomainException("Файл по пути $filepath не найден");
        }
    }
}