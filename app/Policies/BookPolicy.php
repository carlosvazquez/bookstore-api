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
        return true;
    }

    public function update(User $user, $book)
    {
        return true;
    }

    public function delete(User $user, $book)
    {
        return true;
    }

    public function modifyCategories(User $user, $book)
    {
        return true;
    }

    public function modifyAuthors(User $user, $book)
    {
        return true;
    }
}
