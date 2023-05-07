<?php
/*
 * File name: DoctorReviewDataTable.php
 * Last modified: 2021.11.24 at 19:18:10
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\DoctorReview;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class DoctorReviewDataTable extends DataTable
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
            ->editColumn('updated_at', function ($doctorReview) {
                return getDateColumn($doctorReview, 'updated_at');
            })
            ->editColumn('user.name', function ($doctorReview) {
                return getLinksColumnByRouteName([$doctorReview->user], 'users.edit', 'id', 'name');
            })
            ->editColumn('doctor.name', function ($doctorReview) {
                return getLinksColumnByRouteName([$doctorReview->doctor], 'doctors.edit', 'id', 'name');
            })
            ->addColumn('action', 'doctor_reviews.datatables_actions')
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
                'data' => 'review',
                'title' => trans('lang.doctor_review_review'),

            ],
            [
                'data' => 'rate',
                'title' => trans('lang.doctor_review_rate'),

            ],
            [
                'data' => 'user.name',
                'title' => trans('lang.doctor_review_user_id'),

            ],
            [
                'name' => 'doctor.name',
                'data' => 'doctor.name',
                'title' => trans('lang.doctor_review_doctor_id'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.doctor_review_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(DoctorReview::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', DoctorReview::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.doctor_review_' . $field->name),
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
     * @param DoctorReview $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(DoctorReview $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("user")->with("doctor")->select("$model->table.*");
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()->with("user")->with("doctor")
                ->join("doctors", "doctors.id", "=", "doctor_reviews.doctor_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');
        }else if(auth()->user()->hasRole('doctor')){
            return $model->newQuery()->with("user")->with("doctor")
                ->join("doctors", "doctors.id", "=", "doctor_reviews.doctor_id")
                ->where('doctors.user_id', auth()->id())
                ->groupBy('doctor_reviews.id')
                ->select('doctor_reviews.*');
        }
        else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()->where('doctor_reviews.user_id', auth()->id())
                ->select('doctor_reviews.*');
        } else {
            return $model->newQuery()->with("user")->with("doctor")->select("$model->table.*");
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
        return 'doctor_reviewsdatatable_' . time();
    }
}
