<?php

  namespace App\Http\Controller\Vendor;

  use App\Http\Controllers\Controller;
  use Illuminate\Support\Facades\Input;
  use Illuminate\Http\Request;
  use App\Service;
  use Auth;
  use Datatables;
  use DB;
  use Session;
  use Validator;

  class ServiceCtrl extends Controller
  {
    
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
      
      $user = Auth::user(); 
      $datas = $user->services()->orderBy('id', 'desc')->get();

      return Datatables::of($datas)
                        ->toJson();
    }

    //*** POST Request
    public function create(Request $request) {
      
      $rules = [
        'photo' => 'required|mimes:jpeg,jpg,png,svg',
      ];

      $validator = Validator::make(Input::all(), $rules);

      if ($validator->fails()) {
        return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
      }

      $data = new Service();
      $input = $request->all();

      if ($file = $request->file('photo')) {
        $name = time().$file->getClientOriginalName();
        $file->move('asset/images/services', $name);
        $input['photo'] = $name;
      }

      $input['user_id'] = Auth::user()->id;
      $data->fill($input)->save();
      //--- Logic Section Ends

      //--- Redirect Section        
      $msg = 'New Data Added Successfully.';
      return response()->json($msg);  
    }

    public function update(Request $request, $id) {
      //--- Validation Section
      $rules = [
        'photo'      => 'mimes:jpeg,jpg,png,svg',
         ];

      $validator = Validator::make(Input::all(), $rules);
      
      if ($validator->fails()) {
        return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
      }
      //--- Validation Section Ends

      //--- Logic Section
      $data = Service::findOrFail($id);
      $input = $request->all();
          if ($file = $request->file('photo')) 
          {              
              $name = time().$file->getClientOriginalName();
              $file->move('assets/images/services',$name);
              if($data->photo != null)
              {
                  if (file_exists(public_path().'/assets/images/services/'.$data->photo)) {
                      unlink(public_path().'/assets/images/services/'.$data->photo);
                  }
              }            
          $input['photo'] = $name;
          } 
      $data->update($input);
      //--- Logic Section Ends

      //--- Redirect Section     
      $msg = 'Data Updated Successfully.';
      return response()->json($msg);      
      //--- Redirect Section Ends 
    }

    public function destroy($id) {

      $data = Service::findOrFail($id);
      //If Photo Doesn't Exist
      if($data->photo == null){
          $data->delete();
          //--- Redirect Section     
          $msg = 'Data Deleted Successfully.';
          return response()->json($msg);      
          //--- Redirect Section Ends     
      }
      //If Photo Exist
      if (file_exists(public_path().'/assets/images/services/'.$data->photo)) {
          unlink(public_path().'/assets/images/services/'.$data->photo);
      }
      $data->delete();
      //--- Redirect Section     
      $msg = 'Data Deleted Successfully.';
      return response()->json($msg);      
      //--- Redirect Section Ends     
    }

  }
  