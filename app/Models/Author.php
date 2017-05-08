<?php

namespace App\Models;

use Framework\Database\DB;
use Framework\Model\Model;

class Author extends Model {

    protected $table = 'author';

    public function top($count = 10) {
        return DB::query("
            with best_authors_loc as (
                select author.id, author.name as author, object.name as object, object_type.name as type,
                (
                    select count(*)
                    from download
                        where download.object_id = object_author_role.object_id
                ) as count_download,
                count(object_author_role.*) as count_role
                from author
                join object_author_role on author.id = object_author_role.author_id
                join object on object.id = object_author_role.object_id
                join object_type on object_type.id = object.type_id
                group by author.id, object_author_role.object_id, object.name, object_type.name
            )
            select *
            from best_authors_loc
            limit $count
        ", static::class);
    }

}