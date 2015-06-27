<?php

class UserTest extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $this->artisan('migrate');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $this->post('/user', ['email' => 'test@test.com',
            'password' => 'password1234',
            'password_confirmation' => 'password1234'])
        ->seeJson([
            'data' => 'User created successfully',
        ]);
    }

    public function testGetUsers()
    {
        $user = factory('App\User')->create();
        $user2 = factory('App\User')->create();

        $this->get('/user')
            ->seeJson([
                'email' => $user->email
            ])
            ->seeJson([
                'email' => $user2->email
            ]);
    }

    public function testGetUser()
    {
        $user = factory('App\User')->create();

        $this->get('/user/'.$user->id)
            ->seeJson([
                'email' => $user->email
            ]);
    }

    public function buildQuery($queries)
    {
        $separator = '?';
        $string = '';
        foreach($queries as $key => $query)
        {
            $string .= $separator;

            $string .= $key . "=" . $query;

            if($separator != '&') $separator = '&';
        }

        return $string;
    }

    public function testUpdateUser()
    {
        $user = factory('App\User')->create();

        $email = 'new@user.com';

        $user->email = $email;

        $query = $this->buildQuery($user->toArray());

        $this->put('/user/'.$user->id.$query)
            ->seeJson([
                'data' => 'User updated successfully'
            ]);

        $this->get('/user/'.$user->id)
            ->seeJson([
                'email' => $email
            ]);
    }

    public function testDeleteUser()
    {
        $user = factory('App\User')->create();

        $this->delete('/user/'.$user->id)
            ->seeJson([
                'data' => 'User deleted successfully'
            ]);
    }
}
