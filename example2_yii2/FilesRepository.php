<?php

namespace common\repositories\general;

use DomainException;
use frontend\models\work\general\FilesWork;
use Yii;
use yii\db\Exception;

class FilesRepository
{
    public function getById(int $id)
    {
        return FilesWork::find()->where(['id' => $id])->one();
    }

    public function get(string $tableName, int $tableRowId, int $fileType)
    {
        return FilesWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $tableRowId])
            ->andWhere(['file_type' => $fileType])
            ->all();
    }

    public function getLastFile(string $tableName, int $tableRowId, int $fileType)
    {
        return FilesWork::find()
            ->where(['table_name' => $tableName])
            ->andWhere(['table_row_id' => $tableRowId])
            ->andWhere(['file_type' => $fileType])
            ->orderBy(['id' => SORT_DESC])
            ->one();
    }

    /**
     * Подготавливает запрос для создания новой записи в таблице
     * @param string $tableName
     * @param int $tableRowId
     * @param int $filetype
     * @param string $filepath
     * @return string
     */
    public function prepareCreate(string $tableName, int $tableRowId, int $filetype, string $filepath) : string
    {
        $model = FilesWork::fill($tableName, $tableRowId, $filetype, $filepath);
        $command = Yii::$app->db->createCommand();
        $command->insert($model::tableName(), $model->getAttributes());

        return $command->getRawSql();
    }

    /**
     * Подготавливает запрос для изменения существующей записи в таблице
     * @param string $tableName
     * @param int $tableRowId
     * @param int $filetype
     * @param string $filepath
     * @return string
     */
    public function prepareUpdate(string $tableName, int $tableRowId, int $filetype, string $filepath) : string
    {
        $model = $this->get($tableName, $tableRowId, $filetype);
        if (count($model) == 0) {
            throw new Exception('Запись не найдена');
        }

        $command = Yii::$app->db->createCommand();
        $command->update($model[0]::tableName(), ['filepath' => $filepath], ['id' => $model[0]->id]);

        return $command->getRawSql();
    }

    /**
     * Подготавливает запрос для удаления существующей записи в таблице
     * @param int $id
     * @return string
     */
    public function prepareDelete(int $id)
    {
        $model = $this->getById($id);
        if (!$model) {
            throw new Exception('Запись не найдена');
        }

        $command = Yii::$app->db->createCommand();
        $command->delete($model::tableName(), ['id' => $id]);

        return $command->getRawSql();
    }

    public function save(FilesWork $file)
    {
        if (!$file->save()) {
            throw new DomainException('Ошибка сохранения связки входящий/исходящий документы. Проблемы: '.json_encode($file->getErrors()));
        }

        return $file->id;
    }
}
