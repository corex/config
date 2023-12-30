<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Data;

use CoRex\Config\Data\TrueFalse;
use PHPUnit\Framework\TestCase;

/**
 * @covers \CoRex\Config\Data\TrueFalse
 */
class TrueFalseTest extends TestCase
{
    public function testTrueValues(): void
    {
        $this->assertSame(
            ['true', true, 'yes', 'on', 1, '1'],
            TrueFalse::VALUES_TRUE
        );
    }

    public function testFalseValues(): void
    {
        $this->assertSame(
            ['false', false, 'no', 'off', 0, '0'],
            TrueFalse::VALUES_FALSE
        );
    }

    public function testGetTranslatedTrueFalse(): void
    {
        foreach (TrueFalse::VALUES_TRUE as $value) {
            $this->assertTrue(TrueFalse::getTranslatedTrueFalse($value));
        }

        foreach (TrueFalse::VALUES_FALSE as $value) {
            $this->assertFalse(TrueFalse::getTranslatedTrueFalse($value));
        }

        $this->assertNull(TrueFalse::getTranslatedTrueFalse('anything-else'));
    }

    public function testGetReadableTrueFalseValues(): void
    {
        $values = array_merge(TrueFalse::VALUES_TRUE, TrueFalse::VALUES_FALSE);
        $readableValues = TrueFalse::getReadableTrueFalseValues();
        $this->assertCount(count($values), $readableValues);

        foreach ($values as $value) {
            $this->assertContains(
                $this->getReadableValue($value),
                $readableValues
            );
        }
    }

    private function getReadableValue(int|bool|string $value): string
    {
        if (is_string($value)) {
            return "'" . $value . "'";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string)$value;
    }
}