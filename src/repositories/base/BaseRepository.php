<?php

namespace common\repositories\base;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class BaseRepository
 * @package common\repositories\base
 */
abstract class BaseRepository implements RepositoryInterface
{
    protected $modelClass;

    /**
     * @param $modelClass
     */
    public function _construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @param $id
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function findOneById($id)
    {
        /** @var ActiveRecord $model */
        $model = $this->modelClass;
        if (!$model = $model::findOne($id)) {
            throw new \InvalidArgumentException('Model not found');
        }
        return $model;
    }

    /**
     * @param $model
     * @return mixed|void
     * @throws InvalidConfigException|\InvalidArgumentException
     */
    public function add($model)
    {
        if (!$model instanceof $this->modelClass) {
            throw new InvalidConfigException('Property "$model" must be implemented ' . $this->modelClass);
        }
        if (!$model->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $model->insert(false);
    }

    /**
     * @param $model
     * @return mixed|void
     * @throws InvalidConfigException|\InvalidArgumentException
     */
    public function insert($model)
    {
        if (!$model instanceof $this->modelClass) {
            throw new InvalidConfigException('Property "formClass" must be implemented ' . $this->modelClass);
        }
        if (!$model->getIsNewRecord()) {
            throw new \InvalidArgumentException('Model not exists');
        }
        $model->update(false);
    }
}
