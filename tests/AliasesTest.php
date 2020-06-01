<?php

namespace Yiisoft\Aliases\Tests;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Yiisoft\Aliases\Aliases;

final class AliasesTest extends TestCase
{
    public function testNonAlias(): void
    {
        $aliases = new Aliases();

        $aliasNotBeginsWithAt = 'alias not begins with @';
        $this->assertEquals($aliasNotBeginsWithAt, $aliases->get($aliasNotBeginsWithAt));
    }

    public function testDirect(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii', '/yii/framework');
        $this->assertEquals('/yii/framework', $aliases->get('@yii'));
    }

    public function testDerived(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii', '/yii/framework');
        $this->assertEquals('/yii/framework/test/file', $aliases->get('@yii/test/file'));
    }

    public function testComposite(): void
    {
        $aliases = new Aliases();

        $aliases->set('@some/alias', '/www');
        $this->assertEquals('/www', $aliases->get('@some/alias'));
    }

    public function testSpecificAliasWhenGeneralIsDefined(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
            '@yii/gii' => '/yii/gii',
            '@yii/gii/assets' => '/yii/gii_assets'
        ]);

        $this->assertEquals('/yii/framework', $aliases->get('@yii'));
        $this->assertEquals('/yii/gii/test', $aliases->get('@yii/gii/test'));
        $this->assertEquals('/yii/framework/giitest', $aliases->get('@yii/giitest'));
        $this->assertEquals('/yii/gii_assets', $aliases->get('@yii/gii/assets'));
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

        $this->assertEquals('forward', $aliases->get('@forward'));
        $this->assertEquals('backward', $aliases->get('@backward'));
    }

    public function testSetOverridesExistingValue(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
        ]);

        $aliases->set('@yii', '/yii');
        $this->assertEquals('/yii', $aliases->get('@yii'));
    }

    public function testSetToNullRemovesAlias(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
        ]);

        $aliases->set('@yii', null);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid path alias: @yii');
        $aliases->get('@yii');
    }

    public function testSetToNullRemovesSubAlias(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
            '@yii/gii' => '/yii/gii',
        ]);

        $aliases->set('@yii/gii', null);

        $this->assertEquals('/yii/framework', $aliases->get('@yii'));
        $this->assertEquals('/yii/framework/gii', $aliases->get('@yii/gii'));
    }

    public function testGetRoot(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii/framework',
            '@yii/gii' => '/yii/gii',
            '@simple' => 'simple',
        ]);

        $this->assertEquals('@yii', $aliases->getRoot('@yii'));
        $this->assertEquals('@yii', $aliases->getRoot('@yii/test/file'));
        $this->assertEquals('@yii/gii', $aliases->getRoot('@yii/gii'));
        $this->assertEquals('@simple', $aliases->getRoot('@simple/test'));
    }

    public function testConstructConfig(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii',
            '@gii' => '@yii/gii',
        ]);

        $this->assertEquals('/yii', $aliases->get('@yii'));
        $this->assertEquals('/yii/gii', $aliases->get('@gii'));
    }

    public function testOrderShouldNotMatter(): void
    {
        $aliases = new Aliases([
            '@gii' => '@yii/gii',
            '@yii' => '/yii',
        ]);

        $this->assertEquals('/yii/gii', $aliases->get('@gii'));
    }

    public function testMagicSetter(): void
    {
        $aliases = new Aliases();
        $aliases->yii = '/yii';

        $this->assertEquals('/yii', $aliases->get('@yii'));
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
    }
}
