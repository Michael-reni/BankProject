<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BankingSystemTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_creting_account_returns_a_201_response_code()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
        $response = $this->post('/api/account/create_account'
        ,$data
        ,['Accept'=>'application/json']);

        $response->assertStatus(201);
        
    }

    public function test_creted_account_is_stored_in_database()
    {
         $data =  [ "name" => "test",
                     "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'name' => 'test',
            'surname' => 'test'
        ]);
    }

    public function test_creted_account_starting_balance_is_equal_zero()
    {
         $data =  [ "name" => "test",
                     "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' => 0     
        ]);
    }

    public function test_creting_account_with_missing_or_incorrect_data_returns_a_422_response_code()
    {
         $data =  [ "name" => 123,
                     "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);
         $response->assertStatus(422);

         $data =  ["surname" => "test"];

         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);
         $response->assertStatus(422);

    }


    public function test_adding_money_to_balance_account_works()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

         $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' => 0     
        ]);
        $data =  [  "value" => 100.99 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
        
       
        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' =>  100.99     
        ]);
    }

    public function test_withdraw_money_from_balance_account_works()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $data =  [  "value" => 120.99 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
        
        $data =  [  "value" => 50.73 ];
       
    
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' =>  70.26     
        ]);

    }

    public function test_cannot_add_negative_amount_of_money_to_the_account()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $data =  [  "value" => -100.99 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
        
        $response->assertStatus(422);
        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' =>  0     
        ]);
    }

    public function test_cannot_withdraw_more_money_than_there_is_in_the_account()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $data =  [  "value" => 1123.23 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  "value" => 1500 ];
        
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
        $response->assertStatus(422);

        $this->assertDatabaseHas('accounts', [
            'account_number' => $account_number,
            'balance' =>  1123.23     
        ]);
    }

    public function test_every_succesfull_banking_operation_is_save_in_accounts_history_table()
    {
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $data =  [  "value" => 2000 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  "value" => 1500 ];
        
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
      
        $this->assertDatabaseCount('accounts_history', 2);
    }

    public function test_every_faild_banking_operation_is_not_save_in_accounts_history_table()
    {
        //succesfull ones
        $data =  [ "name" => "test",
                    "surname" => "test"];
       
         $response = $this->post('/api/account/create_account'
         ,$data
         ,['Accept'=>'application/json']);

         $account_number = $response->decodeResponseJson()['account_number'];

        $data =  [  "value" => 2000 ];
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  "value" => 1500 ];
        
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);
      
        //unsuccesfull ones
        
        $data =  [  "value" => 3000 ]; //On account should be only 500 so you cant /withdraw that much. 
      
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  "value" => -2000 ]; // value cannot be negative
      
        $response = $this->put('/api/account/add_to_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  "value" => 'string' ]; // value cannot be string
        
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);

        $data =  [  'junk' => 'data' ]; // value must be present
        
        $response = $this->put('/api/account/withdraw_from_balance/' . $account_number
        ,$data
        ,['Accept'=>'application/json']);


        $this->assertDatabaseCount('accounts_history', 2);
    }
}
