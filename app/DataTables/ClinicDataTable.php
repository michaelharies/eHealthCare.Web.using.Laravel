<?php
/*
 * File name: ClinicDataTable.php
 * Last modified: 2021.11.24 at 19:18:10
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\DataTables;

use App\Models\CustomField;
use App\Models\Clinic;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class ClinicDataTable extends DataTable
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
            ->editColumn('image', function ($clinic) {
                return getMediaColumn($clinic, 'image');
            })
            ->editColumn('name', function ($clinic) {
                if ($clinic['featured']) {
                    return $clinic->name . "<span class='badge bg-" . setting('theme_color') . " p-1 m-2'>" . trans('lang.clinic_featured') . "</span>";
                }
                return $clinic->name;
            })->editColumn('address.address', function ($clinic) {
                return getLinksColumnByRouteName([$clinic->address], 'addresses.edit', 'id', 'address');
            })->editColumn('taxes', function ($clinic) {
                return getLinksColumnByRouteName($clinic->taxes, 'taxes.edit', 'id', 'name');
            })
            ->editColumn('users', function ($clinic) {
                return getLinksColumnByRouteName($clinic->users, 'users.edit', 'id', 'name');
            })
            ->editColumn('available', function ($clinic) {
                return getBooleanColumn($clinic, 'available');
            })
            ->editColumn('accepted', function ($clinic) {
                return getBooleanColumn($clinic, 'accepted');
            })
            ->editColumn('updated_at', function ($clinic) {
                return getDateColumn($clinic);
            })
            ->addColumn('action', 'clinics.datatables_actions')
            ->rawColumns(array_merge($columns, ['action']));
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
                'title' => trans('lang.clinic_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'name',
                'title' => trans('lang.clinic_name'),

            ],
            [
                'data' => 'user',
                'title' => trans('lang.clinic_users'),
                'searchable' => false,
            ],
            [
                'data' => 'phone_number',
                'title' => trans('lang.clinic_phone_number'),

            ],
            [
                'data' => 'mobile_number',
                'title' => trans('lang.clinic_mobile_number'),

            ],
            [
                'data' => 'address.address',
                'title' => trans('lang.clinic_address'),
                'searchable' => false,
                'orderable' => false
            ],
            [
                'data' => 'availability_range',
                'title' => trans('lang.clinic_availability_range'),

            ],
            [
                'data' => 'taxes',
                'title' => trans('lang.clinic_taxes'),
                'searchable' => false,
                'orderable' => false
            ],
            [
                'data' => 'available',
                'title' => trans('lang.clinic_available'),

            ], [
                'data' => 'accepted',
                'title' => trans('lang.clinic_accepted'),

            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.address_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Clinic::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Clinic::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.clinic_' . $field->name),
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
     * @param Clinic $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Clinic $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->select("clinics.*");
        } else if (auth()->user()->hasRole('clinic_owner')) {
            return $model->newQuery()
                ->join("clinic_users", "clinic_id", "=", "clinics.id")
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy("clinics.id")
                ->select("clinics.*");
        } else if (auth()->user()->hasRole('doctor')) {
            return $model->newQuery()
                ->join("doctors", "doctors.clinic_id", "=", "clinics.id")
                ->where('doctors.user_id', auth()->id())
                ->groupBy("clinics.id")
                ->select("clinics.*");
        }
        else {
            return $model->newQuery()->select("clinics.*");
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
                        ), true),
                    'fixedColumns' => [],
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
    protected function filename(): string
    {
        return 'clinicsdatatable_' . time();
    }
}
