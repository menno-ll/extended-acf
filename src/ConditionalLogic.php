<?php

/**
 * Copyright (c) Vincent Klaiber.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @see https://github.com/vinkla/extended-acf
 */

declare(strict_types=1);

namespace Extended\ACF;

use InvalidArgumentException;

class ConditionalLogic
{
    public function __construct(
        protected string $name,
        protected string $operator,
        protected mixed $value = null
    ) {
        //
    }

    /**
     * @param string $operator `==` is equal to, `!=` is not equal to, `>` is greater than, `<` is less than, `==pattern` matches pattern, `==contains` contains value, `==empty` has no value, `!=empty` has any value
     * @throws \InvalidArgumentException
     */
    public static function where(string $name, string $operator, mixed $value = null): static
    {
        $allowedOperators = [
            '>',
            '<',
            '==',
            '!=',
            '==pattern',
            '==contains',
            '==empty',
            '!=empty',
        ];

        if (in_array($operator, $allowedOperators) === false) {
            throw new InvalidArgumentException("Invalid conditional logic operator [$operator].");
        }

        return new self($name, $operator, $value);
    }

    /** @internal */
    public function get(string|null $parentKey = null): array
    {
        $parentKey = Key::resolveParentKey($parentKey, Key::sanitize($this->name));

        $key = $parentKey . '_' . Key::sanitize($this->name);

        $rule = [
            'field' => 'field_' . Key::hash($key),
            'operator' => $this->operator,
        ];

        if ($this->value) {
            $rule['value'] = $this->value;
        }

        return $rule;
    }
}
