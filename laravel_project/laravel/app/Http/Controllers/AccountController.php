<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AccountController extends Controller
{

    /**
     * @OA\Post(
     *      path="/account/create_account",
     *      tags={"BankingSystem"},
     *      summary="create banking account",
     *      @OA\RequestBody(
     *           
     *           required=true,
     *           description="pass data to create account",
        *      @OA\JsonContent(
        *          @OA\Property(property = "name", type="string", example="Jan",description = "User name"),
        *          @OA\Property(property = "surname", type="string", example="Kowalski",description = "User surname"),
        *      )
     *      ),
     * 
     *      @OA\Response(
     *          response=201,
     *          description="Created",
     *      @OA\JsonContent(),
     *          
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity",
     *      ),
     * 
     * 
     *       @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *     
     *     )
     */
   public function create_account(Request $request){
    
    $validated_data = $request->validate([
        'name' => 'required|string',
        'surname' => 'required|string'  
    ]);
    $validated_data['balance'] = 0;
    $validated_data['created_at'] = now()->toDateTimeString();
    $validated_data['updated_at'] = now()->toDateTimeString(); // sadly we need to manually set timestamps because insertGetId method doest support making it for us like save mathod 
    
    try{
        $account_number  =  DB::transaction(function() use ($validated_data) {
            return  Account::insertGetId($validated_data,'account_number'); // need that specific function because database is responsible for creating uuid not lararvel
         });
     }catch(\Exception $e){    
        return response()->json('there was an error during creation of account in database',500);
    }

    return response()->json( ['message' =>'account succesuflly created','account_number' => $account_number ],201);
   }

   
/**
     * @OA\Put(
     *      path="/account/add_to_balance/{uuid}",
     *      tags={"BankingSystem"},
     *      summary="Add money to account balance",
     *      @OA\Parameter(
     *           name="uuid",
     *           required=true,
     *           in="path",
     *           description="this uuid is account number"
     *      ),
     * 
     *      @OA\RequestBody(         
     *           required=true,
     *           description="pass data to create account",
        *      @OA\JsonContent(
        *          @OA\Property(property =  "value", type="double", example="120.25",description = "Amount of money that will be added to account"),
           *      )
     *      ),
     * 
     *      @OA\Response(
     *          response=202,
     *          description="Accepted",
     *          @OA\JsonContent(),
     *          
     *       ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity",
     *          @OA\JsonContent(),
     *      ),
     * 
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *      ),
     * 
     *       @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *     
     *     )
     */

   public function add_to_balance(Request $request,$uuid){
    
    $validator = Validator::make(['uuid' => $uuid], [
        'uuid' => 'required|uuid'
    ]);

    if ($validator->fails()) {
       return response()->json($validator->errors(),422);
    }
    $uuid = $validator->validated();

    $validated_data = $request->validate([
        'value' => 'required|regex:/^\d+(\.\d{1,2})?$/'  
    ],
    ['value.regex' => "pass data in 'money' format somethink like: 120.25 "]);
    
    try {
        $account = Account::findOrFail($uuid['uuid']);
    }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
        return response()->json('Account not found',404);
    }
    
    $account->balance += $validated_data['value'];
    
    $account_history = new  AccountHistory;
    $account_history->account_number = $uuid['uuid'];
    $account_history->operation_type = 'add_to_balance'; 
    $account_history->amount = $validated_data['value'];
    $account_history->created_at = now()->toDateTimeString();
    


    DB::beginTransaction();
    try{
        $account->save();  
        $account_history->save();     
        DB::commit();
    }catch(\Exception $e){
        DB::rollback();
        return response()->json('there was an error during balance update in database',500);
    }
 
    return response()->json(['message'=>'balance updated current value: ' . $account->balance,'balance_value' => round($account->balance,2)],202);

   }

/**
     * @OA\Put(
     *      path="/account/withdraw_from_balance/{uuid}",
     *      tags={"BankingSystem"},
     *      summary="withdraw money from account balance",
     *      @OA\Parameter(
     *           name="uuid",
     *           required=true,
     *           in="path",
     *           description="this uuid is account number"
     *      ),
     * 
     *      @OA\RequestBody(         
     *           required=true,
     *           description="pass data to create account",
     *      @OA\JsonContent(
     *          @OA\Property(property =  "value", type="double", example="120.25",description = "Amount of money that will be withdraw from the account"),
     *          )
     *      ),
     * 
     *      @OA\Response(
     *          response=202,
     *          description="Accepted",
     *          @OA\JsonContent(),
     *          
     *       ),
     * 
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *      ),
     * 
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable entity",
     *      @OA\JsonContent(),
     *      ),
     * 
     *       @OA\Response(
     *          response=500,
     *          description="Internal server error",
     *      ),
     *     
     *     )
     */

   public function withdraw_from_balance(Request $request,$uuid){
    $validator = Validator::make(['uuid' => $uuid], [
        'uuid' => 'required|uuid'
    ]);

    if ($validator->fails()) {
       return response()->json($validator->errors(),422);
    }
    $uuid = $validator->validated();

    $validated_data = $request->validate([
        'value' => 'required|regex:/^\d+(\.\d{1,2})?$/'  
    ],
    ['value.regex' => "pass data in 'money' format somethink like: 120.25 "]);
    
    try {
        $account = Account::findOrFail($uuid['uuid']);
    }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
        return response()->json('Account not found',404);
    }
    
    $account->balance -= $validated_data['value'];

    $account_history = new  AccountHistory;
    $account_history->account_number = $uuid['uuid'];
    $account_history->operation_type = 'withdraw_from_balance'; // hardcoded should not be...
    $account_history->amount = $validated_data['value'];
    $account_history->created_at = now()->toDateTimeString();
   
    if ($account->balance < 0 ){
        return response()->json('U cannot withdraw more moeny than you possess on this account',422);
    }
    
    DB::beginTransaction();
    try{
        $account->save();  
        $account_history->save();     
        DB::commit();
    }catch(\Exception $e){
        DB::rollback();
        return response()->json('there was an error during balance update in database',500);
    }
 
    return response()->json(['message'=>'balance updated current value: ' . $account->balance,'balance_value' =>round($account->balance,2)],202);

   }


    /**
     * @OA\Get(
     *      path="/account/check_balance/{uuid}",
     *      tags={"BankingSystem"},
     *      summary="check account balance",
     *      @OA\Parameter(
    *           name="uuid",
    *           required=true,
    *           in="path",
    *           description="this uuid is account number",
     *      ),
     * 
     *      @OA\Response(
     *          response=200,
     *          description="OK",
     *          
     *       ),
     * 
     *      @OA\Response(
     *          response=404,
     *          description="Not Found",
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Unprocessable Entity",
     *      ),
     *     
     *     )
     */
   public function check_balance(Request $request,$uuid){

    $validator = Validator::make(['uuid' => $uuid], [
        'uuid' => 'required|uuid'
    ]);

    if ($validator->fails()) {
       return response()->json($validator->errors(),422);
    }
    $uuid = $validator->validated();

    try {
        $account = Account::findOrFail($uuid['uuid']);
    }catch(\Illuminate\Database\Eloquent\ModelNotFoundException $e){
        return response()->json('Account not found',404);
    }

    return response()->json(['message'=>'balance current value: ' . $account->balance,'balance_value' =>round($account->balance,2)],200);


   }




}
