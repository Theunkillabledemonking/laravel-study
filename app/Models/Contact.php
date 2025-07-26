<?php

namespace App\Models;

use Faker\Provider\bg_BG\PhoneNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Database\Query\Builder;

class Contact extends Model
{   
    public function saveFromRequest(Request $request)
    {
        // 사용자의 입력으로부터 새로운 연락처 데이터를 생성하고 저장
        $contact = new Contact();
        $contact->first_name = $request->input('first_name');
        $contact->last_name = $request->input('last_name');
        $contact->email = $request->input('email');
        $contact->save();

        return redirect('contacts');
    }

    public function show($contactId)
    {
        // URL 세그먼트를 기반으로 하나의 연락처 정보를 조회, JSON으로 반환
        // 만약 ID에 해당하는 데이터가 없으면 예외 발생
        return Contact::findOrFail($contactId);
    }

    public function vips()
    {
        // vip로 지정된 연락처 목록을 확인하여 'formalName' 속성을 지정
        return Contact::where('vip', true)->get()->map(function ($contact) {
            $contact->formalName = "The exalted {$contact->firstname} of the
            {$contact->last_name}s";
            return $contact;
        });
    }
    /** @use HasFactory<\Database\Factories\ContactFactory> */
    use HasFactory;

    protected $fillable = ['name', 'email'];
    protected $guarded = ['id', 'created_at', 'updated_at', 'owner_id'];

    public function a()
    {
        $contact = Contact::firstOrCreate(['email' => 'abc@gmailc.om']);

        // 삭제
        $contact = Contact::find(5);
        $contact->delete();
        // Id 값을 미리 안다면
        Contact::destroy(1);
        //or
        Contact::destroy([1, 5, 7]);
    }

    public function scopeActiveVips($query) {
        $activeVips = Contact::where('vip', true)->where('trial', false)->get();
        return $query->where('vip', true)->where('trial', false);
    }

    // 글로벌 스코프 예시
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('active', function (Builder $builder) {
            $builder->where('active', true);
        });
    }

    public function getnameAttribute($value)
    {
        return $value ?: '(No name provided)';
    }
    // 정의한 접근자 사용
    //$name = $contact->name;
    // 존재하지 않는 값의 접근 속성 제어자
    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    // 정의한 접근자 사용
    //$fullName = $contact->full_name;

    // 변경자
    public function setAmountAttribute($value)
    {
        $this->attributes['amount'] = $value > 0 ? $value : 0;
    }
    // 정의한 변경자 사용
    //$order->aoumt = 15;
    // 존재하지 않는 값의 접근 속성 변경자
    public function setWorkgroupNameAttribute($workgroupName)
    {
        $this->attributes['email'] = "{$workgroupName}@ourcompany.com";
    }
    // 속성값 형변환
    protected $casts = [
        'vip' => 'boolean',
        'children_names' => 'array',
        'birthday' => 'date',
    ];

    public $hidden = ["password", "remeber_token"];
    public $visible = ["name", "email", "status"];

    // 1대1 연관관계
    public function phoneNumber()
    {
        return $this->hasOne(PhoneNumber::class, 'owner_id');
    }

    

}

 // 1대1 연관관계
$contact = Contact::first();
$contactPhone = $contact->phoneNumber;

class GetPhoneNumber extends Model
{
    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }

    // 하위 모델에서 상위 모델으 타임스탬프 값 갱신
    protected $touches = ['contact2'];

    public function contact2()
    {
        return $this->belongsTo(Contact::class);
    }
    // N+1 문제를 피하기 위한 eager 로딩
    public function eager()
    {
        $contacts = Contact::all();

        foreach ($contacts as $contact) {
            foreach ($contact->phone_numbers as $phone_number) {
                echo $phone_number->number;
            }
        }
        $contacts = Contact::with('phoneNumbers')->get();
    }
} 

$contact = $GetphoneNumber->contact;

