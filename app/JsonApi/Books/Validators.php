<?php

namespace App\JsonApi\Books;

use App\Rules\Slug;
use Illuminate\Validation\Rule;
use CloudCreativity\LaravelJsonApi\Rules\HasOne;
use CloudCreativity\LaravelJsonApi\Validation\AbstractValidators;

class Validators extends AbstractValidators
{

    /**
     * The include paths a client is allowed to request.
     *
     * @var string[]|null
     *      the allowed paths, an empty array for none allowed, or null to allow all paths.
     */
    protected $allowedIncludePaths = ['authors', 'categories'];

    /**
     * The sort field names a client is allowed send.
     *
     * @var string[]|null
     *      the allowed fields, an empty array for none allowed, or null to allow all fields.
     */
    protected $allowedSortParameters = ['title', 'content'];

    /**
     * The filters a client is allowed send.
     *
     * @var string[]|null
     *      the allowed filters, an empty array for none allowed, or null to allow all.
     */
    protected $allowedFilteringParameters = ['title', 'content', 'year', 'month', 'search', 'categories', 'authors'];

    /**
     * Get resource validation rules.
     *
     * @param mixed|null $record
     *      the record being updated, or null if creating a resource.
     * @param $data
     * @return mixed
     */
    protected function rules($record, $data): array
    {
        return [
            'title' => ['required'],
            'slug' => [
                'required',
                'alpha_dash',
                new Slug,
                Rule::unique('books')->ignore($record)
            ],
            'content' => ['required'],
            'authors' => [
                Rule::requiredIf(! $record),
                new HasOne('authors')
            ],
            'categories' => [
                Rule::requiredIf(! $record),
                new HasOne('categories')
            ]
        ];
    }

    /**
     * Get query parameter validation rules.
     *
     * @return array
     */
    protected function queryRules(): array
    {
        return [
            //
        ];
    }
}
