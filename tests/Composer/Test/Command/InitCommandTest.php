<?php

/*
 * This file is part of Composer.
 *
 * (c) Nils Adermann <naderman@naderman.de>
 *     Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Composer\Test\Command;

use Composer\Command\InitCommand;
use Composer\Test\TestCase;

class InitCommandTest extends TestCase
{
    public function testParseValidAuthorString()
    {
        $command = new InitCommand;
        $author = $command->parseAuthorString('John Smith <john@example.com>');
        $this->assertEquals('John Smith', $author['name']);
        $this->assertEquals('john@example.com', $author['email']);
    }

    public function testParseValidUtf8AuthorString()
    {
        $command = new InitCommand;
        $author = $command->parseAuthorString('Matti Meikäläinen <matti@example.com>');
        $this->assertEquals('Matti Meikäläinen', $author['name']);
        $this->assertEquals('matti@example.com', $author['email']);
    }

    public function testParseValidUtf8AuthorStringWithNonSpacingMarks()
    {
        // \xCC\x88 is UTF-8 for U+0308 diaeresis (umlaut) combining mark
        $utf8_expected = "Matti Meika\xCC\x88la\xCC\x88inen";
        $command = new InitCommand;
        $author = $command->parseAuthorString($utf8_expected." <matti@example.com>");
        $this->assertEquals($utf8_expected, $author['name']);
        $this->assertEquals('matti@example.com', $author['email']);
    }

    public function testParseNumericAuthorString()
    {
        $command = new InitCommand;
        $author = $command->parseAuthorString('h4x0r <h4x@example.com>');
        $this->assertEquals('h4x0r', $author['name']);
        $this->assertEquals('h4x@example.com', $author['email']);
    }

    /**
     * Test scenario for issue #5631
     * @link https://github.com/composer/composer/issues/5631 Issue #5631
     */
    public function testParseValidAlias1AuthorString()
    {
        $command = new InitCommand;
        $author = $command->parseAuthorString(
            'Johnathon "Johnny" Smith <john@example.com>'
        );
        $this->assertEquals('Johnathon "Johnny" Smith', $author['name']);
        $this->assertEquals('john@example.com', $author['email']);
    }

    /**
     * Test scenario for issue #5631
     * @link https://github.com/composer/composer/issues/5631 Issue #5631
     */
    public function testParseValidAlias2AuthorString()
    {
        $command = new InitCommand;
        $author = $command->parseAuthorString(
            'Johnathon (Johnny) Smith <john@example.com>'
        );
        $this->assertEquals('Johnathon (Johnny) Smith', $author['name']);
        $this->assertEquals('john@example.com', $author['email']);
    }

    public function testParseEmptyAuthorString()
    {
        $command = new InitCommand;
        self::expectException('InvalidArgumentException');
        $command->parseAuthorString('');
    }

    public function testParseAuthorStringWithInvalidEmail()
    {
        $command = new InitCommand;
        self::expectException('InvalidArgumentException');
        $command->parseAuthorString('John Smith <john>');
    }

    public function testNamespaceFromValidPackageName()
    {
        $command = new InitCommand;
        $namespace = $command->namespaceFromPackageName('new_projects.acme-extra/package-name');
        $this->assertEquals('NewProjectsAcmeExtra\PackageName', $namespace);
    }

    public function testNamespaceFromInvalidPackageName()
    {
        $command = new InitCommand;
        $namespace = $command->namespaceFromPackageName('invalid-package-name');
        $this->assertNull($namespace);
    }

    public function testNamespaceFromMissingPackageName()
    {
        $command = new InitCommand;
        $namespace = $command->namespaceFromPackageName('');
        $this->assertNull($namespace);
    }
}
