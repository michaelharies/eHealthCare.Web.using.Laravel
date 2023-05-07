<?php
/*
 * File name: EarningDataTable.php
 * Last modified: 2021.11.24 at 19:22:02
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Earning;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class EarningDataTable extends DataTable
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
            ->editColumn('doctor.name', function ($earning) {
                return getLinksColumnByRouteName([$earning->doctor], "doctors.edit", 'id', 'name');
            })
            ->editColumn('clinic.name', function ($earning) {
                return getLinksColumnByRouteName([$earning->clinic], "clinics.edit", 'id', 'name');
            })
            ->editColumn('updated_at', function ($earning) {
                return getDateColumn($earning);
            })
            ->editColumn('total_earning', function ($earning) {
                return getPriceColumn($earning, 'total_earning');
            })
            ->editColumn('admin_earning', function ($earning) {
                return getPriceColumn($earning, 'admin_earning');
            })
            ->editColumn('doctor_earning', function ($earning) {
                return getPriceColumn($earning, 'doctor_earning');
            })
            ->editColumn('clinic_earning', function ($earning) {
                return getPriceColumn($earning, 'clinic_earning');
            })
            ->editColumn('taxes', function ($earning) {
                return getPriceColumn($earning, 'taxes');
            })
            ->addColumn('action', 'earnings.datatables_actions')
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
                'data' => 'doctor.name',
                'name' => 'doctor.name',
                'title' => trans('lang.earning_doctor_id'),

            ],
            [
                'data' => 'clinic.name',
                'name' => 'clinic.name',
                'title' => trans('lang.earning_clinic_id'),

            ],
            [
                'data' => 'total_appointments',
                'title' => trans('lang.earning_total_appointments'),

            ],
            [
                'data' => 'total_earning',
                'title' => trans('lang.earning_total_earning'),

            ],
            [
                'data' => 'doctor_earning',
                'title' => trans('lang.earning_doctor_earning'),

            ],
            [
                'data' => 'admin_earning',
                'title' => trans('lang.earning_admin_earning'),

            ],
            [
                'data' => 'clinic_earning',
                'title' => trans('lang.earning_clinic_earning'),

            ],
            [
                'data' => 'taxes',
                'title' => trans('lang.earning_taxes'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.earning_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Earning::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Earning::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.earning_' . $field->name),
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
     * @param Earning $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Earning $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("clinic")->with("doctor")->select("$model->table.*");
        } else if ((auth()->user()->hasRole('clinic_owner'))) {
            return $model->newQuery()->with("clinic")->with("doctor")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "earnings.clinic_id")
                ->where('clinic_users.user_id', auth()->id())->select("$model->table.*");
        }else if ((auth()->user()->hasRole('doctor'))) {
            return $model->newQuery()->with("clinic")->with("doctor")
                ->join("doctors", "doctors.id", "=", "earnings.doctor_id")
                ->where('doctors.user_id', auth()->id())->select("$model->table.*");
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
            ->addAction(['width' => '80px', 'printable' => false, 'responsivePriority' => '100'])
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
        return 'earningsdatatable_' . time();
    }
}
