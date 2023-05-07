<?php
/*
 * File name: DoctorDataTable.php
 * Last modified: 2021.11.24 at 19:18:10
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Doctor;
use App\Models\Post;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class DoctorDataTable extends DataTable
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
            ->editColumn('image', function ($doctor) {
                return getMediaColumn($doctor, 'image');
            })
            ->editColumn('name', function ($doctor) {
                if ($doctor['featured']) {
                    return $doctor['name'] . "<span class='badge bg-" . setting('theme_color') . " p-1 m-2'>" . trans('lang.doctor_featured') . "</span>";
                }
                return $doctor['name'];
            })
            ->editColumn('price', function ($doctor) {
                return getPriceColumn($doctor);
            })
            ->editColumn('discount_price', function ($doctor) {
                if (empty($doctor['discount_price'])) {
                    return '-';
                } else {
                        return getPriceColumn($doctor, 'discount_price');
                }
            })
            ->editColumn('updated_at', function ($doctor) {
                return getDateColumn($doctor, 'updated_at');
            })
            ->editColumn('specialities', function ($doctor) {
                return getLinksColumnByRouteName($doctor->specialities, 'specialities.edit', 'id', 'name');
            })
            ->editColumn('clinic.name', function ($doctor) {
                return getLinksColumnByRouteName([$doctor->clinic], 'clinics.edit', 'id', 'name');
            })
            ->editColumn('available', function ($doctor) {
                return getBooleanColumn($doctor, 'available');
            })
            ->addColumn('action', 'doctors.datatables_actions')
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
                'data' => 'image',
                'title' => trans('lang.doctor_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'name',
                'title' => trans('lang.doctor_name'),

            ],
            [
                'data' => 'clinic.name',
                'name' => 'clinic.name',
                'title' => trans('lang.doctor_clinic_id'),

            ],
            [
                'data' => 'price',
                'title' => trans('lang.doctor_price'),

            ],
            [
                'data' => 'discount_price',
                'title' => trans('lang.doctor_discount_price'),

            ],
            [
                'data' => 'specialities',
                'title' => trans('lang.doctor_specialities'),
                'searchable' => false,
                'orderable' => false
            ],
            [
                'data' => 'available',
                'title' => trans('lang.doctor_available'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.doctor_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Doctor::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Doctor::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.doctor_' . $field->name),
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
     * @param Doctor $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Doctor $model)
    {
        if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()->with("clinic")->join('clinic_users', 'clinic_users.clinic_id', '=', 'doctors.clinic_id')
                ->groupBy('doctors.id')
                ->where('clinic_users.user_id', auth()->id())
                ->select('doctors.*');
        }elseif (auth()->user()->hasRole('doctor')) {
            return $model->newQuery()->with("clinic")
                ->groupBy('doctors.id')
                ->where('doctors.user_id', auth()->id())
                ->select('doctors.*');
        }
        return $model->newQuery()->with("clinic")->select("$model->table.*");
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
        return 'doctorsdatatable_' . time();
    }
}
