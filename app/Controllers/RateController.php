<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RateModel;
use App\Models\UnitModel;
use CodeIgniter\HTTP\ResponseInterface;

class RateController extends BaseController
{
    public function index()
    {
        $rateModel = new RateModel();
        $unitModel = new UnitModel();

        $perPage    = (int) ($this->request->getGet('per_page') ?? 25);
        $search     = $this->request->getGet('search') ?? '';
        $unitFilter = $this->request->getGet('unit_id') ?? '';

        $builder = $rateModel
            ->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('rates.name', $search)
                ->orLike('rates.description', $search)
                ->groupEnd();
        }

        if ($unitFilter !== '') {
            $builder->where('rates.unit_id', $unitFilter);
        }

        $total = $builder->countAllResults(false);
        $rates = $builder->orderBy('rates.created_at', 'DESC')->paginate($perPage, 'rates');
        $pager = $rateModel->pager;

        $units = $unitModel->orderBy('name', 'ASC')->findAll();

        $data = [
            'crumbs'     => 'Список ставок',
            'rates'      => $rates,
            'total'      => $total,
            'pager'      => $pager,
            'perPage'    => $perPage,
            'search'     => $search,
            'unitFilter' => $unitFilter,
            'units'      => $units,
        ];

        echo view('templates/header', $data);
        echo view('rate/index', $data);
        echo view('templates/footer');
    }

    public function store()
    {
        $rules = [
            'name'        => 'required|min_length[2]|max_length[255]',
            'rate'        => 'required|decimal|greater_than[0]',
            'unit_id'     => 'required|is_natural_no_zero',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new RateModel();
        $model->insert([
            'name'        => $this->request->getPost('name'),
            'rate'        => $this->request->getPost('rate'),
            'unit_id'     => $this->request->getPost('unit_id'),
            'description' => $this->request->getPost('description') ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/rates-list')->with('message', 'Ставка добавлена.');
    }

    public function update()
    {
        $id = (int) $this->request->getPost('id');

        $rules = [
            'name'        => 'required|min_length[2]|max_length[255]',
            'rate'        => 'required|decimal|greater_than[0]',
            'unit_id'     => 'required|is_natural_no_zero',
            'description' => 'permit_empty',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new RateModel();
        $model->update($id, [
            'name'        => $this->request->getPost('name'),
            'rate'        => $this->request->getPost('rate'),
            'unit_id'     => $this->request->getPost('unit_id'),
            'description' => $this->request->getPost('description') ?: null,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ]);

        return redirect()->to('/rates-list')->with('message', 'Ставка обновлена.');
    }

    public function delete(int $id)
    {
        $model = new RateModel();
        $model->delete($id);

        return redirect()->to('/rates-list')->with('message', 'Ставка удалена.');
    }

    // Добавление единицы измерения
    public function storeUnit()
    {
        $rules = [
            'name'       => 'required|min_length[2]|max_length[100]|is_unique[units.name]',
            'short_name' => 'permit_empty|max_length[20]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new UnitModel();
        $model->insert([
            'name'        => $this->request->getPost('name'),
            'short_name'  => $this->request->getPost('short_name') ?: null,
            'description' => $this->request->getPost('description') ?: null,
        ]);

        return redirect()->to('/rates-list')->with('message', 'Единица измерения добавлена.');
    }

    public function exportCsv()
    {
        $rateModel = new RateModel();

        $search     = $this->request->getGet('search') ?? '';
        $unitFilter = $this->request->getGet('unit_id') ?? '';

        $builder = $rateModel
            ->select('rates.*, units.name as unit_name, units.short_name as unit_short')
            ->join('units', 'units.id = rates.unit_id', 'left');

        if ($search !== '') {
            $builder->groupStart()
                ->like('rates.name', $search)
                ->orLike('rates.description', $search)
                ->groupEnd();
        }

        if ($unitFilter !== '') {
            $builder->where('rates.unit_id', $unitFilter);
        }

        $rates = $builder->orderBy('rates.name', 'ASC')->findAll();

        $filename = 'rates_' . date('Y-m-d_H-i') . '.csv';
        $handle   = fopen('php://temp', 'r+');
        fwrite($handle, "\xEF\xBB\xBF"); // BOM — кириллица в Excel

        fputcsv($handle, [
            'Название',
            'Единица измерения',
            'Краткое обозначение',
            'Ставка',
            'Описание',
            'Активна',
        ], ';');

        foreach ($rates as $rate) {
            fputcsv($handle, [
                $rate['name'],
                $rate['unit_name'] ?? '',
                $rate['unit_short'] ?? '',
                number_format((float) $rate['rate'], 2, ',', ''),
                $rate['description'] ?? '',
                $rate['is_active'] ? 'Да' : 'Нет',
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
