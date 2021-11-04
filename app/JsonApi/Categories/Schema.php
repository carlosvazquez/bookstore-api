<?php

namespace App\JsonApi\Categories;

use App\Models\Category;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'categories';

    /**
     * @param Category $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param Category $resource
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($resource)
    {
        return [
            'name' => $resource->name,
            'slug' => $resource->slug,
        ];
    }

    public function getRelationships($category, $isPrimary, array $includeRelationships)
    {
        return [
            'books' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['books']),
                self::DATA => function () use ($category) {
                    return $category->books;
                }
            ]
        ];
    }
}
