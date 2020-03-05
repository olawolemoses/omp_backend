<?php

  namespace App\Http\Controllers\Front;

  use App\Classes\GeniusMailer;
  use App\Models\User;
  use App\Models\Conversation;
  use App\Models\Message;
  use App\Models\Product;
  use App\Models\Category;
  use App\Models\Subcategory;
  use App\Models\Childcategory;
  use App\Models\Generalsetting;
  use Illuminate\Support\Facades\DB;
  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use Illuminate\Support\Collection;


  class VendorCtrl extends Controller
  {

    public function index(Request $request,$slug) {
      $this->code_image();
      $sort = "";
      $min="";
      $max="";
      $string = str_replace('-',' ', $slug);
      $vendor = User::where('shop_name','=',$string)->first();
      $services = DB::table('services')->where('user_id','=',$vendor->id)->get();
      $oldcats = $vendor->products()->where('status','=',1)->orderBy('id','desc')->get();
      $vprods = (new Collection(Product::filterProducts($oldcats)))->paginate(9);

      // Search By Price
      if(!empty($request->min) || !empty($request->max)) {
        $min = $request->min;
        $max = $request->max;
        $oldcats = $vendor->products()->where('status','=',1)->whereBetween('price', [$min, $max])->orderBy('price','asc')->get();
        $vprods = (new Collection(Product::filterProducts($oldcats)))->paginate(9);
        return response()->json([
          'success' => true,
          'data' => compact('vendor','vprods','sort','min','max','services')
        ], 201);
      }

      // Search By Sort
      if( !empty($request->sort)) {
        $sort = $request->sort;
        if($sort == "new") {
          $oldcats = $vendor->products()->where('status','=',1)->orderBy('id','desc')->get();
          $vprods = (new Collection(Product::filterProducts($oldcats)))->paginate(9);
        }
        else if($sort == "old") {
          $oldcats = $vendor->products()->where('status','=',1)->get();
          $vprods = (new Collection(Product::filterProducts($oldcats)))->paginate(9);
        }
        else if($sort == "low") {
          $oldcats = $vendor->products()->where('status','=',1)->orderBy('price','asc')->get();
          $vprods = (new Collection(Product::filterProducts($oldcats)))->sortBy('price')->paginate(9);
        }
        else if($sort == "high") {
          $oldcats = $vendor->products()->where('status','=',1)->orderBy('price','desc')->get();
          $vprods = (new Collection(Product::filterProducts($oldcats)))->sortByDesc('price')->paginate(9);
        }

        return response()->json([
            'success' => true,
            'data' => compact('vendor','vprods','sort','min','max','services')
          ], 201);
      }
      // Otherwise Go To Category
      return response()->json([
        'success' => true,
        'data' => compact('vendor','vprods','sort','min','max','services')
      ], 201);
    }

    //Send email to user
    public function vendorcontact(Request $request) {

      $user = User::findOrFail($request->user_id);
      $vendor = User::findOrFail($request->vendor_id);
      $gs = Generalsetting::findOrFail(1);
      $subject = $request->subject;
      $to = $vendor->email;
      $name = $request->name;
      $from = $request->email;
      $msg = "Name: ".$name."\nEmail: ".$from."\nMessage: ".$request->message;
      if($gs->is_smtp) {
        $data = [
          'to' => $to,
          'subject' => $subject,
          'body' => $msg,
        ];

        $mailer = new GeniusMailer();
        $mailer->sendCustomMail($data);            
      }
      else{
        $headers = "From: ".$gs->from_name."<".$gs->from_email.">";
        mail($to,$subject,$msg,$headers);            
      }

      $conv = Conversation::where('sent_user','=',$user->id)->where('subject','=',$subject)->first();

      if(isset($conv)) {
        $msg = new Message();
        $msg->conversation_id = $conv->id;
        $msg->message = $request->message;
        $msg->sent_user = $user->id;
        $msg->save();
      }
      else {
        $message = new Conversation();
        $message->subject = $subject;
        $message->sent_user= $request->user_id;
        $message->recieved_user = $request->vendor_id;
        $message->message = $request->message;
        $message->save();
        $msg = new Message();
        $msg->conversation_id = $message->id;
        $msg->message = $request->message;
        $msg->sent_user = $request->user_id;;
        $msg->save();
      }
    }

    // Capcha Code Image
    private function  code_image() {

      $actual_path = str_replace('project','',base_path());
      $image = imagecreatetruecolor(200, 50);
      $background_color = imagecolorallocate($image, 255, 255, 255);
      imagefilledrectangle($image,0,0,200,50,$background_color);

      $pixel = imagecolorallocate($image, 0,0,255);
      for($i=0;$i<500;$i++) {
        imagesetpixel($image,rand()%200,rand()%50,$pixel);
      }

      $font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
      $allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      $length = strlen($allowed_letters);
      $letter = $allowed_letters[rand(0, $length-1)];
      $word='';
      //$text_color = imagecolorallocate($image, 8, 186, 239);
      $text_color = imagecolorallocate($image, 0, 0, 0);
      $cap_length=6;// No. of character in image
      for ($i = 0; $i< $cap_length;$i++) {
        $letter = $allowed_letters[rand(0, $length-1)];
        imagettftext($image, 25, 1, 35+($i*25), 35, $text_color, $font, $letter);
        $word.=$letter;
      }
      $pixels = imagecolorallocate($image, 8, 186, 239);
      for($i=0;$i<500;$i++) {
        imagesetpixel($image,rand()%200,rand()%50,$pixels);
      }
      session(['captcha_string' => $word]);
      imagepng($image, $actual_path."assets/images/capcha_code.png");
    }

  }
