<?php

declare(strict_types=1);

namespace Yiisoft\Aliases\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Aliases\Aliases;

final class AliasesTest extends TestCase
{
    public function dataBase(): array
    {
        return [
            ['', ['@url' => ''], '@url'],
            ['/', ['@url' => ''], '@url/'],
            ['/about', ['@url' => ''], '@url/about'],
            ['/', ['@url' => '/'], '@url'],
            ['/', ['@url' => '/'], '@url/'],
            ['/about', ['@url' => '/'], '@url/about'],
            ['test', ['@url' => 'test'], '@url'],
            ['test/', ['@url' => 'test'], '@url/'],
            ['test/about', ['@url' => 'test'], '@url/about'],
            ['test/', ['@url' => 'test/'], '@url'],
            ['test/', ['@url' => 'test/'], '@url/'],
            ['test/about', ['@url' => 'test/'], '@url/about'],
            ['/test', ['@url' => '/test'], '@url'],
            ['/test/', ['@url' => '/test'], '@url/'],
            ['/test/about', ['@url' => '/test'], '@url/about'],
            ['/test/', ['@url' => '/test/'], '@url'],
            ['/test/', ['@url' => '/test/'], '@url/'],
            ['/test/about', ['@url' => '/test/'], '@url/about'],
        ];
    }

    /**
     * @dataProvider dataBase
     */
    public function testBase(string $expectedPath, array $config, string $aliasPath): void
    {
        $aliases = new Aliases($config);

        $this->assertSame($expectedPath, $aliases->get($aliasPath));
    }

    public function testNonAlias(): void
    {
        $aliases = new Aliases();

        $aliasNotBeginsWithAt = 'alias not begins with @';
        $this->assertSame($aliasNotBeginsWithAt, $aliases->get($aliasNotBeginsWithAt));
    }

    public function testDirect(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii', '/yii/framework');
        $this->assertSame('/yii/framework', $aliases->get('@yii'));
    }

    public function testDerived(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii', '/yii/framework');
        $this->assertSame('/yii/framework/test/file', $aliases->get('@yii/test/file'));
    }

    public function testComposite(): void
    {
        $aliases = new Aliases();

        $aliases->set('@some/alias', '/www');
        $this->assertSame('/www', $aliases->get('@some/alias'));
    }

    public function testSpecificAliasWhenGeneralIsDefined(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
            '@yii/gii' => '/yii/gii',
            '@yii/gii/assets' => '/yii/gii_assets',
        ]);

        $this->assertSame('/yii/framework', $aliases->get('@yii'));
        $this->assertSame('/yii/gii/test', $aliases->get('@yii/gii/test'));
        $this->assertSame('/yii/framework/giitest', $aliases->get('@yii/giitest'));
        $this->assertSame('/yii/gii_assets', $aliases->get('@yii/gii/assets'));
    }

    public function testNonExisting(): void
    {
        $aliases = new Aliases();

        $erroneousAlias = '@alias_not_exists';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Invalid path alias: %s', $erroneousAlias));
        $aliases->get($erroneousAlias);
    }

    public function testSetTrimsTrailingSlashes(): void
    {
        $aliases = new Aliases([
            '@forward' => 'forward//',
            '@backward' => 'backward\\\\',
        ]);

        $this->assertSame('forward/test', $aliases->get('@forward/test'));
        $this->assertSame('backward/test', $aliases->get('@backward/test'));
    }

    public function testSetOverridesExistingValue(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
        ]);

        $aliases->set('@yii', '/yii');
        $this->assertSame('/yii', $aliases->get('@yii'));
    }

    public function testSetWithoutAt(): void
    {
        $aliases = new ALiases();
        $aliases->set('yii', '/yii');

        $path = $aliases->get('@yii/hello');

        $this->assertSame('/yii/hello', $path);
    }

    public function testRemovesAlias(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
        ]);

        $aliases->remove('yii');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path alias: @yii');
        $aliases->get('@yii');
    }

    public function testRemovesSubAlias(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
            '@yii/gii' => '/yii/gii',
        ]);

        $aliases->remove('@yii/gii');

        $this->assertSame('/yii/framework', $aliases->get('@yii'));
        $this->assertSame('/yii/framework/gii', $aliases->get('@yii/gii'));
    }

    public function testConstructConfig(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii',
            '@gii' => '@yii/gii',
        ]);

        $this->assertSame('/yii', $aliases->get('@yii'));
        $this->assertSame('/yii/gii', $aliases->get('@gii'));
    }

    public function testOrderShouldNotMatter(): void
    {
        $aliases = new Aliases([
            '@gii' => '@yii/gii',
            '@yii' => '/yii',
        ]);

        $this->assertSame('/yii/gii', $aliases->get('@gii'));
    }

    public function testGetAll(): void
    {
        $aliases = new Aliases([
            '@zii' => '@gii/zii',
            '@gii' => '@yii/gii',
            '@yii' => '/yii',
        ]);

        $expected = [
            '@zii' => '/yii/gii/zii',
            '@gii' => '/yii/gii',
            '@yii' => '/yii',
        ];
        $this->assertEquals($expected, $aliases->getAll());

        $expected = [
            '@yii' => '/yii/framework',
            '@yii/gii/assets' => '/yii/gii_assets',
            '@yii/gii' => '/yii/gii',
        ];

        $aliases = new Aliases($expected);

        $this->assertEquals($expected, $aliases->getAll());
    }

    public function dataGetArray(): array
    {
        return [
            'empty' => [[], []],
            'not-empty' => [['/a/hello', '/b/world', '/c/test'], ['@a/hello', '@b/world', '/c/test']],
        ];
    }

    /**
     * @dataProvider dataGetArray
     */
    public function testGetArray(array $expected, array $aliases): void
    {
        $service = new Aliases([
            '@a' => '/a',
            '@b' => '/b',
        ]);

        $this->assertSame($expected, $service->getArray($aliases));
    }
}
