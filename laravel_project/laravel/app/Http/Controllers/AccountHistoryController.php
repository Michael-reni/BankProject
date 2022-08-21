<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Account;
use App\Models\AccountHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Exports\AccountHistoryExport;
use Maatwebsite\Excel\Facades\Excel;

class AccountHistoryController extends Controller
{

    /**
     * @OA\Get(
     *      path="/account/check_history/{uuid}",
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
     *           @OA\MediaType(
     *          mediaType="application/json"
     *      ),
     *      @OA\MediaType(
     *          mediaType="application/xlsx"
     *      )
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
    
   public function check_history(Request $request,$uuid){

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

    $AccountHistory = AccountHistory::where('account_number',$uuid['uuid'])->paginate(15)->withQueryString();
    
    $download = $request->header("accept") ? : 'application/json';
   
    if ( $download ==  'application/xlsx' )
    {
        $data = $AccountHistory->toarray()['data'];
        $export_xlsx = new AccountHistoryExport($data,count($data));
        return Excel::download($export_xlsx,$uuid['uuid'].'_account_history_' .now()->toDateTimestring().'.xlsx');
    }


    return response()->json($AccountHistory,200);

   }
}
