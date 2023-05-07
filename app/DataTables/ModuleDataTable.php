<?php
/*
 * File name: ModuleDataTable.php
 * Last modified: 2022.04.03 at 13:55:01
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Nwidart\Modules\Facades\Module;
use Yajra\DataTables\CollectionDataTable;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class ModuleDataTable extends DataTable
{
    /**
     * custom fields columns
     * @var array
     */
    public static $customFields = [];

    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return DataTableAbstract
     */
    public function dataTable($query): DataTableAbstract
    {
        $dataTable = new CollectionDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('title', function ($module) {
                return "<a target='_blank' class='text-bold text-dark' href='" . url($module['url']) . "'>" . $module['title'] . "</a><span class='mx-2 small'>" . implode('.', str_split(substr($module['version'], 1, 3))) . "</span>";
            })
            ->editColumn('image', function ($module) {
                return "<a target='_blank' href='" . url($module['url']) . "'><img class='rounded' style='height:80px' src='" . $module['image'] . "' alt='" . $module['id'] . "'></a>";
            })
            ->editColumn('price', function ($module) {
                if (empty($module['discountPrice'])) {
                    return "<b>" . $module['price'] . "<b>";
                }
                return "<b>" . $module['discountPrice'] . "</b><del class='mx-1'>" . $module['price'] . "</del>";
            })
            ->editColumn('enabled', function ($module) {
                return getBooleanColumn($module, 'enabled');
            })
            ->addColumn('action', 'modules.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));
        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns(): array
    {
        $columns = [
            [
                'data' => 'image',
                'title' => trans('lang.module_image'),
            ],
            [
                'data' => 'title',
                'title' => trans('lang.module_title'),
            ],
            [
                'data' => 'description',
                'title' => trans('lang.module_description'),

            ],
            [
                'data' => 'author',
                'title' => trans('lang.module_author'),
            ],
            [
                'data' => 'enabled',
                'title' => trans('lang.module_enabled'),

            ],
            [
                'data' => 'price',
                'title' => trans('lang.module_price'),
            ],
        ];
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @return Collection
     */
    public function query(): Collection
    {
        $collection = Module::toCollection();
        $array = $collection->toArray();
        foreach ($array as &$row) {
            $row['image'] = $row['image'] ?? null;
            $row['installed'] = Module::isInstalled($row['id']);
            $row['updated'] = Module::isUpdated($row['id'], $row['version']);
            $row['enabled'] = Module::isActivated($row['name']);
        }
        return collect($array);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return Builder
     */
    public function html()
    {
        return $this->builder()
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->addAction(['printable' => false, 'responsivePriority' => '100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/' . app()->getLocale() . '/datatable.json')
                        ), true)
                ]
            ));
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename() . '.pdf');
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'modulesdatatable_' . time();
    }

    /**
     * @param string $id
     * @param $version
     * @return bool
     */
    private function isContainsUpdate(string $id, $version = null): bool
    {
        $executedMigrations = $this->getExecutedMigrations();
        $newMigrations = $this->getNewMigrations($id, $version);
        return (!empty($newMigrations)) && count(array_intersect($newMigrations, $executedMigrations->toArray())) < count($newMigrations);
    }

    /**
     * Get the migrations that have already been ran.
     *
     * @return Collection List of migrations
     */
    private function getExecutedMigrations(): Collection
    {
        // migrations table should exist, if not, user will receive an error.
        return DB::table('migrations')->get()->pluck('migration');
    }

    /**
     * @param string $id
     * @param $version
     * @return array|false|string|string[]
     */
    private function getNewMigrations(string $id, $version = null)
    {
        if (empty($version)) {
            $migrations = glob(module_path($id, 'Database/Migrations') . DIRECTORY_SEPARATOR . '*.php');
        } else {
            $migrations = glob(module_path($id, 'Database/Migrations') . DIRECTORY_SEPARATOR . $version . DIRECTORY_SEPARATOR . '*.php');
        }
        $migrations = array_map(function ($element) {
            return Arr::last(explode(DIRECTORY_SEPARATOR, $element));
        }, $migrations);
        return str_replace('.php', '', $migrations);
    }
}
