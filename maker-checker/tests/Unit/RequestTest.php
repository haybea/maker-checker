<?php

namespace Tests\Unit;

//use PHPUnit\Framework\TestCase;

use App\Models\Admin;
use App\Models\AdminRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use JWTAuth;

class RequestTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCreateUserWithMiddleware()
    {
        $data = [
            'email' => "john@doe.com",
            'firstname' => "John",
            'lastname' => 'Doe',
        ];

        $response = $this->json('POST', '/api/users/add',$data);
        $response->assertStatus(401);
    }
    public function testUpdateUserWithMiddleware()
    {

        $user = User::factory()->create();
        $data = [
            'lastname' => 'Emma',
            'user_id' => $user->id,
        ];
        $response = $this->json('PATCH', '/api/users/edit',$data);
        $response->assertStatus(401);
    }
    public function testDeleteUserWithMiddleware()
    {

        $user = User::factory()->create();
        $response = $this->json('DELETE', '/api/users/delete/'.$user->id);
        $response->assertStatus(401);
    }
    public function testPendingRequestList()
    {
        $admin = Admin::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $user = User::factory()->create();
        $data = [
            'firstname' => "John",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->count(4)->state(new Sequence(
            ['request_type'=>'update'],
            ['request_type'=>'delete']
        ))->create(
            [
                'payload'=>$data,
                'user_id'=>$user->id,
                'maker_id'=>$admin->id
            ]
        );
        $response = $this->json('GET', '/api/pending-requests',[],['Authorization' => "Bearer $token"]);
        $response->assertStatus(200);
        $response->assertJsonStructure(
            [
                'success',
                'message',
                'data'=> [
                    '*' => [
                        'id',
                        'user_id',
                        'request_type',
                        'payload',
                        'status',
                    ]
                ]
            ]
        );
    }
    public function testApproveRequestWithMiddleware()
    {

        $user = User::factory()->create();
        $data = [
            'lastname' => 'Emma',
            'user_id' => $user->id,
        ];
        $response = $this->json('PATCH', '/api/users/edit',$data);
        $response->assertStatus(401);
    }
    public function testApproveRequestAsSameAdmin()
    {

        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $data = [
            'firstname' => "John",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'maker_id'=>$admin->id

        ]);

        $response = $this->json('GET', '/api/approve/'.$admin_request->id,[],['Authorization' => "Bearer $token"]);
        $response->assertStatus(401);
    }
    public function testApproveRequestAsAnotherAdmin()
    {

        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $data = [
            'firstname' => "John",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'maker_id'=>$admin->id

        ]);

        //Different Admin
        $admin2 = Admin::factory()->create();
        $token2 = JWTAuth::fromUser($admin2);

        $response2 = $this->json('GET', '/api/approve/'.$admin_request->id,[],['Authorization' => "Bearer $token2"]);
        $response2->assertStatus(200);
    }

    public function testDeclineRequestAsSameAdmin()
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $token = JWTAuth::fromUser($admin);
        $data = [
            'firstname' => "John",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'maker_id'=>$admin->id

        ]);

        $response = $this->json('GET', '/api/decline/'.$admin_request->id,[],['Authorization' => "Bearer $token"]);
        $response->assertStatus(401);
    }
    public function testDeclineRequestAsAnotherAdmin()
    {

        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $data = [
            'firstname' => "John",
            'user_id'=>$user->id
        ];
        $admin_request = AdminRequest::factory()->create([
            'payload'=>$data,
            'user_id'=>$user->id,
            'request_type'=>'update',
            'maker_id'=>$admin->id

        ]);

        //Different Admin
        $admin2 = Admin::factory()->create();
        $token2 = JWTAuth::fromUser($admin2);

        $response2 = $this->json('GET', '/api/decline/'.$admin_request->id,[],['Authorization' => "Bearer $token2"]);
//        $response2 = $this->json('GET', '/api/decline/'.$admin_request->id.'?token='.$token2);
        $response2->assertStatus(200);
    }

}
