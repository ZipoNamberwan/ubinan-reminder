<?php

namespace App\Rules;

use App\Models\Bs;
use Illuminate\Contracts\Validation\Rule;

class AreaRule implements Rule
{
    private $nbs;
    private $village;
    private $subdistrict;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($subdistrict, $village, $nbs)
    {
        $this->subdistrict = $subdistrict;
        $this->village = $village;
        $this->nbs = $nbs;
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
        $code = $this->subdistrict . $this->village . $this->nbs;
        return in_array($code, Bs::all()->pluck('code')->toArray());
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The validation error message.';
    }
}
