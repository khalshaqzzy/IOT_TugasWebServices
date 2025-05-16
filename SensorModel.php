<?php
namespace App\Models;

use CodeIgniter\Model;

class SensorModel extends Model
{
    protected $table = 'sensor';
    protected $allowedFields = ['motion_detected'];
}