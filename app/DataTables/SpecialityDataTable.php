<?php
/*
 * File name: SpecialityDataTable.php
 * Last modified: 2021.04.12 at 09:17:55
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\Speciality;
use App\Models\CustomField;
use App\Models\Post;
use Barryvdh\DomPDF\Facade as PDF;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class SpecialityDataTable extends DataTable
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
            ->editColumn('image', function ($speciality) {
                return getMediaColumn($speciality, 'image');
            })
            ->editColumn('description', function ($speciality) {
                return getStripedHtmlColumn($speciality, 'description');
            })
            ->editColumn('name', function ($speciality) {
                return $speciality->name;
            })
            ->editColumn('color', function ($speciality) {
                return getColorColumn($speciality, 'color');
            })
            ->editColumn('featured', function ($speciality) {
                return getBooleanColumn($speciality, 'featured');
            })
            ->editColumn('parent_speciality.name', function ($speciality) {
                return getLinksColumnByRouteName([$speciality->parentSpeciality], 'specialities.edit', 'id', 'name');
            })
            ->editColumn('updated_at', function ($speciality) {
                return getDateColumn($speciality, 'updated_at');
            })
            ->addColumn('action', 'specialities.datatables_actions')
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
                'title' => trans('lang.speciality_image'),
                'searchable' => false, 'orderable' => false, 'exportable' => false, 'printable' => false,
            ],
            [
                'data' => 'name',
                'title' => trans('lang.speciality_name'),

            ],
            [
                'data' => 'color',
                'title' => trans('lang.speciality_color'),

            ],
            [
                'data' => 'description',
                'title' => trans('lang.speciality_description'),

            ],
            [
                'data' => 'featured',
                'title' => trans('lang.speciality_featured'),
            ],
            [
                'data' => 'order',
                'title' => trans('lang.speciality_order'),
            ],
            [
                'data' => 'parent_speciality.name',
                'name' => 'parentSpeciality.name',
                'title' => trans('lang.speciality_parent_id'),
                'searchable' => false, 'orderable' => false,
            ],
            [
                'data' => 'updated_at',
                'title' => trans('lang.speciality_updated_at'),
                'searchable' => false,
            ]
        ];

        $hasCustomField = in_array(Speciality::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Speciality::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.speciality_' . $field->name),
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
     * @param Speciality $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Speciality $model)
    {
        return $model->newQuery()->with("parentSpeciality")->select("specialities.*");
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
        return 'specialitiesdatatable_' . time();
    }
}
