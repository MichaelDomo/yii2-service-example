<?php

namespace common\repositories\base;

/**
 * Interface RepositoryInterface
 * @package common\repositories
 */
interface RepositoryInterface
{
    /**
     * @param $id
     * @return mixed
     */
    public function findOneById($id);
    /**
     * Method that save (insert or update) in a specific repository
     *
     * @param RepositoryModel $model
     * @return mixed
     */
    public function save($model);
    /**
     * Method that insert a record in a specific repository
     *
     * @param RepositoryModel $model
     * @return mixed
     */
    public function insert($model);


    /**
     * @param $modelClass
     * @param $condition
     * @return mixed
     */
    //public function find($modelClass, $condition);
    /**
     * Method to fetch one record
     *
     * @param $modelClass
     * @param $condition
     * @return mixed
     */
    //public function findOne($modelClass, $condition);
    /**
     * Method to fetch all records
     *
     * @param $modelClass
     * @param $condition
     * @return mixed
     */
    //public function findAll($modelClass, $condition);
    /**
     * Method that update a record in a specific repository
     *
     * @param RepositoryModel $model
     * @return mixed
     */
    //public function update($model);
    /**
     * Method that delete a record in a specific repository
     *
     * @param RepositoryModel $model
     * @return mixed
     */
    //public function delete($model);
}
