<?php

namespace Yiisoft\Aliases\Tests;

use PHPUnit\Framework\TestCase;
use Yiisoft\Aliases\Aliases;

final class AliasesTest extends TestCase
{
    public function testGet(): void
    {
        $aliases = new Aliases();

        $aliasNotBeginsWithAt = 'alias not begins with @';
        $this->assertEquals($aliasNotBeginsWithAt, $aliases->get($aliasNotBeginsWithAt));

        $aliases->set('@yii', '/yii/framework');
        $this->assertEquals('/yii/framework', $aliases->get('@yii'));
        $this->assertEquals('/yii/framework/test/file', $aliases->get('@yii/test/file'));
        $aliases->set('yii/gii', '/yii/gii');
        $this->assertEquals('/yii/framework', $aliases->get('@yii'));
        $this->assertEquals('/yii/framework/test/file', $aliases->get('@yii/test/file'));
        $this->assertEquals('/yii/gii', $aliases->get('@yii/gii'));
        $this->assertEquals('/yii/gii/file', $aliases->get('@yii/gii/file'));

        $aliases->set('@tii', '@yii/test');
        $this->assertEquals('/yii/framework/test', $aliases->get('@tii'));

        $aliases->set('@yii', null);
        $this->assertEquals('/yii/gii/file', $aliases->get('@yii/gii/file'));

        $aliases->set('@some/alias', '/www');
        $this->assertEquals('/www', $aliases->get('@some/alias'));

        $erroneousAlias = '@alias_not_exists';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Invalid path alias: %s', $erroneousAlias));
        $aliases->get($erroneousAlias);
    }

    public function testGetRoot(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii', '/yii/framework');
        $this->assertEquals('@yii', $aliases->getRoot('@yii'));
        $this->assertEquals('@yii', $aliases->getRoot('@yii/test/file'));
        $aliases->set('@yii/gii', '/yii/gii');
        $this->assertEquals('@yii/gii', $aliases->getRoot('@yii/gii'));
    }

    public function testSet(): void
    {
        $aliases = new Aliases();

        $aliases->set('@yii/gii', '/yii/gii');
        $this->assertEquals('/yii/gii', $aliases->get('@yii/gii'));
        $aliases->set('@yii/tii', '/yii/tii');
        $this->assertEquals('/yii/tii', $aliases->get('@yii/tii'));
    }

    public function testConstructConfig(): void
    {
        $aliases = new Aliases([
            '@yii' => '/yii',
            '@gii' => '@yii/gii',
        ]);

        $this->assertEquals('/yii', $aliases->get('@yii'));
        $this->assertEquals('/yii/gii', $aliases->get('@gii'));
        $this->assertEquals('@yii', $aliases->getRoot('@yii/tii'));

        $aliases->set('@tii', '@gii/tii');
        $this->assertEquals('/yii/gii/tii', $aliases->get('@tii'));

        $erroneousAlias = '@alias_not_exists';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Invalid path alias: %s', $erroneousAlias));
        $aliases->get($erroneousAlias);
    }
}
