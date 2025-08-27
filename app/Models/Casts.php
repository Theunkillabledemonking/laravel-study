<?php

namespace App\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

// class Casts implements CastsAttributes
// {
//     /**
//      * Cast the given value.
//      *
//      * @param  array<string, mixed>  $attributes
//      */
//     public function get(Model $model, string $key, mixed $value, array $attributes): mixed
//     {
//         return $value;
//     }

//     /**
//      * Prepare the given value for storage.
//      *
//      * @param  array<string, mixed>  $attributes
//      */
//     public function set(Model $model, string $key, mixed $value, array $attributes): mixed
//     {
//         return $value;
//     }
// }

class Json implements CastsAttributes
{
    /**
     * 주어진 값 형변환
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param mixed $value
     * @param array $attributes
     * return array
     */
    public function get($model, $key, $value, $attributes)
    {
        return json_decode($value, true);
    }

    /**
     * 주어진 값을 저장히기 위해 준비
     * 
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $key
     * @param array $value
     * @param array $attributes
     * @return string
     */
    public function set($model, $key, $value, $attributes)
    {
        return json_encode($value);
    }
}

// class User extends Model
// {
//     /**
//      * 형변환이 되어야 하는 속성들
//      * @var array
//     */
//     protected $casts = [
//         'options' => Json::class,
//     ];

//     // 날짜 변경자
//     protected $dates = [
//         'met_at',
//     ];

// }