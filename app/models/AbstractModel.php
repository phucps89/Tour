<?php
/**
 * Created by PhpStorm.
 * User: PhucTran
 * Date: 1/19/2016
 * Time: 10:29 PM
 */

class AbstractModel extends \Illuminate\Database\Eloquent\Model{

    public static function getTableName($column = NULL)
    {
        return with(new static)->getTable() . (! $column ? '' : '.' . $column);
    }

    public static function col($column)
    {
        return self::getTableName($column);
    }

    protected static function _getResultsPagination($query, $limit, $page)
    {
        $total = 0;
        $rawQuery = $query->toSql();
        if(str_contains($rawQuery,"group by")) {
            $countQuery = clone $query;
            $total = $countQuery->select(DB::raw("count(*) as AGGREGATE "))->get();
            $total = count($total);
        } else {
            $total = $query->count();
        }

        $result = $query->take($limit)->skip(($page-1)*$limit)->get();
        $from = $total == 0 ? 0 : ($page - 1)*$limit+1;
        $to = $page*$limit;
        return [
            'total' => $total,
            'results'   =>  $result,
            'currentPage'   =>  $page,
            'from'  =>  $from,
            'to'  =>  $to > $total ? $total : $to,
        ];
    }
}