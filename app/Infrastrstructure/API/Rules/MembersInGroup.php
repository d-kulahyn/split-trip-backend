<?php

namespace App\Infrastrstructure\API\Rules;

use Closure;
use App\Domain\Entity\Group;
use Illuminate\Contracts\Validation\ValidationRule;

readonly class MembersInGroup implements ValidationRule
{
    /**
     * @param Group $group
     */
    public function __construct(protected Group $group) {}

    /**
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $memberIds = array_column($value, 'id');

        $groupMemberIds = $this->group->getMemberIds();

        $diff = array_diff($memberIds, $groupMemberIds);

        if (count($diff)) {
            $fail('Some of the :attribute do not belong to the selected group.');
        }
    }
}
