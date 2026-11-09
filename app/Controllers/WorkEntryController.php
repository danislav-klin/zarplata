<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EmployeeModel;
use App\Models\RateModel;
use App\Models\WorkEntryModel;
use CodeIgniter\HTTP\ResponseInterface;

class WorkEntryController extends BaseController
{
    public function index()
    {
        $model = new WorkEntryModel();
        $employeeModel = new EmployeeModel();
        $rateModel = new RateModel();

        // Параметры фильтрации и пагинации
        $perPage    = (int) ($this->request->getGet('per_page') ?? 25);
        $dateFrom   = $this->request->getGet('date_from') ?? '';
        $dateTo     = $this->request->getGet('date_to') ?? '';
        $employeeId = $this->request->getGet('employee_id') ?? '';
        $rateId     = $this->request->getGet('rate_id') ?? '';
        $search     = $this->request->getGet('search') ?? '';

        // Строим запрос через МОДЕЛЬ (не напрямую через Query Builder)
        $builder = $model
            ->select('
                work_entries.*,
                CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
                CONCAT(usr.last_name, " ", usr.first_name) AS created_by_name
            ')
            ->join('employees emp', 'emp.id = work_entries.employee_id', 'left')
            ->join('users usr', 'usr.id = work_entries.created_by', 'left');

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
        if ($rateId !== '') {
            $builder->where('work_entries.rate_id', $rateId);
        }
        if ($search !== '') {
            $builder->groupStart()
                ->like('work_entries.note', $search)
                ->orLike('work_entries.rate_name', $search)
                ->groupEnd();
        }

        // Подсчёт с сохранением builder
        $total = $builder->countAllResults(false);

        // Пагинация через встроенный метод
        $entries = $builder
            ->orderBy('work_entries.id', 'DESC')
            ->paginate($perPage, 'work_entries');

        $pager = $model->pager;

        // Данные для модалок и фильтров
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
            'crumbs'     => 'Список записей',
            'entries'    => $entries,
            'total'      => $total,
            'pager'      => $pager,
            'perPage'    => $perPage,
            'dateFrom'   => $dateFrom,
            'dateTo'     => $dateTo,
            'employeeId' => $employeeId,
            'rateId'     => $rateId,
            'search'     => $search,
            'employees'  => $employees,
            'rates'      => $rates,
        ];

        return view('templates/header', $data)
            . view('workentry/index', $data)
            . view('templates/footer');
    }


