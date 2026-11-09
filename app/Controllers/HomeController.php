<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\PositionModel;
use App\Models\RateModel;
use App\Models\WorkEntryModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UnitModel;

class HomeController extends BaseController
{
    public function index()
    {
        $employeeModel  = new EmployeeModel();
        $workEntryModel = new WorkEntryModel();
        $rateModel      = new RateModel();
        $departmentModel = new DepartmentModel();
        $positionModel   = new PositionModel();

        // Начало и конец текущего месяца
        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');

        // Кол-во сотрудников
        $employeeCount = $employeeModel->countAllResults();

        // Кол-во записей за текущий месяц
        $entriesCount = $workEntryModel
            ->where('work_date >=', $monthStart)
            ->where('work_date <=', $monthEnd)
            ->countAllResults();

        // Сумма выплат за текущий месяц
        $monthAmount = $workEntryModel
            ->selectSum('amount')
            ->where('work_date >=', $monthStart)
            ->where('work_date <=', $monthEnd)
            ->get()
            ->getRow()
            ->amount ?? 0;

        // Средняя выплата на сотрудника за месяц
        $avgAmount = $employeeCount > 0
            ? round($monthAmount / $employeeCount, 2)
            : 0;

        // Кол-во активных ставок
        $activeRates = $rateModel->where('is_active', 1)->countAllResults();

        // Последние 10 записей с именем сотрудника и именем пользователя создавшего
        $lastEntries = $workEntryModel
            ->select('
                work_entries.*,
                CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
                CONCAT(usr.last_name, " ", usr.first_name) AS created_by_name
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left')
            ->join('users usr', 'usr.id = work_entries.created_by', 'left')
            ->orderBy('work_entries.id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Топ-5 сотрудников по выплатам за месяц
        $topEmployees = $workEntryModel
            ->select('
                emp.id,
                CONCAT(emp.last_name, " ", emp.first_name) AS employee_name,
                SUM(work_entries.amount) as total_amount,
                COUNT(work_entries.id) as work_count
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left')
            ->where('work_entries.work_date >=', $monthStart)
            ->where('work_entries.work_date <=', $monthEnd)
            ->groupBy('emp.id')
            ->orderBy('total_amount', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Данные для модалки добавления сотрудника
        $departments = $departmentModel->where('is_active', 1)->findAll();
        $positions   = $positionModel->findAll();

        // Данные для модалки добавления записи
        $employees = $employeeModel
            ->select('employees.*, positions.name as position_name')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->orderBy('employees.last_name', 'ASC')
            ->findAll();

        $rates = $rateModel
            ->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left')
            ->where('rates.is_active', 1)
            ->findAll();

        $data = [
            'crumbs'        => 'Дашборд',
            'employeeCount' => $employeeCount,
            'entriesCount'  => $entriesCount,
            'monthAmount'   => $monthAmount,
            'avgAmount'     => $avgAmount,
            'activeRates'   => $activeRates,
            'lastEntries'   => $lastEntries,
            'topEmployees'  => $topEmployees,
            'departments'   => $departments,
            'positions'     => $positions,
            'employees'     => $employees,
            'rates'         => $rates,
        ];

        return view('templates/header', $data)
            . view('dushboard', $data)
            . view('templates/footer');
    }

    public function exportCsv()
    {
        $workEntryModel = new WorkEntryModel();

        // Начало и конец текущего месяца
        $monthStart = date('Y-m-01');
        $monthEnd   = date('Y-m-t');

        // Получаем записи за текущий месяц
        $entries = $workEntryModel
            ->select('
                work_entries.work_date,
                CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
                work_entries.rate_name,
                work_entries.qty,
                work_entries.rate_snapshot,
                work_entries.amount,
                work_entries.note
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left')
            ->where('work_entries.work_date >=', $monthStart)
            ->where('work_entries.work_date <=', $monthEnd)
            ->orderBy('work_entries.work_date', 'ASC')
            ->get()
            ->getResultArray();

        // Генерируем CSV
        $filename = 'dashboard_report_' . date('Y-m') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // BOM для корректного отображения кириллицы в Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Заголовки
        fputcsv($output, ['Дата', 'Сотрудник', 'Вид работы', 'Количество', 'Ставка', 'Сумма', 'Примечание'], ';');
        
        // Данные
        foreach ($entries as $entry) {
            // Форматируем дату для Excel (ДД.ММ.ГГГГ)
            $date = date('d.m.Y', strtotime($entry['work_date']));
            
            fputcsv($output, [
                $date,
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
