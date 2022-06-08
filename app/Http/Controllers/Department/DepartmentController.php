<?php

namespace App\Http\Controllers\Department;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Schema;
use DB;

class DepartmentController extends Controller
{
    public function __construct() {
        return true;
    }
    

    /**
     * Список всех отделов
     * @return type
     */
    public function getDepartments() {
        return DB::table('departments')->get();
    }

}
