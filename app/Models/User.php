<?php

namespace App\Models;

use Framework\Database\DB;
use Framework\Model\Model;

class User extends Model {

    protected $table = 'users';

    public function top($count = 10) {
        return DB::query("
            with rate_users as (
                select users.*,
                (
                    select count(*) from download
                        join object on object.id = download.object_id
                        join object_type on object_type.id = object.type_id
                    where download.user_id = users.id
                    group by download.user_id
                ) as count_download
                from users
            )
            select
                *
            from (
                select
                    *,
                    dense_rank() over (order by  COALESCE(rate_users.count_download, 0) desc) as rate_download
                from rate_users
            ) users
            where rate_download <= $count
            order by rate_download
        ", static::class);
    }

}