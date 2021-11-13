<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function list(){
        $pageLength = request('pageLength') ?? 10;
        $users = User::filtered();

        return response()->json($users->paginate($pageLength), 200);
    }
}
