<?php

namespace App\Infrastrstructure\API\Rules;

use App\Infrastrstructure\API\Enum\ConfirmationEmailCodeRuleEnum;
use Illuminate\Contracts\Validation\Rule;
use App\Infrastrstructure\Service\Interface\SecurityCodeStorageInterface;

class ConfirmationEmailCodeRule implements Rule
{
    /**
     * @var int
     */
    protected int $statusCode;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        protected SecurityCodeStorageInterface $securityCodeStorage
    ) {}

    /**
     * @param $attribute
     * @param $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $code = $this->securityCodeStorage->extract(request()->user()->id);
        if (is_null($code)) {
            $this->statusCode = ConfirmationEmailCodeRuleEnum::IS_EXPIRED->value;

            return false;
        }

        if ($code !== strval($value)) {
            $this->statusCode = ConfirmationEmailCodeRuleEnum::IS_WRONG->value;

            return false;
        }

        $this->securityCodeStorage->forget(request()->user()->id);

        return true;
    }

    /**
     * @return string
     */
    public function message(): string
    {
        return $this->messages()[$this->statusCode];
    }

    /**
     * @return string[]
     */
    public function messages(): array
    {
        return [
            ConfirmationEmailCodeRuleEnum::IS_EXPIRED->value => 'Code has been expired.',
            ConfirmationEmailCodeRuleEnum::IS_WRONG->value   => 'Invalid code.'
        ];
    }
}
