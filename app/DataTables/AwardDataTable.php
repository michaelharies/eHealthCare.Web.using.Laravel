<?php
/*
 * File name: AwardDataTable.php
 * Last modified: 2021.11.24 at 19:13:49
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\Award;
use App\Models\CustomField;
use App\Models\Post;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class AwardDataTable extends DataTable
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
            ->editColumn('updated_at', function ($award) {
                return getDateColumn($award, 'updated_at');
            })
            ->editColumn('title', function ($award) {
                return $award->title;
            })
            ->editColumn('description', function ($award) {
                return getStripedHtmlColumn($award, 'description');
            })
            ->editColumn('clinic.name', function ($award) {
                return getLinksColumnByRouteName([$award->clinic], 'clinics.edit', 'id', 'name');
            })
            ->addColumn('action', 'awards.datatables_actions')
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
                'title' => trans('lang.award_title'),

            ],
            [
                'data' => 'description',
                'title' => trans('lang.award_description'),

            ],
            [
                'data' => 'clinic.name',
                'title' => trans('lang.award_clinic_id'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.award_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Award::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Award::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.award_' . $field->name),
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
     * @param Award $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Award $model)
    {
        if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()->with("clinic")
                ->join('clinic_users', 'clinic_users.clinic_id', '=', 'awards.clinic_id')
                ->groupBy('awards.id')
                ->select('awards.*')
                ->where('clinic_users.user_id', auth()->id());
        } else if (auth()->user()->hasRole('doctor')) {
            return $model->newQuery()->with("clinic")
                ->join("clinics", "clinics.id", "=", "awards.clinic_id")
                ->join("doctors", "doctors.clinic_id", "=", "clinics.id")
                ->where('doctors.user_id', auth()->id())
                ->groupBy('awards.id')
                ->select('awards.*');
        }
        else {
            return $model->newQuery()->with("clinic")->select('awards.*');
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
        return 'awardsdatatable_' . time();
    }
}
