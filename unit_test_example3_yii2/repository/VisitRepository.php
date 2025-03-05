<?php


namespace common\repositories\educational;

use common\repositories\providers\visit\VisitProvider;
use common\repositories\providers\visit\VisitProviderInterface;
use DomainException;
use frontend\models\work\educational\journal\VisitLesson;
use frontend\models\work\educational\journal\VisitWork;
use Yii;
use yii\helpers\ArrayHelper;

class VisitRepository
{
    public $provider;

    public function __construct(
        VisitProviderInterface $provider = null
    )
    {
        if (!$provider) {
            $provider = Yii::createObject(VisitProvider::class);
        }

        $this->provider = $provider;
    }

    public function get(int $id)
    {
        return $this->provider->get($id);
    }

    public function getByTrainingGroup(int $groupId)
    {
        if (get_class($this->provider) == VisitProvider::class) {
            return $this->provider->getByTrainingGroup($groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getByTrainingGroup');
        }
    }

    public function getByTrainingGroupParticipant(int $trainingGroupParticipantId)
    {
        return $this->provider->getByTrainingGroupParticipant($trainingGroupParticipantId);
    }

    public function getByGroupAndParticipant(int $groupId, int $participantId)
    {
        return VisitWork::find()
            ->joinWith(['trainingGroupParticipantWork trainingGroupParticipantWork'])
            ->where(['trainingGroupParticipantWork.training_group_id' => $groupId])
            ->andWhere(['trainingGroupParticipantWork.participant_id' => $participantId])
            ->one();
    }

    public function delete(VisitWork $visit)
    {
        return $this->provider->delete($visit);
    }

    public function save(VisitWork $visit)
    {
        return $this->provider->save($visit);
    }

    public function getParticipantsFromGroup(int $groupId)
    {
        if (get_class($this->provider) == VisitProvider::class) {
            return $this->provider->getParticipantsFromGroup($groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getParticipantsFromGroup');
        }
    }

    public function getLessonsFromGroup(int $groupId)
    {
        if (get_class($this->provider) == VisitProvider::class) {
            return $this->provider->getLessonsFromGroup($groupId);
        } else {
            throw new DomainException('Mock-провайдер не имеет реализации метода getLessonsFromGroup');
        }
    }

    public function prepareUpdateLessons(int $visitIds, string $lessons)
    {
        $command = Yii::$app->db->createCommand();
        $command->update(
            'visit',
            ['lessons' => $lessons],
            ['IN', 'id', $visitIds],
        );
        return $command->getRawSql();
    }
}
