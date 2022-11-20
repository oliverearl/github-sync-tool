<?php

declare(strict_types=1);

namespace App\Objects;

use InvalidArgumentException;

final class ContributionSquare
{
    /**
     * Date of the GitHub contributions.
     *
     * @var string
     */
    private string $date;

    /**
     * The number of contributions committed on a given day.
     *
     * @var int
     */
    private int $count;

    /**
     * Build a new contribution square object.
     *
     * @param string $date
     * @param int $count
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(string $date, int $count)
    {
        if (strtotime($date) === false) {
            throw new InvalidArgumentException('Date is invalid!');
        }

        $this->date = $date;
        $this->count = $count;
    }

    /**
     * Returns the date.
     *
     * @return string
     */
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * Returns the count.
     *
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Return the contribution square as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'date' => $this->date,
            'count' => $this->count,
        ];
    }
}
