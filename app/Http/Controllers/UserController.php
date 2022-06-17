<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //

    public function list(){

        $draw = request('draw');
        $start = request('start');
        $length = request('length');
        $search = request('search');
        $columns = request('columns');
        $order = request('order');

        $users = User::query();

        $recordsTotal = $users->count('id');

        $recordsFiltered = 0;
        if($search){
            $firstColumn = true;
            foreach($columns as $column){
                if($column['searchable'] === 'true'){
                    if ($firstColumn){
                        $users->where($column['data'], 'LIKE', "%{$search}%");
                        $firstColumn = false;
                    }else{
                        $users->orWhere($column['data'], 'LIKE', "%{$search}%");
                    }
                }
            }
            $recordsFiltered = $users->count('id');
        }else{
            $recordsFiltered = $recordsTotal;
        }

        if($columns[$order['column']]['orderable'] == 'true'){
            $users->orderBy($columns[$order['column']]['data'], $order['dir']);
        }

        $users->skip($start);
        $users->limit($length);
        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $users->get()
        ], 200);
    }
}
