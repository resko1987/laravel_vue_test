<?php

namespace App\Http\Controllers\Employes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Schema;
use DB;

class EmployesController extends Controller {

    public function __construct() {
        //ФИО, Отдел, Должность, Руководитель, Телефон
        if (Schema::hasTable('employes')) {
            $departments_count = DB::table('departments')->count();
            if ($departments_count == 0) {
                DB::insert('insert into departments (title) values (?)', ['Первый']);
                DB::insert('insert into departments (title) values (?)', ['Второй']);
                DB::insert('insert into departments (title) values (?)', ['Третий']);

                DB::insert('insert into jobs (title) values (?)', ['Директор']);
                DB::insert('insert into jobs (title) values (?)', ['Руководитель']);
                DB::insert('insert into jobs (title) values (?)', ['Программист']);
                DB::insert('insert into jobs (title) values (?)', ['Дизайнер']);

                DB::insert("INSERT into employes (fio, department_id, job_id, parent_id) "
                        . "VALUES (?,?,?,?)", ['Лебедев А.В.', 1, 1, 0]);
                DB::insert("INSERT into employes (fio, department_id, job_id, parent_id) "
                        . "VALUES (?,?,?,?)", ['Иванов С.А.', 1, 2, 1]);
                DB::insert("INSERT into employes (fio, department_id, job_id, parent_id) "
                        . "VALUES (?,?,?,?)", ['Петров Г.Д.', 1, 3, 2]);
                DB::insert("INSERT into employes (fio, department_id, job_id, parent_id) "
                        . "VALUES (?,?,?,?)", ['Сидоров М.Д.', 1, 4, 2]);
                DB::insert("INSERT into employes (fio, department_id, job_id, parent_id) "
                        . "VALUES (?,?,?,?)", ['Куликов А.Н.', 1, 3, 2]);
            }
        }
    }

    /**
     * Колличество сотрудников
     * @return type
     */
    public function getCount($find = null) {
        $where = '';
        $w = [];
        $params = [];
        $findParams = $this->findParseString($find);

        foreach ($findParams['wheres'] as $v) {
            $w[] = $v;
        }
        foreach ($findParams['params'] as $v) {
            $params[] = $v;
        }
        if (count($w) > 0) {
            $where = "WHERE " . implode(' and ', $w);
        }

        $employes = DB::select("SELECT * FROM employes e {$where}
                                ", $params);
        return count($employes);
    }

    /**
     * Список всех сотрудников
     * @return type
     */
    public function getEmployes($limit = 2, $page = NULL, $find = null) {
        $where = '';
        $w = [];
        $params = [];
        $findParams = $this->findParseString($find);

        // finds
        foreach ($findParams['wheres'] as $v) {
            $w[] = $v;
        }
        foreach ($findParams['params'] as $v) {
            $params[] = $v;
        }
        if (count($w) > 0) {
            $where = "WHERE " . implode(' and ', $w);
        }

        $limitStr = '';
        if ($page != NULL) {
            $limitStr = 'LIMIT ? OFFSET ?';
            // limit
            $params[] = $limit;
            $params[] = ($limit * $page);
        }

        $employes = DB::select("SELECT e.*, d.title department_title, j.title job_title,
                                (select e2.fio from employes e2 where e2.id=e.parent_id) as director
                                FROM employes e
                                left join departments d on e.department_id=d.id
                                left join jobs j on e.job_id=j.id {$where} {$limitStr} 
                                ", $params);
        return $employes;
    }

    /**
     * Обновление или добавление id, fio, department_id, job_id, parent_id
     * @param type $data
     * @return boolean
     */
    public function update($data) {
        if ($data['id'] == 0) {
            if (DB::table('employes')->insert([[
                    'fio' => $data['fio'],
                    'department_id' => $data['department_id'],
                    'job_id' => $data['job_id'],
                    'parent_id' => $data['parent_id']
                ]])) {
                return true;
            }
        } else {
            if (DB::table('employes')
                            ->where('id', $data['id'])
                            ->update([
                                'fio' => $data['fio'],
                                'department_id' => $data['department_id'],
                                'job_id' => $data['job_id'],
                                'parent_id' => $data['parent_id']
                            ])) {
                return true;
            }
        }

        return false;
    }

    /**
     * Удаление сотрудника
     * @param type $id
     * @return boolean
     */
    public function dellEmploye($id) {
        if (DB::table('employes')->delete($id)) {
            return true;
        }
        return false;
    }

    /**
     * Получаем параметры из find строки
     * @param string $findStr
     * @return string
     */
    private function findParseString($findStr) {
        $r['wheres'] = [];
        $r['params'] = [];
        if ($findStr) {
            mb_parse_str($findStr, $gets);
//            print_r($gets);
//            echo PHP_EOL;
            if (isset($gets['department']) && is_numeric($gets['department']) && $gets['department'] > 0) {
                $r['wheres'][] = " e.department_id=? ";
                $r['params'][] = $gets['department'];
            }
            if (isset($gets['find_director_name']) && is_string($gets['find_director_name']) && $gets['find_director_name'] > 0) {
                $r['wheres'][] = " e.fio ILIKE ? ";
                $r['params'][] = '%' . trim($gets['find_director_name']) . '%';
            }
            if (isset($gets['id']) && is_numeric($gets['id']) && $gets['id'] > 0) {
                $r['wheres'][] = " e.id=? ";
                $r['params'][] = $gets['id'];
            }
        }
        return $r;
    }

}
