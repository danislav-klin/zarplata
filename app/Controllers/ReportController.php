<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\WorkEntryModel;
use CodeIgniter\HTTP\ResponseInterface;

class ReportController extends BaseController
{
    public function index()
    {
        $employeeModel = new EmployeeModel();
        $departmentModel = new DepartmentModel();

        // Получаем списки для фильтров
        $employees = $employeeModel
            ->select('employees.id, employees.last_name, employees.first_name, employees.surname')
            ->orderBy('employees.last_name', 'ASC')
            ->findAll();

        $departments = $departmentModel
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = [
            'crumbs'      => 'Отчеты и ведомости',
            'employees'   => $employees,
            'departments' => $departments,
        ];

        return view('templates/header', $data)
            . view('report/index', $data)
            . view('templates/footer');
    }

    public function generate()
    {
        $workEntryModel = new WorkEntryModel();

        // Получаем параметры фильтров
        $dateFrom     = $this->request->getGet('date_from') ?? '';
        $dateTo       = $this->request->getGet('date_to') ?? '';
        $employeeId   = $this->request->getGet('employee_id') ?? '';
        $departmentId = $this->request->getGet('department_id') ?? '';
        $sortBy       = $this->request->getGet('sort_by') ?? 'date';

        // Строим запрос
        $builder = $workEntryModel
            ->select('
                work_entries.*,
                CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
                emp.id as employee_id
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left');

        // Применяем фильтры
        if ($dateFrom !== '') {
            $builder->where('work_entries.work_date >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('work_entries.work_date <=', $dateTo);
        }
        if ($employeeId !== '') {
            $builder->where('work_entries.employee_id', $employeeId);
        }
        if ($departmentId !== '') {
            $builder->where('emp.department_id', $departmentId);
        }

        // Применяем сортировку
        switch ($sortBy) {
            case 'employee':
                $builder->orderBy('employee_name', 'ASC');
                break;
            case 'amount_desc':
                $builder->orderBy('work_entries.amount', 'DESC');
                break;
            default:
                $builder->orderBy('work_entries.work_date', 'ASC');
                break;
        }

        // Получаем данные
        $entries = $builder->findAll();

        // Подсчитываем итоговую сумму
        $total = array_sum(array_column($entries, 'amount'));

        // Сводка по сотрудникам
        $summary = [];
        foreach ($entries as $entry) {
            $empId = $entry['employee_id'];
            if (!isset($summary[$empId])) {
                $summary[$empId] = [
                    'employee_id'   => $empId,
                    'employee_name' => $entry['employee_name'],
                    'total_amount'  => 0,
                    'work_count'    => 0,
                ];
            }
            $summary[$empId]['total_amount'] += $entry['amount'];
            $summary[$empId]['work_count']++;
        }

        // Сортируем сводку по убыванию суммы
        usort($summary, function($a, $b) {
            return $b['total_amount'] <=> $a['total_amount'];
        });

        // Возвращаем JSON
        return $this->response->setJSON([
            'success'   => true,
            'entries'   => $entries,
            'total'     => $total,
            'summary'   => array_values($summary),
            'count'     => count($entries),
        ]);
    }

    public function exportCsv()
    {
        // Получаем те же параметры что и для generate
        $dateFrom     = $this->request->getGet('date_from') ?? '';
        $dateTo       = $this->request->getGet('date_to') ?? '';
        $employeeId   = $this->request->getGet('employee_id') ?? '';
        $departmentId = $this->request->getGet('department_id') ?? '';

        $workEntryModel = new WorkEntryModel();

        $builder = $workEntryModel
            ->select('
                work_entries.work_date,
                CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
                work_entries.rate_name,
                work_entries.qty,
                work_entries.rate_snapshot,
                work_entries.amount,
                work_entries.note
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left');

        if ($dateFrom !== '') {
            $builder->where('work_entries.work_date >=', $dateFrom);
        }
        if ($dateTo !== '') {
            $builder->where('work_entries.work_date <=', $dateTo);
        }
        if ($employeeId !== '') {
            $builder->where('work_entries.employee_id', $employeeId);
        }
        if ($departmentId !== '') {
            $builder->where('emp.department_id', $departmentId);
        }

        $entries = $builder->orderBy('work_entries.work_date', 'ASC')->findAll();

        // Генерируем CSV
        $filename = 'report_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM для корректного отображения кириллицы в Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Заголовки
        fputcsv($output, ['Дата', 'Сотрудник', 'Вид работы', 'Количество', 'Ставка', 'Сумма', 'Примечание'], ';');
        
        // Данные
        foreach ($entries as $entry) {
            fputcsv($output, [
                $entry['work_date'],
                $entry['employee_name'],
                $entry['rate_name'],
                $entry['qty'],
                $entry['rate_snapshot'],
                $entry['amount'],
                $entry['note'] ?? '',
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}
