<?php
/*
 * File name: AppointmentDataTable.php
 * Last modified: 2021.06.10 at 20:38:02
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2021
 */

namespace App\DataTables;

use App\Models\Appointment;
use App\Models\CustomField;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTableAbstract;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder;
use Yajra\DataTables\Services\DataTable;

class AppointmentDataTable extends DataTable
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
            ->editColumn('id', function ($appointment) {
                return "#" . $appointment->id;
            })
            ->editColumn('appointment_at', function ($appointment) {
                return getDateColumn($appointment, 'appointment_at');
            })
            ->editColumn('patient.first_name', function ($appointment) {
                return getLinksColumnByRouteName([$appointment->patient],'patients.edit', 'id', 'first_name');
            })
            ->editColumn('patient.last_name', function ($appointment) {
                return getLinksColumnByRouteName([$appointment->patient], 'patients.edit', 'id', 'last_name');
            })
            ->editColumn('doctor.name', function ($appointment) {
                return getLinksColumnByRouteName([$appointment->doctor], 'doctors.edit', 'id', 'name');
            })
            ->editColumn('clinic.name', function ($appointment) {
                return getLinksColumnByRouteName([$appointment->clinic], 'clinics.edit', 'id', 'name');
            })
            ->editColumn('total', function ($appointment) {
                return "<span class='text-bold text-success'>" . getPrice($appointment->getTotal()) . "</span>";
            })
            ->editColumn('address', function ($appointment) {
                return $appointment->address->address;
            })
            ->editColumn('taxes', function ($appointment) {
                return "<span class='text-bold'>" . getPrice($appointment->getTaxesValue()) . "</span>";
            })
            ->editColumn('coupon', function ($appointment) {
                return $appointment->coupon->code . " <span class='text-bold'>(" . getPrice($appointment->getCouponValue()) . ")</span>";
            })
            ->editColumn('appointment_status.status', function ($appointment) {
                if (isset($appointment->appointmentStatus))
                    return "<span class='badge px-2 py-1 bg-" . setting('theme_color') . "'>" . $appointment->appointmentStatus->status . "</span>";
                else
                    return "";
            })
            ->editColumn('payment.payment_status.status', function ($appointment) {
                if (isset($appointment->payment)) {
                    return "<span class='badge px-2 py-1 bg-" . setting('theme_color') . "'>" . $appointment->payment->paymentStatus->status . "</span>";
                } else {
                    return '-';
                }
            })
            ->editColumn('online', function ($appointment) {
                return getBooleanColumn($appointment, 'online');
            })
            ->setRowClass(function ($appointment) {
                return $appointment->cancel ? 'appointment-cancel' : '';
            })
            ->addColumn('action', 'appointments.datatables_actions')
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
                'data' => 'id',
                'title' => trans('lang.appointment_id'),
            ],
            [
                'data' => 'doctor.name',
                'name' => 'doctor',
                'title' => trans('lang.appointment_doctor'),

            ],
            [
                'data' => 'clinic.name',
                'name' => 'clinic',
                'title' => trans('lang.appointment_clinic'),

            ],
            [
                'data' => 'patient.first_name',
                'title' => trans('lang.appointment_patient_first_name'),
            ],
            [
                'data' => 'patient.last_name',
                'title' => trans('lang.appointment_patient_last_name'),
            ],
            [
                'data' => 'address',
                'name' => 'address',
                'title' => trans('lang.appointment_address'),
            ],
            [
                'data' => 'appointment_status.status',
                'name' => 'appointmentStatus.status',
                'title' => trans('lang.appointment_appointment_status_id'),
            ],
            [
                'data' => 'payment.payment_status.status',
                'name' => 'payment.paymentStatus.status',
                'title' => trans('lang.payment_payment_status_id'),
            ],
            [
                'data' => 'taxes',
                'title' => trans('lang.appointment_taxes'),
                'searchable' => false,
                'orderable' => false,

            ],
            [
                'data' => 'coupon',
                'title' => trans('lang.appointment_coupon'),
                'searchable' => false,
                'orderable' => false,

            ],
            [
                'data' => 'online',
                'title' => trans('lang.appointment_online'),

            ],
            [
                'data' => 'total',
                'title' => trans('lang.appointment_total'),
                'searchable' => false,
                'orderable' => false,

            ],
            [
                'data' => 'appointment_at',
                'title' => trans('lang.appointment_appointment_at'),

            ],
        ];

        $hasCustomField = in_array(Appointment::class, setting('custom_field_models', []));
        if ($hasCustomField) {
            $customFieldsCollection = CustomField::where('custom_field_model', Appointment::class)->where('in_table', '=', true)->get();
            foreach ($customFieldsCollection as $key => $field) {
                array_splice($columns, $field->order - 1, 0, [[
                    'data' => 'custom_fields.' . $field->name . '.view',
                    'title' => trans('lang.appointment_' . $field->name),
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
     * @param Appointment $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Appointment $model)
    {
        if (auth()->user()->hasRole('admin')) {
            return $model->newQuery()->with("user")->with("appointmentStatus")->with("payment")->with("payment.paymentStatus")->select('appointments.*');
        } else if (auth()->user()->hasRole('clinic_owner')) {
            $clinicId = DB::raw("json_extract(clinic, '$.id')");
            return $model->newQuery()->with("user")->with("appointmentStatus")->with("payment")->with("payment.paymentStatus")->join("clinic_users", "clinic_users.clinic_id", "=", $clinicId)
                ->where('clinic_users.user_id', auth()->id())
                ->groupBy('appointments.id')
                ->select('appointments.*');

        }else if (auth()->user()->hasRole('doctor')) {
            $doctorId = DB::raw("json_extract(doctor, '$.id')");
            return $model->newQuery()->with("user")->with("appointmentStatus")->with("payment")->with("payment.paymentStatus")
                ->join("doctors", "doctors.id", "=", $doctorId)
                ->where('doctors.user_id', auth()->id())
                ->groupBy('appointments.id')
                ->select('appointments.*');

        }
        else if (auth()->user()->hasRole('customer')) {
            return $model->newQuery()->with("user")->with("appointmentStatus")->with("payment")->with("payment.paymentStatus")->where('appointments.user_id', auth()->id())
                ->select('appointments.*')
                ->groupBy('appointments.id');
        } else {
            return $model->newQuery()->with("user")->with("appointmentStatus")->with("payment")->with("payment.paymentStatus")->select('appointments.*');
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
        return 'appointmentsdatatable_' . time();
    }
}
