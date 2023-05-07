<?php
/*
 * File name: ClinicPayoutDataTable.php
 * Last modified: 2021.03.23 at 19:02:19
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\ClinicPayout;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class ClinicPayoutDataTable extends DataTable
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
            ->editColumn('clinic.name', function ($earning) {
                return getLinksColumnByRouteName([$earning->clinic], "clinics.edit", 'id', 'name');
            })
            ->editColumn('note', function ($clinicPayout) {
                return getStripedHtmlColumn($clinicPayout, 'note');
            })
            ->editColumn('paid_date', function ($clinicPayout) {
                return getDateColumn($clinicPayout, "paid_date");
            })
            ->editColumn('amount', function ($eproviders_payout) {
                return getPriceColumn($eproviders_payout, 'amount');
            })
            ->rawColumns(array_merge($columns));

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
                'data' => 'clinic.name',
                'name' => 'clinic.name',
                'title' => trans('lang.clinic_payout_clinic_id'),

            ],
            [
                'data' => 'method',
                'title' => trans('lang.clinic_payout_method'),

            ],
            [
                'data' => 'amount',
                'title' => trans('lang.clinic_payout_amount'),

            ],
            [
                'data' => 'paid_date',
                'name' => 'paidDate',
                'title' => trans('lang.clinic_payout_paid_date'),

            ],
            [
                'data' => 'note',
                'title' => trans('lang.clinic_payout_note'),

            ]
        ];

        $hasCustomField = in_array(ClinicPayout::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', ClinicPayout::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.clinic_payout_' . $field->name),
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
     * @param ClinicPayout $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(ClinicPayout $model)
    {
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
        return 'clinic_payoutsdatatable_' . time();
    }
}
