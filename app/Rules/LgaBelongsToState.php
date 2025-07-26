<?php

namespace App\Rules;

use Closure;
use App\Models\Lga;
use Illuminate\Contracts\Validation\ValidationRule;

class LgaBelongsToState implements ValidationRule
{
    protected int $stateId;

    public function __construct(int $stateId)
    {
        $this->stateId = $stateId;
    }

    public function validate(string $attribute, mixed $value, \Closure $fail): void
    {
        $match = Lga::where('lga_id', $value)
            ->where('state_id', $this->stateId)
            ->exists();

        if (! $match) {
            $fail('The selected local government does not belong to the selected state.');
        }
    }
}
