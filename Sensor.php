<?php
namespace App\Controllers;

use App\Models\SensorModel;
use CodeIgniter\RESTful\ResourceController;

class Sensor extends ResourceController
{
    protected $modelName = 'App\Models\SensorModel';
    protected $format    = 'json';

    public function create()
    {
        $data = $this->request->getJSON(true);
        if ($this->model->insert($data)) {
            return $this->respondCreated(['status' => 'Data inserted successfully']);
        }
        return $this->failServerError('Failed to insert data');
    }

    public function getData()
    {
        $data = $this->model->orderBy('timestamp', 'ASC')->findAll();
        return $this->respond($data);
    }
}