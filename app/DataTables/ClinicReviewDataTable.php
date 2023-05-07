<?php

namespace App\DataTables;

use App\Models\ClinicReview;
use App\Models\CustomField;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;
use Barryvdh\DomPDF\Facade as PDF;

class ClinicReviewDataTable extends DataTable
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
        return $dataTable
            ->editColumn('updated_at',function($clinicReview){
                return getDateColumn($clinicReview,'updated_at');
            })
            ->editColumn('user.name', function ($clinicReview) {
                return getLinksColumnByRouteName([$clinicReview->user], 'users.edit', 'id', 'name');
            })
            ->editColumn('clinic.name', function ($clinicReview) {
                return getLinksColumnByRouteName([$clinicReview->clinic], 'clinics.edit', 'id', 'name');
            })
            ->addColumn('action', 'clinic_reviews.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));
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
                'title' => trans('lang.clinic_review_review'),

            ],
            [
                'data' => 'rate',
                'title' => trans('lang.clinic_review_rate'),

            ],
            [
                'data' => 'user.name',
                'title' => trans('lang.clinic_review_user_id'),

            ],
            [
                'data' => 'clinic.name',
                'title' => trans('lang.clinic_review_clinic_id'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.clinic_review_updated_at'),
                'searchable'=>false,
            ]
        ];

        $hasCustomField = in_array(ClinicReview::class, setting('custom_field_models',[]));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', ClinicReview::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.clinic_review_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

//    /**
//     * Get query source of dataTable.
//     *
//     * @param ClinicReview $model
//     * @return \Illuminate\Database\Eloquent\Builder
//     */
//    public function query(ClinicReview $model)
//    {
//        return $model->newQuery()->with("")->with("user")->with("clinic");
//    }

    /**
     * Get query source of dataTable.
     *
     * @param ClinicReview $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ClinicReview $model): \Illuminate\Database\Eloquent\Builder
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("user")->with("clinic")->select("$model->table.*");
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()->with("user")->with("clinic")
                ->join("clinics", "clinics.id", "=", "clinic_reviews.clinic_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "clinics.id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy('clinic_reviews.id')
                ->select('clinic_reviews.*');
        }else if(auth()->user()->hasRole('doctor')){
            return $model->newQuery()->with("user")->with("clinic")
                ->join("clinics", "clinics.id", "=", "clinic_reviews.clinic_id")
                ->join("doctors", "doctors.clinic_id", "=", "clinics.id")
                ->where('doctors.user_id', auth()->id())
                ->groupBy('clinic_reviews.id')
                ->select('clinic_reviews.*');
        }

        else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()->where('clinic_reviews.user_id', auth()->id())
                ->select('clinic_reviews.*');
        } else {
            return $model->newQuery()->with("user")->with("clinic")->select("$model->table.*");
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
            ->addAction(['width' => '80px', 'printable' => false ,'responsivePriority'=>'100'])
            ->parameters(array_merge(
                config('datatables-buttons.parameters'), [
                    'language' => json_decode(
                        file_get_contents(base_path('resources/lang/'.app()->getLocale().'/datatable.json')
                        ),true)
                ]
            ));
    }



    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'clinic_reviewsdatatable_' . time();
    }

    /**
     * Export PDF using DOMPDF
     * @return mixed
     */
    public function pdf()
    {
        $data = $this->getDataForPrint();
        $pdf = PDF::loadView($this->printPreview, compact('data'));
        return $pdf->download($this->filename().'.pdf');
    }
}
