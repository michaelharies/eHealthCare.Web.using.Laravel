<?php

namespace App\DataTables;

use App\Models\Patient;
use App\Models\CustomField;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;
use Barryvdh\DomPDF\Facade as PDF;

class PatientDataTable extends DataTable
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
            ->editColumn('image', function ($patient) {
                return getMediaColumn($patient, 'image');
            })
            ->editColumn('updated_at', function ($patient) {
                return getDateColumn($patient, 'updated_at');
            })
            ->addColumn('action', 'patients.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));

        return $dataTable;
    }

    /**
     * Get query source of dataTable.
     *
     * @param Patient $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Patient $model): \Illuminate\Database\Eloquent\Builder
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->select("patients.*");
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()
                ->join("doctor_patients", "patient_id", "=", "patients.id")
                ->join("doctors", "doctors.id", "=", "doctor_patients.doctor_id")
                ->join("clinic_users", "clinic_users.clinic_id", "=", "doctors.clinic_id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy("patients.id")
                ->select("patients.*");
        } else if (auth()->user()->hasRole('doctor')) {
            return $model->newQuery()
                ->join("doctor_patients", "patient_id", "=", "patients.id")
                ->join("doctors", "doctors.id", "=", "doctor_patients.doctor_id")
                ->where('doctors.user_id', auth()->id())
                ->groupBy("patients.id")
                ->select("patients.*");
        }
        else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()
                ->join("doctor_patients", "patient_id", "=", "patients.id")
                ->where('patients.user_id', auth()->id())
                ->groupBy("patients.id")
                ->select("patients.*");
        }
        else {
            return $model->newQuery()->select("patients.*");
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
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        $columns = [
            [
                'data' => 'image',
                'title' => trans('lang.patient_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'first_name',
                'title' => trans('lang.patient_first_name'),

            ],
            [
                'data' => 'last_name',
                'title' => trans('lang.patient_last_name'),

            ],
            [
                'data' => 'phone_number',
                'title' => trans('lang.patient_phone_number'),

            ],
            [
                'data' => 'mobile_number',
                'title' => trans('lang.patient_mobile_number'),

            ],
            [
                'data' => 'age',
                'title' => trans('lang.patient_age'),

            ],
            [
                'data' => 'gender',
                'title' => trans('lang.patient_gender'),

            ],
            [
                'data' => 'weight',
                'title' => trans('lang.patient_weight'),

            ],
            [
                'data' => 'height',
                'title' => trans('lang.patient_height'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.patient_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Patient::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Patient::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.patient_' . $field->name),
                    'orderable' => false,
                    'searchable' => false,
                ]]);
            }
        }
        return $columns;
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'patientsdatatable_' . time();
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
}
