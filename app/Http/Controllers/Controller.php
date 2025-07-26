<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class Controller
{   
    public function index()
    {
    // 쿼리를 직접 전달하는 방식
    DB::statement('drop table users');

    // SELECT 쿼리를 지겁 전달하고 파라미터를 바인딩하는 호출 방식
    DB::select('select * from contacts where validated = ?', [true]);

    // 체이닝 방법을 사용해 데이터를 조회하는 방법
    $users = DB::table('users')->get();

    // 다른 테이블과의 JOIN 구문을 체이닝으로 호출하는 방법
    DB::table('users')
        ->join('contacts', function ($join) {
            $join->on('users_id', '=', 'contacts.user_id')
                ->where('contacts.type', 'donor');
        })
        ->get();
    }
}

class TestController
{
    public function index()
    {   
        // select
        $emails = DB::table('contacts')
            ->select('email', 'email2 as second_email')
            ->get();
        // or
        $emails = DB::table('contact')
            ->select('email')
            ->addSelect('email2 as second_email')
            ->get();

        //where 
        $newContacts = DB::table('contact')
            ->where('created_at', '>', now()->subDay())
            ->get();
        $vipContacts = DB::table('contact')->where('vip',true)->get(); // = 연산자 생략가능
        $newVips = DB::table('contacts')
            ->where('vip', true)
            ->where('created_at', '>', now()->subDay());
        //orWhere()
        $priorityContacts = DB::table('contacts')
            ->where('vip', true)
            ->orWhere('created_at', '>', now()->subDay())
            ->get();
        //whereBetween()
        $mediumDrinks = DB::table('drinks')
            ->whereBetween('size', [6, 12])
            ->get();
        // whereIn()
        $closeBy = DB::table('conatacts')
            ->whereIn('state', ['FL', 'GA', "AL"])
            ->get();
        // whereRaw()
        $goofs = DB::table('contacts')
            ->whereRaw('id = 12345')->get();
        // whereExists()
        $commneters = DB::table('users')
            ->whereExists(function ($query) {
                $query->select('id')
                    ->from('comments')
                    ->whereRaw('comments.user_id = user.id');
            })
            ->get();
        $lastNames = DB::table('contacts')->select('city')->distinct()->get();
        
        // 쿼리 결과 변경 메서드
        // orderBy
        $contacts = DB::table('contacts')
            ->orderBy('last_name', 'asc') // desc (내림차순)
            ->get();
        // groupBy(), having(), havingRaw()
        $populousCities = DB::table('contacts')
            ->groupBy('city')
            ->havingRaw('count(contact_id) > 30')
            ->get();
        // skip(), take()
        $page4 = DB::table('contacts')
            ->skip(30)->take(10)->get();

        // 조건에 따라 쿼리를 추가하는 메서드
        $status = null;
        $posts = DB::table('posts')
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->get();
        
        // 쿼리를 실행하고 결과를 반환하는 메서드
        // get()
        $vipContacts = DB::table('contacts')->where('vip', true)->get();
        // first(), firstOrFail()
        $newContact = DB::table('contacts')->orderBy('created_at', 'desc')
            ->first();
        // find(), findOrFail()
        $contactFive = DB::table('contacts')->find(5);
        // value()
        $newestContactEmail = DB::table('contacts')
            ->orderBy('created_at', 'desc')
            ->value('email');
        // count()
        $countVips = DB::table('contacts')
            ->where('vip', true)
            ->count();
        // min() max()
        $highestCost = DB::table('orders')->max('amount');
        // sum() avg()
        $averageCost = DB::table('orders')
            ->where('status', 'completed')
            ->avg('amount');
        // DB Raw를 사용해 쿼리 빌더 내부에 원시 쿼리 작성
        $contacts = DB::table('contacts')
            ->select(DB::raw('*, (score * 100) AS integer_score'))
            ->get();

        // 조인 쿼리
        $users = DB::table('users')
            ->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->select('users.*', 'contacts.name', 'contacts.status')
            ->get();
        // 유니온 쿼리
        $first = DB::table('contacts')
            ->whereNull('first_name');
        $contacts = DB::table('contacts')
            ->whereNull('last_name')
            ->union($first)
            ->get();
        // 인서트 쿼리
        $id = DB::table('contacts')->insertGetId([
            'name' => 'Abe Thomas',
            'email' => 'athomas1987@gmail.com'
        ]);
        DB::table('contacts')->insert([
            ['name' => 'Tamika Johnson', 'email' => 'tamikaj@gmail.com'],
            ['name' => 'Jim Patterson', 'email' => 'james.patterson@hotmail.com']
        ]);
        // 업데이트 쿼리
        DB::table('contacts')
            ->where('posts', '>', 100)
            ->update(['status' => 'vip']);
        // 삭제 쿼리
        DB::table('users')
            ->where('last_login', '<', now()->subYear())
            ->delete();
        // JSON 연산
        DB::table('users')->where('options->isAdmin', true)->get();
        DB::table('users')->update(['options->isVerfied', true]);

        // 트랙잭션
        $userId = null;
        $numVotes = null;
        DB::transaction(function () use ($userId, $numVotes) {
            // 실패 가능성이 있는 DB 쿼리
            DB::table('users')
                ->where('id', $userId)
                ->update(['votes' => $numVotes]);
            // 위의 쿼리가 실패하면 실행되지 않는 쿼리
            DB::table('votes')
                ->where('user_id', $userId)
                ->delete();
            // 명시적인 방법
            DB::beginTransaction();
            if ($userId) {
                DB::rollBack();
            }
            DB::commit();
        });
        
    }
}