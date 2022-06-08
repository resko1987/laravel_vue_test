<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Employes\EmployesController;
use App\Http\Controllers\Department\DepartmentController;
use \App\Http\Controllers\Job\JobController;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

/*
 * Pages routes
 */
Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/', function () {
    return view('index');
})->name('index');

Route::get('/employes', function () {
    $employesController = new EmployesController();
    return view('employes');
})->name('employes');

/*
 * Api routes
 */
// Сотрудники 
Route::get('/employes/all/', function (Request $request) {
    $employesController = new EmployesController();
    $find = $request->input('find');
    $page = NULL;
    if ($request->input('page') >= 0) {
        $page = $request->input('page');
    }

    $employes_count = $employesController->getCount($find);
    $employes = $employesController->getEmployes(5, $page, $find);
    return response()->json(['employes_count' => $employes_count, 'employes' => $employes]);
});

// Данные по сотруднику
Route::get('/employes/{id}/', function (Request $request, $id) {
    $employesController = new EmployesController();
    $employe = $employesController->getEmployes(1, null, 'id=' . $id);
    return response()->json(['employe' => $employe]);
});

// Данные по сотруднику
Route::post('/employes/update/', function (Request $request) {
    $employesController = new EmployesController();
    $data['id'] = $request->input('id');
    $data['fio'] = $request->input('fio');
    $data['department_id'] = $request->input('department_id');
    $data['job_id'] = $request->input('job_id');
    $data['parent_id'] = $request->input('parent_id');
    $return = 1;
    if($employesController->update($data)){
        $return = 0;
    }

    return response()->json(['status' => $return]);
});

// Удаление сотрудника
Route::get('/employes/del/{id}/', function (Request $request, $id) {
    $employesController = new EmployesController();
    $return = 1;
    if ($employesController->dellEmploye($id)) {
        $return = 0;
    }
    return response()->json(['status' => $return]);
});

// Отделы
Route::get('/departments/all/', function () {
    $departmentController = new DepartmentController();
    $departments = $departmentController->getDepartments();
    return response()->json(['departments' => $departments]);
});

// Должности
Route::get('/jobs/all/', function () {
    $jobController = new JobController();
    $jobs = $jobController->getJobs();
    return response()->json(['jobs' => $jobs]);
});
