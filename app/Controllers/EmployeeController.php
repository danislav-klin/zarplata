<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use App\Models\PositionModel;
use CodeIgniter\HTTP\ResponseInterface;

class EmployeeController extends BaseController
{
    public function index()
    {
        $employeeModel   = new EmployeeModel();
        $departmentModel = new DepartmentModel();
        $positionModel   = new PositionModel();

        $perPage    = (int) ($this->request->getGet('per_page') ?? 25);
        $search     = $this->request->getGet('search') ?? '';
        $deptFilter = $this->request->getGet('department_id') ?? '';
        $posFilter  = $this->request->getGet('position_id') ?? '';

        $builder = $employeeModel
            ->select('employees.*, departments.name AS department_name, positions.name AS position_name')
            ->join('departments', 'departments.id = employees.department_id', 'left')
            ->join('positions', 'positions.id = employees.position_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('employees.last_name', $search)
                ->orLike('employees.first_name', $search)
                ->orLike('employees.surname', $search)
                ->orLike('employees.personnel_number', $search)
                ->orLike('employees.phone', $search)
                ->orLike('employees.email', $search)
                ->groupEnd();
        }

        if ($deptFilter !== '') {
            $builder->where('employees.department_id', $deptFilter);
        }

        if ($posFilter !== '') {
            $builder->where('employees.position_id', $posFilter);
        }

        $total     = $builder->countAllResults(false); // false = не сбрасывать builder
        $employees = $builder->paginate($perPage, 'employees');
        $pager     = $employeeModel->pager;

        $data = [
            'crumbs'      => 'Список сотрудников',
            'employees'   => $employees,
            'total'       => $total,
            'pager'       => $pager,
            'perPage'     => $perPage,
            'search'      => $search,
            'deptFilter'  => $deptFilter,
            'posFilter'   => $posFilter,
            'departments' => $departmentModel->where('is_active', 1)->findAll(),
            'positions'   => $positionModel->findAll(),
        ];

        echo view('templates/header', $data);
        echo view('employee/index', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'surname'          => 'permit_empty|max_length[100]',
            'email'            => 'permit_empty|valid_email|is_unique[employees.email]',
            'phone'            => 'permit_empty|max_length[50]',
            'department_id'    => 'permit_empty|is_natural_no_zero',
            'position_id'      => 'permit_empty|is_natural_no_zero',
            'hired_at'         => 'permit_empty|valid_date',
            'personnel_number' => 'permit_empty|is_unique[employees.personnel_number]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new EmployeeModel();
        $model->insert([
            'first_name'       => $this->request->getPost('first_name'),
            'last_name'        => $this->request->getPost('last_name'),
            'surname'          => $this->request->getPost('surname') ?: null,
            'email'            => $this->request->getPost('email') ?: null,
            'phone'            => $this->request->getPost('phone') ?: null,
            'department_id'    => $this->request->getPost('department_id') ?: null,
            'position_id'      => $this->request->getPost('position_id') ?: null,
            'hired_at'         => $this->request->getPost('hired_at') ?: null,
            'personnel_number' => $this->request->getPost('personnel_number') ?: null,
            'note'             => $this->request->getPost('note') ?: null,
        ]);

        return redirect()->to('/employees-list')->with('message', 'Сотрудник добавлен.');
    }

    public function update()
    {
        $id = (int) $this->request->getPost('id');

        $rules = [
            'first_name'       => 'required|min_length[2]|max_length[100]',
            'last_name'        => 'required|min_length[2]|max_length[100]',
            'surname'          => 'permit_empty|max_length[100]',
            'email'            => "permit_empty|valid_email|is_unique[employees.email,id,{$id}]",
            'phone'            => 'permit_empty|max_length[50]',
            'department_id'    => 'permit_empty|is_natural_no_zero',
            'position_id'      => 'permit_empty|is_natural_no_zero',
            'hired_at'         => 'permit_empty|valid_date',
            'personnel_number' => "permit_empty|is_unique[employees.personnel_number,id,{$id}]",
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new EmployeeModel();
        $model->update($id, [
            'first_name'       => $this->request->getPost('first_name'),
            'last_name'        => $this->request->getPost('last_name'),
            'surname'          => $this->request->getPost('surname') ?: null,
            'email'            => $this->request->getPost('email') ?: null,
            'phone'            => $this->request->getPost('phone') ?: null,
            'department_id'    => $this->request->getPost('department_id') ?: null,
            'position_id'      => $this->request->getPost('position_id') ?: null,
            'hired_at'         => $this->request->getPost('hired_at') ?: null,
            'personnel_number' => $this->request->getPost('personnel_number') ?: null,
            'note'             => $this->request->getPost('note') ?: null,
        ]);

        return redirect()->to('/employees-list')->with('message', 'Данные сотрудника обновлены.');
    }

    public function delete(int $id)
    {
        $model = new EmployeeModel();
        $model->delete($id);

        return redirect()->to('/employees-list')->with('message', 'Сотрудник удалён.');
    }

    public function exportCsv()
    {
        $model = new EmployeeModel();

        $search     = $this->request->getGet('search') ?? '';
        $deptFilter = $this->request->getGet('department_id') ?? '';
        $posFilter  = $this->request->getGet('position_id') ?? '';

        $builder = $model
            ->select('employees.*, positions.name AS position_name, departments.name AS department_name')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->join('departments', 'departments.id = employees.department_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('employees.last_name', $search)
                ->orLike('employees.first_name', $search)
                ->orLike('employees.surname', $search)
                ->orLike('employees.personnel_number', $search)
                ->orLike('employees.phone', $search)
                ->orLike('employees.email', $search)
                ->groupEnd();
        }
        if ($deptFilter !== '') {
            $builder->where('employees.department_id', $deptFilter);
        }
        if ($posFilter !== '') {
            $builder->where('employees.position_id', $posFilter);
        }

        $employees = $builder->orderBy('employees.last_name', 'ASC')->findAll();

        $filename = 'employees_' . date('Y-m-d_H-i') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF"); // BOM — кириллица в Excel

        fputcsv($handle, [
            'ФИО',
            'Таб. №',
            'Должность',
            'Отдел',
            'Телефон',
            'Email',
            'Дата приёма',
        ], ';');

        foreach ($employees as $emp) {
            $fio = trim($emp['last_name'] . ' ' . $emp['first_name'] . ' ' . ($emp['surname'] ?? ''));

            fputcsv($handle, [
                $fio,
                $emp['personnel_number'] ?? '',
                $emp['position_name'] ?? '',
                $emp['department_name'] ?? '',
                $emp['phone'] ?? '',
                $emp['email'] ?? '',
                $emp['hired_at'] ?? '',
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
