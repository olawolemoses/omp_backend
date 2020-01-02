<?php
  
  namespace App\Http\Controllers\Vendor;

  use App\Http\Controllers\Controller;
  use App\Models\Category;
  use App\Models\Childcategory;
  use App\Models\Conversation;
  use App\Models\Generalsetting;
  use App\Models\Subcategory;
  use App\Models\VendorOrder;
  use Auth;
  use Illuminate\Http\Request;
  use DB;
  use Illuminate\Support\Facades\Input;
  use Session;
  use Validator;
  use Cloudder;


  class VendorCtrl extends Controller {
    public $global_language;

    public function __construct() {

      if (Session:: has('language')) {

        $data = DB:: table('language') -> find(Session:: get('language'));
        $data_results = file_get_contents(public_path(). '/assets/languages/'.$data -> file);
        $this -> vendor_language = json_decode($data_results);
      } 
        else {
        $data = DB:: table('language') -> where('is_default', '=', 1) -> first();
        $data_results = file_get_contents(public_path(). '/assets/languages/'.$data -> file);
        $this -> vendor_language = json_decode($data_results);
      }
    }

    public function index() {
      $user = Auth:: user();
      $pending = VendorOrder:: where('user_id', '=', $user -> id) -> where('status', '=', 'pending') -> get();
      $processing = VendorOrder:: where('user_id', '=', $user -> id) -> where('status', '=', 'processing') -> get();
      $completed = VendorOrder:: where('user_id', '=', $user -> id) -> where('status', '=', 'completed') -> get();
      return response()->json([
        'success' => true,
        'data' => compact('user','pending','processing','completed')
      ], 201);
    }

    public function profileupdate(Request $request) {

      $rules = [
        'shop_image' => 'mimes:jpeg,jpg,png,svg',
        'shop_number' => 'max:10',
      ];

      $validator = Validator::make(Input::all(), $rules);

      if ($validator->fails()) {
        return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
      }

      //--- Validation Section Ends
      $input = $request->all();
      $data = Auth::user();

    
      if($request->hasFile('shop_image') && $request->file('shop_image')->isValid()){
        $cloudder = Cloudder::upload($request->file('shop_image')->getRealPath());

        $uploadResult = $cloudder->getResult();

        $file_url = $uploadResult["url"];
        $vendor->shop_image = $file_url;
        
    
      }

      $data->update($input);
      $msg = 'Successfully updated your profile';
      return response()->json($msg);
    }

    public function socialupdate(Request $request) {
      //--- Logic Section
      $input = $request->all(); 
      $data = Auth::user();   
      if ($request->f_check == ""){
          $input['f_check'] = 0;
      }
      if ($request->t_check == ""){
          $input['t_check'] = 0;
      }

      if ($request->g_check == ""){
          $input['g_check'] = 0;
      }

      if ($request->l_check == ""){
          $input['l_check'] = 0;
      }
      $data->update($input);
      //--- Logic Section Ends
      //--- Redirect Section        
      $msg = 'Data Updated Successfully.';
      return response()->json($msg);
    }

    //*** GET Request
    public function profile() {
      $data = Auth::user();  
      return response()->json($data);
    }

    //*** GET Request
    public function ship() {
        $gs = Generalsetting::find(1);
        $data = Auth::user();  
        return response()->json($data);
    }

    //*** GET Request
    public function banner()  {
        $data = Auth::user();  
        return response()->json($data);        
    }

    //*** GET Request
    public function social() {
      $data = Auth::user();  
      return response()->json($data);        
    }

    //*** GET Request
    public function subcatload($id) {
      $cat = Category::findOrFail($id);
      return response()->json($cat);        
    }

    //*** GET Request
    public function childcatload($id) {
      $subcat = Subcategory::findOrFail($id);
      return response()->json($subcat);        
    }

  }