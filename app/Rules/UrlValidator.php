<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class UrlValidator implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $resureveKeys=['create','update','delete','edit'];
        if (!preg_match("/^([a-zA-Z0-9-]){3,}$/",$value) || in_array(strtolower($value),$resureveKeys))
        {
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
        return trans('error.invalid_url');
    }
}
