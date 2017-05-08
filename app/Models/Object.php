<?php

namespace App\Models;

use Framework\Database\DB;
use Framework\Model\Model;

class Object extends Model {

    protected $table = 'object';

    public function top($count = 10) {
        return DB::query("
            select object.id, object.name from (
                select object.*, rank() over (PARTITION BY object.type_id order by count(download.object_id) desc) as rate
                from object 
                    join download on download.object_id = object.id
                group by object.id
            ) object
            where rate <= {$count}
            order by object.rate desc
        ", static::class);
    }
}