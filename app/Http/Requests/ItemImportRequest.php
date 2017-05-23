<?php

namespace App\Http\Requests;

use App\Models\Import;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ItemImportRequest extends FormRequest
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
            //
        ];
    }

    public function import(Import $import)
    {
        $filename = config('app.private_uploads') . '/imports/' . $import->file_path;
        $class = title_case($this->input('import-type'));
        $classString = "App\\Importer\\{$class}Importer";
        $importer = new $classString($filename);
        $importer->setCallbacks([$this, 'log'], [$this, 'progress'], [$this, 'errorCallback'])
                 ->setUserId(Auth::id())
                 ->setUpdating($this->has('import-update'))
                 ->setUsernameFormat('firstname.lastname');

        $importer->import();
        return $this->errors;
    }

    public function log($string)
    {
        return; // FUTURE IMPLEMENTATION
    }

    public function progress($count)
    {
        // Open for future
        return;
    }
    public function errorCallback($item, $field, $errorString)
    {
        $this->errors[$item->name][$field] = $errorString;
        // $this->errors[$item->name] = $errorString;
    }

    private $errors;
}