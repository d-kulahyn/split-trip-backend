<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Domain\Entity\Debt;
use App\Domain\Entity\Group;
use App\Domain\Entity\Payer;
use App\Domain\Entity\Debtor;
use App\Domain\Entity\Expense;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\DataProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    #[DataProvider('debtSimplificationProvider')]
    public function test_debts_distribution(array $debts, array $expectedResult): void
    {
        $expenses = [];

        foreach ($debts as $debt) {
            $expenses[] = new Expense(
                'other',
                now()->timestamp,
                'USD',
                'other',
                null,
                [$debt],
                [],
                [new Debtor($debt->from, $debt->amount)],
                [new Payer($debt->to, $debt->amount, 'USD')]
            );
        }

        $group = new Group(
            'test',
            'other',
            1,
            'USD',
            true,
            null,
            [],
            $expenses
        );

        $result = $group->distributeDebts();

        $this->assertEqualsCanonicalizing($expectedResult, $result);
    }

    public static function debtSimplificationProvider(): array
    {
        return [
            'no transitive relations'     => [
                [
                    new Debt(100, 'USD', 1, 2),
                ],
                [
                    new Debt(100, 'USD', 1, 2),
                ],
            ],
            'simple transitive relation'  => [
                [
                    new Debt(50, 'USD', 1, 2),
                    new Debt(50, 'USD', 2, 3),
                ],
                [
                    new Debt(50, 'USD', 1, 3),
                ],
            ],
            'complex transitive relation' => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(50, 'USD', 2, 3),
                    new Debt(50, 'USD', 3, 4),
                ],
                [
                    new Debt(50, 'USD', 1, 2),
                    new Debt(50, 'USD', 1, 4),
                ],
            ],
            'circular debt case'          => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(100, 'USD', 2, 3),
                    new Debt(120, 'USD', 3, 1),
                ],
                [
                    new Debt(20, 'USD', 3, 1),
                ],
            ],
            'equal circular debt case'    => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(100, 'USD', 2, 3),
                    new Debt(100, 'USD', 3, 1),
                ],
                [],
            ],
            'several transitive relations'                        => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(100, 'USD', 2, 3),
                    new Debt(100, 'USD', 1, 3),
                    new Debt(100, 'USD', 3, 4),
                    new Debt(100, 'USD', 2, 5),
                ],
                [
                    new Debt(100, 'USD', 1, 3),
                    new Debt(100, 'USD', 1, 4),
                    new Debt(100, 'USD', 2, 5),
                ],
            ],
            'single debtor multiple creditors' => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(50, 'USD', 1, 3),
                ],
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(50, 'USD', 1, 3),
                ],
            ],
            'multiple debtors single creditor' => [
                [
                    new Debt(100, 'USD', 2, 1),
                    new Debt(50, 'USD', 3, 1),
                ],
                [
                    new Debt(100, 'USD', 2, 1),
                    new Debt(50, 'USD', 3, 1),
                ],
            ],
            'complex mixed debts' => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(200, 'USD', 2, 3),
                    new Debt(50, 'USD', 1, 3),
                    new Debt(150, 'USD', 3, 4),
                ],
                [
                    new Debt(150, 'USD', 1, 4),
                    new Debt(100, 'USD', 2, 3),
                ],
            ],
            'participant cancels out' => [
                [
                    new Debt(100, 'USD', 1, 2),
                    new Debt(100, 'USD', 2, 3),
                    new Debt(100, 'USD', 3, 4),
                    new Debt(100, 'USD', 4, 1),
                ],
                [],
            ],
        ];
    }
}
