<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\EmployeeModel;
use CodeIgniter\HTTP\ResponseInterface;

class DepartmentController extends BaseController
{
    public function index()
    {
        $model = new DepartmentModel();
        $employeeModel = new EmployeeModel();

        // Параметры пагинации и поиска
        $perPage = (int) ($this->request->getGet('per_page') ?? 25);
        $search  = $this->request->getGet('search') ?? '';

        // Строим запрос
        $builder = $model->select('departments.*');

        // Поиск
        if ($search !== '') {
            $builder->groupStart()
                ->like('departments.name', $search)
                ->orLike('departments.description', $search)
                ->groupEnd();
        }

        // Подсчёт
        $total = $builder->countAllResults(false);

        // Получаем данные с пагинацией
        $departments = $builder
            ->orderBy('departments.name', 'ASC')
            ->paginate($perPage, 'departments');

        $pager = $model->pager;

        // Подсчитываем количество сотрудников в каждом отделе
        foreach ($departments as &$dep) {
            $dep['employee_count'] = $employeeModel
                ->where('department_id', $dep['id'])
                ->countAllResults();
        }

        $data = [
            'crumbs'      => 'Список отделов',
            'departments' => $departments,
            'total'       => $total,
            'pager'       => $pager,
            'perPage'     => $perPage,
            'search'      => $search,
        ];

        return view('templates/header', $data)
            . view('department/index', $data)
            . view('templates/footer');
    }

    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new DepartmentModel();
        $model->insert([
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description') ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/department-list')->with('message', 'Отдел добавлен.');
    }

    public function update()
    {
        $rules = [
            'id'   => 'required|is_natural_no_zero',
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new DepartmentModel();
        $model->update($this->request->getPost('id'), [
            'name'        => $this->request->getPost('name'),
            'description' => $this->request->getPost('description') ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/department-list')->with('message', 'Отдел обновлён.');
    }

    public function delete(int $id)
    {
        $model = new DepartmentModel();

        // Проверяем, используется ли отдел
        $employeeModel = new EmployeeModel();
        $usageCount = $employeeModel->where('department_id', $id)->countAllResults();

        if ($usageCount > 0) {
            return redirect()->back()->with('errors', ["Отдел используется у {$usageCount} сотрудников. Удаление невозможно."]);
        }

        $model->delete($id);

        return redirect()->to('/department-list')->with('message', 'Отдел удалён.');
    }

    public function view(int $id)
    {
        $model = new DepartmentModel();
        $department = $model->find($id);

        if (!$department) {
            return redirect()->to('/department-list')->with('errors', ['Отдел не найден.']);
        }

        // Получаем сотрудников отдела
        $employeeModel = new EmployeeModel();
        $employees = $employeeModel
            ->select('employees.*, positions.name as position_name')
            ->join('positions', 'positions.id = employees.position_id', 'left')
            ->where('employees.department_id', $id)
            ->findAll();

        $data = [
            'crumbs'     => 'Просмотр отдела',
            'department' => $department,
            'employees'  => $employees,
        ];

        return view('templates/header', $data)
            . view('department/view', $data)
            . view('templates/footer');
    }

    public function exportCsv()
    {
        $model         = new DepartmentModel();
        $employeeModel = new EmployeeModel();

        $search = $this->request->getGet('search') ?? '';

        $builder = $model->select('departments.*');

        if ($search !== '') {
            $builder->groupStart()
                ->like('departments.name', $search)
                ->orLike('departments.description', $search)
                ->groupEnd();
        }

        $departments = $builder->orderBy('departments.name', 'ASC')->findAll();

        // Формируем CSV в памяти
        $filename = 'departments_' . date('Y-m-d_H-i') . '.csv';
        $handle   = fopen('php://temp', 'r+');

        // BOM для корректного открытия кириллицы в Excel
        fwrite($handle, "\xEF\xBB\xBF");

        // Заголовок
        fputcsv($handle, [
            'Название',
            'Описание',
            'Сотрудников',
            'Активен',
        ], ';');

        // Данные
        foreach ($departments as $dep) {
            $employeeCount = $employeeModel
                ->where('department_id', $dep['id'])
                ->countAllResults();

            fputcsv($handle, [
                $dep['name'],
                $dep['description'] ?? '',
                $employeeCount,
                $dep['is_active'] ? 'Да' : 'Нет',
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
