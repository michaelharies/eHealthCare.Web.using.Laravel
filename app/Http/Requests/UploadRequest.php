<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Nwidart\Modules\Facades\Module;

class UploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
//        if (Module::isActivated('ClinicDocuments')) {
        return [
            'file' => 'mimes:jpeg,png,jpg,gif,svg,pdf,doc,docx,xls,xlsx,ppt,pptx',
        ];
//        } else {
//            return [
//                'file' => 'image|mimes:jpeg,png,jpg,gif,svg',
//            ];
//        }
    }
}
