<?php

/*
 * This file is part of WordPlate.
 *
 * (c) Vincent Klaiber <hello@vinkla.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace WordPlate\Tests\Acf;

use PHPUnit\Framework\TestCase;
use WordPlate\Acf\Field;
use WordPlate\Acf\Group;

/**
 * This is the field test class.
 *
 * @author Vincent Klaiber <hello@vinkla.com>
 */
class FieldTest extends TestCase
{
    /**
     * @runInSeparateProcess
     */
    public function testGetKey()
    {
        $field = $this->getField();

        $this->assertSame('field_employee_image', $field->getKey());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSetKey()
    {
        $field = $this->getField();

        $field->setKey('thumbnail');

        $this->assertSame('field_employee_thumbnail', $field->getKey());
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetFields()
    {
        $field = $this->getField();

        $subFields = $field->getFields('sub_fields');

        $this->assertCount(1, $subFields);
        $this->assertSame('field_employee_image_source', $subFields[0]['key']);

        $layouts = $field->getFields('layouts');

        $this->assertCount(1, $layouts);
        $this->assertSame('field_employee_image_author', $layouts[0]['key']);
    }

    /**
     * @runInSeparateProcess
     */
    public function testGetConditionalLogic()
    {
        $field = $this->getField();

        $this->assertInternalType('array', $field->getConditionalLogic());
    }

    /**
     * @runInSeparateProcess
     */
    public function testToArray()
    {
        $field = $this->getField();

        $this->assertSame([
            'label' => 'Thumbnail',
            'name' => 'image',
            'sub_fields' => [
                [
                    'type' => 'text',
                    'label' => 'Source',
                    'name' => 'source',
                    'key' => 'field_employee_image_source',
                ],
            ],
            'layouts' => [
                [
                    'type' => 'text',
                    'label' => 'Author',
                    'name' => 'author',
                    'key' => 'field_employee_image_author',
                ],
            ],
            'conditional_logic' => [
                [
                    [
                        'field' => 'field_employee_source',
                        'operator' => '==',
                        'value' => 'https://example.com/',
                    ],
                ],
            ],
            'key' => 'field_employee_image',
        ], $field->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The field key [field_employee_link] is not unique.
     */
    public function testKeyDuplication()
    {
        $group = $this->getGroup();

        new Field($group, acf_url(['name' => 'link', 'label' => 'Link']));
        new Field($group, acf_url(['name' => 'link', 'label' => 'Link']));
    }

    protected function getField()
    {
        return new Field($this->getGroup(), [
            'label' => 'Thumbnail',
            'name' => 'image',
            'sub_fields' => [
                acf_text(['label' => 'Source', 'name' => 'source']),
            ],
            'layouts' => [
                acf_text(['label' => 'Author', 'name' => 'author']),
            ],
            'conditional_logic' => [
                [
                    acf_conditional_logic('source', 'https://example.com/'),
                ],
            ],
        ]);
    }

    protected function getGroup()
    {
        return new Group([
            'key' => 'employee',
            'title' => 'Employee',
            'fields' => [],
        ]);
    }
}
