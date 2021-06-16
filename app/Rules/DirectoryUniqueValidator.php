<?php

namespace App\Rules;

use App\Directory;
use Illuminate\Contracts\Validation\Rule;


class DirectoryUniqueValidator implements Rule
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

    public function passes($attribute, $value){
        $directory = Directory::find($this->requestData['directory_id']);
        $same_level_dirs = Directory::where('parent_id','=',$directory->parent_id )->where('id','!=',$this->requestData['directory_id'] )->where('name','=',$this->requestData['directory'] )->get();
        if(count($same_level_dirs)>0){
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
