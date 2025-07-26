<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Collections extends Model
{
    /** @use HasFactory<\Database\Factories\CollectionFactory> */
    use HasFactory;

    public function getProcessCollection()
    {
        $collection = collect([1, 2, 3]);
        
        $odds = $collection->reject(function ($item) {
            return $item % 2 === 0;
        });

        $multipied = $collection->map(function ($item) {
            return $item * 10;
        });

        $sum = $collection
            ->filter(function ($item) {
                return $item % 2 == 0;
            })->map(function ($item) {
                return $item * 10;
            })->sum();
    }

    // test code
    public function test_active_page_shows_active_and_not_inactive_contacts()
    {
        $activeContact = Contact::factory()->create();
        $inactiveContact = Contact::factory()->inactive()->create();

        // url /active-contacts로 요청한 결과 확인
        $this->get('active-contacts')
            ->assertSee($activeContact->name)
            ->assertDontSee($inactiveContact->name);
    }
}

class OrderCollection extends Collections
{
    public function sumBillableAmount()
    {
        return $this->reduce(function ($carry, $order) {
            return $carry + ($order->billable ? $order->amount: 0);
        }, 0);


    }
}

class Order extends Model
{
    public function newCollection(array $models = [])
    {
        return new OrderCollection($models);
    }
}

$orders = Order::all();
$billableAmount = $orders->sumBillableAmount();