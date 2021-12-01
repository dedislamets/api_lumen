<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Migrate;
use Illuminate\Support\Facades\Hash;
use Validator;
use Auth;
use function dd;
use Illuminate\Support\Facades\DB;

set_time_limit(0);

class MigrateController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function membersdata()
    {

    	// DB::beginTransaction();

        try {
        	

	    	$memberdata = DB::table("membersdata")->select('*')->whereNotIn('email',function($query) {
			   $query->select('email')->from('users');
			})->orderBy('MemberID')->chunk('200', function($rows) {

				$no=1;
			 	foreach($rows as $row) {
		 			print("<pre>".print_r($no . ". ". $row->Email . " " . $row->Name,true)."</pre>");
					$data = new Migrate();
			        $data->name 		= $row->Name;
			        $data->email 		= $row->Email;
			        $data->password 	= Hash::make($row->Password);
			        $data->phone 		= $row->Password;
			        $data->suspend 		= 0;

			        if($row->DateOfBirth != '0000-00-00'){
			        	$data->birth_date 	= $row->DateOfBirth;
			        }
			       
			        $data->phone 		= $row->HandPhone;
			        $data->member_code 	= $row->membercode;
			        $data->address 		= $row->Address;
			        $data->exported 	= 1;
			        $data->save();
			        $no++;
			 	}
			});

	    }catch (\Exception $e) {
            // DB::rollback();
            // throw $e;
            return response()->json(['status' => 'error','msg' => $e,401]);
        }

        // DB::commit();
        // return response()->json(['status' => 'success','msg' => ""], 201);

        // $user = Register::where(array('email' => $request->input('email'),"email_verified_at" => null))->first();

    }
}