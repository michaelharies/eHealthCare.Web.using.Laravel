<?php
/*
 * File name: ExperienceDataTable.php
 * Last modified: 2021.11.24 at 19:18:10
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Experience;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class ExperienceDataTable extends DataTable
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
            ->editColumn('updated_at', function ($experience) {
                return getDateColumn($experience, 'updated_at');
            })
            ->editColumn('title', function ($experience) {
                return $experience->title;
            })
            ->editColumn('description', function ($experience) {
                return getStripedHtmlColumn($experience, 'description');
            })
            ->editColumn('doctor.name', function ($experience) {
                return getLinksColumnByRouteName([$experience->doctor], 'doctors.edit', 'id', 'name');
            })
            ->addColumn('action', 'experiences.datatables_actions')
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
                'data' => 'title',
                'title' => trans('lang.experience_title'),

            ],
            [
                'data' => 'description',
                'title' => trans('lang.experience_description'),

            ],
            [
                'data' => 'doctor.name',
                'title' => trans('lang.experience_doctor_id'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.experience_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Experience::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Experience::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.experience_' . $field->name),
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
     * @param Experience $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Experience $model)
    {
        if (auth()->user()->hasRole('clinic_owner')) {
            $model->newQuery()->with("user")->with("doctor")->join("doctors", "doctors.id", "=", "experiences.doctor_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy('experiences.id')
                ->select('experiences.*');
        }
        if (auth()->user()->hasRole('doctor')) {
            return $model->newQuery()->with("doctor")->join('doctors', 'doctors.id', '=', 'experiences.doctor_id')
                ->groupBy('experiences.id')
                ->select('experiences.*')
                ->where('doctors.user_id', auth()->id());
        } else {
            return $model->newQuery()->with("doctor")->select("$model->table.*");
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
        return 'experiencesdatatable_' . time();
    }
}
