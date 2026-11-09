<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;
use App\Models\PositionModel;
use CodeIgniter\HTTP\ResponseInterface;

class PositionController extends BaseController
{
    public function index()
    {
        $model = new PositionModel();
        $departmentModel = new DepartmentModel();

        // Получаем список должностей с информацией об отделе
        $positions = $model
            ->select('positions.*, departments.name as department_name')
            ->join('departments', 'departments.id = positions.department_id', 'left')
            ->orderBy('positions.created_at', 'DESC')
            ->findAll();

        // Получаем список отделов для модалок
        $departments = $departmentModel
            ->where('is_active', 1)
            ->findAll();

        $data = [
            'crumbs'      => 'Список должностей',
            'positions'   => $positions,
            'departments' => $departments,
        ];

        return view('templates/header', $data)
            . view('position/index', $data)
            . view('templates/footer');
    }

    // сохранить
    public function store()
    {
        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new PositionModel();
        $model->insert([
            'name'          => $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'description'   => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to('/positions-list')->with('message', 'Должность добавлена.');
    }

    // обновить
    public function update()
    {
        $rules = [
            'id'   => 'required|is_natural_no_zero',
            'name' => 'required|min_length[2]|max_length[100]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new PositionModel();
        $model->update($this->request->getPost('id'), [
            'name'          => $this->request->getPost('name'),
            'department_id' => $this->request->getPost('department_id') ?: null,
            'description'   => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to('/positions-list')->with('message', 'Должность обновлена.');
    }

    // удалить
    public function delete(int $id)
    {
        $model = new PositionModel();

        // Проверяем, используется ли должность
        $employeeModel = new \App\Models\EmployeeModel();
        $usageCount = $employeeModel->where('position_id', $id)->countAllResults();

        if ($usageCount > 0) {
            return redirect()->back()->with('errors', ["Должность используется у {$usageCount} сотрудников. Удаление невозможно."]);
        }

        $model->delete($id);

        return redirect()->to('/positions-list')->with('message', 'Должность удалена.');
    }
}
