<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Contact2 extends Model
{
    protected $table = 'contacts_secondary';

    protected $primaryKey = 'contact_id';

    public $incrementing = false;
    public $timestamps = false;
    
    protected $dateFormat = 'U';
    
    $allContacts = Contact2::all();
    $vipContacts = Contact2::where('vip', true)->get();
    $newsContacts = Contact2::orderBy('created_at', 'desc')
        ->take(10)
        ->get();
    
    public function show($contactId)
    {
        return view('contacts.show')
            ->with('contact', Contact2::findOrFail($contactId));
    };

    $contacts = null;
    Contact2::chunk(100, function ($contacts) {
        foreach ($contacts as $contact) {
            //~~
        }
    });

    $contact = new Contact;
    $contact->name = 'Kim';
    $contact->email ='abc@gmail.com';
    $contact->save();

    // or 
    $contact = new Contact([
        'name' => 'Kim',
        'email' => 'abc@gmail.com'
    ]);
    $contact->save();

    // or
    $contact = Contact::make([
        'name' => 'Ken Hirta',
        'email' => 'abc@gmail.com'
    ]);
    $contact->save();

    //수정
    $contact = Contact::fine(1);
    $contact->email = 'abc@gmail.com';
    $contact->save();

    // 대량할당
    public function update(Contact $contact, Request $request)
    {
        $contact->update($requset->all());
    }
}
