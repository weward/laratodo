<?php

namespace App\Http\Requests;

use App\Rules\DueDateRule;
use Illuminate\Foundation\Http\FormRequest;

class TaskRequest extends FormRequest
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
    public function rules()
    {
        return [
            'title' => 'required',
            'description' => 'required',
            'due_date' => ['nullable', 'date_format:Y-m-d', new DueDateRule],
            'priority_id' => [
                'nullable',
                'numeric',
                fn($att, $val, $fail) => ($val < 1 || $val > 4) ? $fail("{$att} is invalid.") : true
            ],
            'new_attachments.*' => 'nullable|file|mimes:svg,png,jpg,mp4,csv,txt,doc,docx|max:20480',
        ];
    }
}
