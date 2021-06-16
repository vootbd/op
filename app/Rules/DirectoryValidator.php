<?php

namespace App\Rules;

use App\Directory;
use Illuminate\Contracts\Validation\Rule;


class DirectoryValidator implements Rule
{
    private $requestData;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($requestData)
    {
        $this->requestData = $requestData;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $parentId = 0;
        $directoryName = $this->requestData['directory'];
        if (isset($this->requestData['directory_id']) && !empty($this->requestData['directory_id'])) {
            $parentId = $this->requestData['directory_id'];
        }
        $directory = Directory::where('parent_id', $parentId)->where('name', $directoryName)->where('deleted_at', null)->get()->toArray();
        if (isset($directory) && !empty($directory)) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return trans('directory.dir_unique');
    }
}
