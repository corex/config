<?php

declare(strict_types=1);

namespace Tests\CoRex\Config\Unit\Section;

use CoRex\Config\ConfigInterface;
use CoRex\Config\Section\Section;
use CoRex\Config\Section\SectionInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class SectionTest extends TestCase
{
    /** @var ConfigInterface&MockObject */
    private ConfigInterface $config;

    private SectionInterface $section;

    public function testGetSection(): void
    {
        $this->assertSame('test', $this->section->getSection());
    }

    public function testHas(): void
    {
        $this->config->expects($this->once())
            ->method('has')
            ->with('test.key')
            ->willReturn(true);

        $this->assertTrue($this->section->has('key'));
    }

    public function testGetMixed(): void
    {
        $this->config->expects($this->once())
            ->method('getMixed')
            ->with('test.key')
            ->willReturn('a.value');

        $this->assertSame('a.value', $this->section->getMixed('key'));
    }

    public function testGetStringOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getStringOrNull')
            ->with('test.key')
            ->willReturn('a.value');

        $this->assertSame('a.value', $this->section->getStringOrNull('key'));
    }

    public function testGetString(): void
    {
        $this->config->expects($this->once())
            ->method('getString')
            ->with('test.key')
            ->willReturn('a.value');

        $this->assertSame('a.value', $this->section->getString('key'));
    }

    public function testGetIntOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getIntOrNull')
            ->with('test.key')
            ->willReturn(7);

        $this->assertSame(7, $this->section->getIntOrNull('key'));
    }

    public function testGetInt(): void
    {
        $this->config->expects($this->once())
            ->method('getInt')
            ->with('test.key')
            ->willReturn(7);

        $this->assertSame(7, $this->section->getInt('key'));
    }

    public function testGetBoolOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getBoolOrNull')
            ->with('test.key')
            ->willReturn(true);

        $this->assertTrue($this->section->getBoolOrNull('key'));
    }

    public function testGetBool(): void
    {
        $this->config->expects($this->once())
            ->method('getBool')
            ->with('test.key')
            ->willReturn(true);

        $this->assertTrue($this->section->getBool('key'));
    }

    public function testGetTranslatedBoolOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getTranslatedBoolOrNull')
            ->with('test.key')
            ->willReturn(true);

        $this->assertTrue($this->section->getTranslatedBoolOrNull('key'));
    }

    public function testGetTranslatedBool(): void
    {
        $this->config->expects($this->once())
            ->method('getTranslatedBool')
            ->with('test.key')
            ->willReturn(true);

        $this->assertTrue($this->section->getTranslatedBool('key'));
    }

    public function testGetDoubleOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getDoubleOrNull')
            ->with('test.key')
            ->willReturn(10.4);

        $this->assertSame(10.4, $this->section->getDoubleOrNull('key'));
    }

    public function testGetDouble(): void
    {
        $this->config->expects($this->once())
            ->method('getDouble')
            ->with('test.key')
            ->willReturn(10.4);

        $this->assertSame(10.4, $this->section->getDouble('key'));
    }

    public function testGetArrayOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getArrayOrNull')
            ->with('test.key')
            ->willReturn(['value']);

        $this->assertSame(['value'], $this->section->getArrayOrNull('key'));
    }

    public function testGetArray(): void
    {
        $this->config->expects($this->once())
            ->method('getArray')
            ->with('test.key')
            ->willReturn(['value']);

        $this->assertSame(['value'], $this->section->getArray('key'));
    }

    public function testGetListOrNull(): void
    {
        $this->config->expects($this->once())
            ->method('getListOrNull')
            ->with('test.key')
            ->willReturn(['value']);

        $this->assertSame(['value'], $this->section->getListOrNull('key'));
    }

    public function testGetList(): void
    {
        $this->config->expects($this->once())
            ->method('getList')
            ->with('test.key')
            ->willReturn(['value']);

        $this->assertSame(['value'], $this->section->getList('key'));
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        /** @var ConfigInterface&MockObject $config */
        $config = $this->createMock(ConfigInterface::class);

        $this->config = $config;

        $this->section = new Section($config, 'test');
    }
}