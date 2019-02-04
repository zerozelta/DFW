<?php

namespace Illuminate\Contracts\Validation;

interface ValidatesWhenResolved
{
    /**
     * Validate the given classes instance.
     *
     * @return void
     */
    public function validateResolved();
}
