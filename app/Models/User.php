<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeFiltered(Builder $builder) {
        $search = request('search') ?? null;
        $searchColumns = request('searchColumns') ?? null;

        $sort = request('sort') ?? null;
        $sortBy = request('sortBy') ?? null;
        $sortColumns = request('sortColumns') ?? null;

        // format

        $users = $builder->select(
            'users.id AS id',
            'users.name AS name',
            'users.last_name AS last_name',
            'users.cell_phone AS cell_phone',
            'users.address AS address',
            'users.email AS email',
        );

        if ($search && Str::length($search) > 0) {
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 1 ? implode("%", $listSearch) : "%{$search}%";

            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();

            $users->where(function($query) use ($search, $searchColumns) {
                foreach($searchColumns as $searchColumn){
                    $query->orWhereRaw("users.{$searchColumn} LIKE '{$search}'");
                }
            });
        }


        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();

        if(collect($sortColumns)->contains("name") &&  collect(['ASC', 'DESC'])->contains($sort)){
            $users->orderBy("users.{$sortBy}", $sort);
        }

        return $users;
    }
}
