<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Schema;
use DB;

class JobController extends Controller {

    public function __construct() {
        return true;
    }

    /**
     * Список всех отделов
     * @return type
     */
    public function getJobs() {
        return DB::table('jobs')->get();
    }

}
