<?php
/*
 * File name: OptionDataTable.php
 * Last modified: 2021.11.24 at 19:18:10
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Option;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class OptionDataTable extends DataTable
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
    public function dataTable($query)
    {
        $dataTable = new EloquentDataTable($query);
        $columns = array_column($this->getColumns(), 'data');
        $dataTable = $dataTable
            ->editColumn('name', function ($option) {
                return $option->name;
            })
            ->editColumn('image', function ($option) {
                return getMediaColumn($option, 'image');
            })
            ->editColumn('price', function ($option) {
                return getPriceColumn($option);
            })
            ->editColumn('doctor.name', function ($option) {
                return getLinksColumnByRouteName([$option->doctor], 'doctors.edit', 'id', 'name');
            })
            ->editColumn('option_group.name', function ($option) {
                return getLinksColumnByRouteName([$option->optionGroup], 'optionGroups.edit', 'id', 'name');
            })
            ->editColumn('doctor.clinic.name', function ($option) {
                if (isset($option->doctor))
                    return getLinksColumnByRouteName([$option->doctor->clinic], 'clinics.edit', 'id', 'name');
                else
                    return "";
            })
            ->editColumn('updated_at', function ($option) {
                return getDateColumn($option);
            })
            ->addColumn('action', 'options.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'name',
                'title' => trans('lang.option_name'),

            ],
            [
                'data' => 'image',
                'title' => trans('lang.option_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'price',
                'title' => trans('lang.option_price'),

            ],
            [
                'data' => 'doctor.name',
                'title' => trans('lang.doctor'),

            ],
            [
                'data' => 'doctor.clinic.name',
                'name' => 'doctor.clinic.name',
                'title' => trans('lang.clinic'),

            ],
            [
                'data' => 'option_group.name',
                'name' => 'optionGroup.name',
                'title' => trans('lang.option_group'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.option_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Option::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Option::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.option_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get query source of dataTable.
     *
     * @param Option $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Option $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("doctor")->with("optionGroup")->with('doctor.clinic')->select("$model->table.*");
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()->with("doctor")->with("optionGroup")->with('doctor.clinic')
                ->join("doctors", "options.doctor_id", "=", "doctors.id")
                ->join("clinic_users", "doctors.clinic_id", "=", "clinic_users.clinic_id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy("options.id")
                ->select("$model->table.*");
        } else {
            return $model->newQuery()->with("doctor")->with("optionGroup")->with('doctor.clinic')->select("$model->table.*");
        }
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
            ->addAction(['title' => trans('lang.actions'), 'width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
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
        return 'optionsdatatable_' . time();
    }
}
