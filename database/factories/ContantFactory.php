<?php

namespace Database\Factories;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Content>
 */
class ContantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Contact::class;
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'company_id' => function() {
                return App\Models\Company::factory()->create()->id;
            },
            'company_size' => function($contact) {
                // 바로 위에서 생성한 company_id 속성값을 사용
                return App\Models\Company::find($contact['company_id']->size);
            }
        ];  
    }
}
