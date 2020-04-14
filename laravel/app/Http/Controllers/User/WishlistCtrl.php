<?php

  namespace App\Http\Controllers\User;

  use Illuminate\Http\Request;
  use App\Http\Controllers\Controller;
  use App\Models\Wishlist;
  use App\Models\Product;
  use Auth;

  class WishlistCtrl extends Controller 
  {

    public function wishlists (Request $request) {

      $sort = '';
      $user = Auth::user();

      // Search By Sort
      if (!empty($request->sort)) {
        # code...
        $sort = $request->sort;
        $wishes = WishList::where('user_id','=',$user->id)->pluck('product_id');

        if ($sort == 'new') {
          # code...
          $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('id','desc')->paginate(8);
        }
        else if ($sort == "old") {
          $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->paginate(8);
        }
        else if ($sort == "low") {
          $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('price','asc')->paginate(8);
        }
        else if ($sort == "high") {
          $wishlists = Product::where('status','=',1)->whereIn('id',$wishes)->orderBy('price','desc')->paginate(8);
        }
              
        return response()->json([
          'success' => true,
          'data' => compact('wishlists', 'sort')
        ], 201);
      }

      $wishlists = Wishlist::where('user_id','=',$user->id)->paginate(8);

      return response()->json([
        'success' => true,
        'data' => compact('wishlists', 'sort')
      ], 201);

    }

    public function addwish($id) {

      $user = Auth::user();
      $data[0] = 0;
      $ck = Wishlist::where('user_id','=',$user->id)->where('product_id','=',$id)->get();
     
      if (count($ck) > 0) {
        return response()->json([
          'success' => true,
          'message' => 'Succesfully Added'
        ], 201);
      }
      
      $wish = new Wishlist();
      $wish->user_id = $user->id;
      $wish->product_id = $id;
      $wish->save();
      $data[0] = 1; 
      $data[1] = count($user->wishlists);
      return response()->json([
        'success' => true,
        'msg' => 'Successfully Added'
      ], 201); 
    }

    public function removewish() {

      $user = Auth::user();
      $wish = Wishlist::findOrFail($id);
      $wish->delete();        
      $data[0] = 1; 
      $data[1] = count($user->wishlists);

      return response()->json([
        'success' => true,
        'msg' => 'Successfully Removed'
      ], 201);
    }

    //fetch all product
    public function show(Request $request, $product_id )
    {
      
        $product = Wishlist::where('product_id',$product_id)->get();
        

        if(!$product){
            return response() ->json([
                'status' =>false,
                'message' => 'product could not be found',
             
            ]);
        }
        else{
            return response() ->json([
                'status' =>true,
                'data' => [
                    'product' =>$product
                   
                ],
            ], 200);
        }

    }

     //fetch all vendor users
     public function vendoWish(Request $request)
    {
    
        // $vendor = Wishlist::join('products','products.id','=','wishlists.product_id')->select('wishlists.*')->get();
        $user = Auth::user()->id;
        
        // $vendor = Product::join('wishlists','wishlists.product_id', '=', 'products.id')->select('products.*')->get();
        $vendor=Wishlist::join('products', function ($join) use ($user) {
          $join->on('wishlists.product_id', '=', 'products.id')
               ->where('products.user_id','=',$user);
         })->get();

        if(!$vendor){
            return response() ->json([
                'status' =>false,
                'message' => 'vendor could not be found',
             
            ]);
        }
        else{
        
            return response() ->json([
                'status' =>true,
                'data' => [
                  'product' =>$vendor
                 
              ],

               
            ], 200);
        }

    }
  }