<?php
/**
 * Created by PhpStorm.
 * User: BaoHoang
 * Date: 12/8/2017
 * Time: 3:56 PM
 */

namespace App\Utils;


use Illuminate\Database\Eloquent\Model;

class BasicAuth
{
    protected $model;

    public function __construct()
    {
    }

    public static function getInstance()
    {
        static $instance = null;
        if (!$instance) {
            $instance = new BasicAuth();
        }
        return $instance;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }
}