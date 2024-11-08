<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class MediaUploadingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $allowedRoutes = [
            str($this->input('model_type'))->snake()->toString() . '_create',
            str($this->input('model_type'))->snake()->toString() . '_edit',
        ];

        if (Gate::any($allowedRoutes)) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $allowedMimeTypes = [
            'text/csv',
            'text/plain',
            'image/heic',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/bmp',
            'image/x-icon',
            'image/tiff',
            'image/webp',
            'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];

        return [
            'file' => [
                'required',
                'mimetypes:' . implode(',', $allowedMimeTypes),
                'max:' . (1024 * ($this->input('size') ?? 10)),
            ],
            'model_type' => [
                'required',
                'in:User',
            ],
        ];
    }
}
