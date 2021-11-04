<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

//    public function before(User $user)
//    {
//        if ($user->tokenCan('books:admin')) {
//            return true;
//        }
//    }

    public function create(User $user, $request)
    {
        return $user->tokenCan('books:create') &&
            $user->id === $request->json('data.relationships.authors.data.id');
    }

    public function update(User $user, $book)
    {
        return $user->tokenCan('books:update') &&
            $book->user->is($user);
    }

    public function delete(User $user, $book)
    {
        return $user->tokenCan('books:delete') &&
            $book->user->is($user);
    }

    public function modifyCategories(User $user, $book)
    {
        return $user->tokenCan('books:modify-categories') &&
            $book->user->is($user);
    }

    public function modifyAuthors(User $user, $book)
    {
        return $user->tokenCan('books:modify-authors') &&
            $book->user->is($user);
    }
}
