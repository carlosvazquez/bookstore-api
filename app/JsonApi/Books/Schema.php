<?php

namespace App\JsonApi\Books;

use App\Models\Book;
use Neomerx\JsonApi\Schema\SchemaProvider;

class Schema extends SchemaProvider
{

    /**
     * @var string
     */
    protected $resourceType = 'books';

    /**
     * @param Book $resource
     *      the domain record being serialized.
     * @return string
     */
    public function getId($resource)
    {
        return (string) $resource->getRouteKey();
    }

    /**
     * @param Book $book
     *      the domain record being serialized.
     * @return array
     */
    public function getAttributes($book)
    {
        return [
            'title' => $book->title,
            'slug' => $book->slug,
            'content' => $book->content,
            'created-at' => $book->created_at->toAtomString(),
            'updated-at' => $book->updated_at->toAtomString(),
        ];
    }

    public function getRelationships($book, $isPrimary, array $includeRelationships)
    {
        return [
            'authors' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['authors']),
                self::DATA => function () use ($book) {
                    return $book->user;
                }
            ],
            'categories' => [
                self::SHOW_RELATED => true,
                self::SHOW_SELF => true,
                self::SHOW_DATA => isset($includeRelationships['categories']),
                self::DATA => function () use ($book) {
                    return $book->category;
                }
            ]
        ];
    }
}
