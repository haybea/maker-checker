<?php

namespace Database\Factories;

use App\Models\Admin;
use App\Models\AdminRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AdminRequestFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AdminRequest::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'maker_id'=>1,
            'checker_id'=>null,
            'user_id'=>null,
            'request_type'=> 'create',
            'payload'=>null,
            'status'=>'pending'
        ];
    }
}