    public function store()
    {
        $rules = [
            'work_date'   => 'required|valid_date',
            'employee_id' => 'required|is_natural_no_zero',
            'rate_id'     => 'required|is_natural_no_zero',
            'qty'         => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $rateModel = new RateModel();

        // Получаем ставку с JOIN к units
        $rate = $rateModel
            ->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left')
            ->find($this->request->getPost('rate_id'));

        if (!$rate) {
            return redirect()->back()->with('errors', ['Ставка не найдена.']);
        }

        $qty    = (float) $this->request->getPost('qty');
        $amount = $rate['rate'] * $qty;

        $model = new WorkEntryModel();
        $model->insert([
            'work_date'     => $this->request->getPost('work_date'),
            'employee_id'   => $this->request->getPost('employee_id'),
            'rate_id'       => $rate['id'],
            'qty'           => $qty,
            'rate_snapshot' => $rate['rate'],
            'rate_name'     => $rate['name'],
            'rate_unit'     => $rate['unit_name'] ?? $rate['unit_short'] ?? null,
            'amount'        => $amount,
            'note'          => $this->request->getPost('note') ?: null,
            'created_by'    => session()->get('user_id'),
        ]);

        return redirect()->to('/workentries-list')->with('message', 'Запись добавлена.');
    }

    public function update()
    {
        $rules = [
            'id'          => 'required|is_natural_no_zero',
            'work_date'   => 'required|valid_date',
            'employee_id' => 'required|is_natural_no_zero',
            'rate_id'     => 'required|is_natural_no_zero',
            'qty'         => 'required|decimal|greater_than[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $rateModel = new RateModel();
        $rate = $rateModel
            ->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left')
            ->find($this->request->getPost('rate_id'));

        if (!$rate) {
            return redirect()->back()->with('errors', ['Ставка не найдена.']);
        }

        $qty    = (float) $this->request->getPost('qty');
        $amount = $rate['rate'] * $qty;

        $model = new WorkEntryModel();
        $model->update($this->request->getPost('id'), [
            'work_date'     => $this->request->getPost('work_date'),
            'employee_id'   => $this->request->getPost('employee_id'),
            'rate_id'       => $rate['id'],
            'qty'           => $qty,
            'rate_snapshot' => $rate['rate'],
            'rate_name'     => $rate['name'],
            'rate_unit'     => $rate['unit_name'] ?? $rate['unit_short'] ?? null,
            'amount'        => $amount,
            'note'          => $this->request->getPost('note') ?: null,
        ]);

        return redirect()->to('/workentries-list')->with('message', 'Запись обновлена.');
    }

    public function delete(int $id)
    {
        $model = new WorkEntryModel();
        $model->delete($id);

        return redirect()->to('/workentries-list')->with('message', 'Запись удалена.');
    }

    public function exportCsv()
{
    $model = new WorkEntryModel();

    // Те же параметры фильтрации, что и в index()
    $dateFrom   = $this->request->getGet('date_from') ?? '';
    $dateTo     = $this->request->getGet('date_to') ?? '';
    $employeeId = $this->request->getGet('employee_id') ?? '';
    $rateId     = $this->request->getGet('rate_id') ?? '';
    $search     = $this->request->getGet('search') ?? '';

    $builder = $model
        ->select('
            work_entries.work_date,
            CONCAT(emp.last_name, " ", emp.first_name, " ", IFNULL(emp.surname, "")) AS employee_name,
            work_entries.rate_name,
            work_entries.rate_unit,
            work_entries.qty,
            work_entries.rate_snapshot,
            work_entries.amount,
            work_entries.note,
            CONCAT(usr.last_name, " ", usr.first_name) AS created_by_name
        ')
        ->join('employees emp', 'emp.id = work_entries.employee_id', 'left')
        ->join('users usr', 'usr.id = work_entries.created_by', 'left');

    if ($dateFrom !== '') {
        $builder->where('work_entries.work_date >=', $dateFrom);
    }
    if ($dateTo !== '') {
        $builder->where('work_entries.work_date <=', $dateTo);
    }
    if ($employeeId !== '') {
        $builder->where('work_entries.employee_id', $employeeId);
    }
    if ($rateId !== '') {
        $builder->where('work_entries.rate_id', $rateId);
    }
    if ($search !== '') {
        $builder->groupStart()
            ->like('work_entries.note', $search)
            ->orLike('work_entries.rate_name', $search)
            ->groupEnd();
    }

    $entries = $builder->orderBy('work_entries.id', 'DESC')->findAll();

    // Формируем CSV в памяти
    $filename = 'workentries_' . date('Y-m-d_H-i') . '.csv';
    $handle   = fopen('php://temp', 'r+');

    // BOM для корректного открытия кириллицы в Excel
    fwrite($handle, "\xEF\xBB\xBF");

    // Заголовок
    fputcsv($handle, [
        'Дата',
        'Сотрудник',
        'Вид работы',
        'Ед. изм.',
        'Кол-во',
        'Ставка',
        'Сумма',
        'Примечание',
        'Создал',
    ], ';');

    // Данные
    foreach ($entries as $row) {
        fputcsv($handle, [
            $row['work_date'],
            trim($row['employee_name']),
            $row['rate_name'] ?? '',
            $row['rate_unit'] ?? '',
            $row['qty'],
            number_format((float) $row['rate_snapshot'], 2, ',', ''),
            number_format((float) $row['amount'], 2, ',', ''),
            $row['note'] ?? '',
            $row['created_by_name'] ?? '',
        ], ';');
    }

    rewind($handle);
    $csv = stream_get_contents($handle);
    fclose($handle);

    return $this->response
        ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
        ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
        ->setBody($csv);
}
}
