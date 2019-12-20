<?php

	namespace App\Http\Controllers\Front;

	use App\Http\Controllers\Controller;
	use App\Category;
	use App\Childcategory;
	use App\Comment;
	use App\Currency;
	use App\Order;
	use App\Product;
	use App\ProductClick;
	use App\Rating;
	use App\Reply;
	use App\Report;
	use App\Subcategory;
	use Auth;
	use Carbon\Carbon;
	use Illuminate\Http\Request;
	use Illuminate\Support\Collection;
	use Session;
	use Illuminate\Support\Facades\Input;
	use Validator;


	class CatalogCtrl extends Controller {


		// -------------------------------- CATEGORY SECTION ----------------------------------------

		public function category(Request $request, $slug) {

			$this -> code_image();
			$sort = "";
			$cat = Category:: where('slug', '=', $slug) -> first();
			$oldcats = $cat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
			$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
			// Search By Price

			if (!empty($request -> min) || !empty($request -> max)) {
				$min = $request -> min;
				$max = $request -> max;
				$$oldcats = $cat -> products() -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
				$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				
				return response() -> json([
					'success' => true,
					'data' => compact('cat', 'cats', 'sort', 'min', 'max')
				], 201);
				
			}

			// Search By Sort

			if (!empty($request -> sort)) {
				$sort = $request -> sort;
				if ($sort == "new") {
					$oldcats = $cat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "old") {
					$oldcats = $cat -> products() -> where('status', '=', 1) -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "low") {
					$oldcats = $cat -> products() -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				}
				else if ($sort == "high") {
					$oldcats = $cat -> products() -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
				}

				return response() -> json([
					'success' => true,
					'data' => compact('cat', 'cats', 'sort')
				], 201);
			}
			// Otherwise Go To Category
			return response() -> json([
				'success' => true,
				'data' => compact('cat', 'cats', 'sort')
			], 201);
		}

		public function subcategory(Request $request, $slug1, $slug2) {
			$this -> code_image();
			$sort = "";
			$subcat = Subcategory:: where('slug', '=', $slug2) -> first();
			$oldcats = $subcat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
			$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);

			// Search By Price

			if (!empty($request -> min) || !empty($request -> max)) {
				$min = $request -> min;
				$max = $request -> max;
				$oldcats = $subcat -> products() -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
				$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);

				return response() -> json([
					'success' => true,
					'data' => compact('subcat', 'cats', 'sort', 'min', 'max')
				], 201);
			}

			// Search By Sort

			if (!empty($request -> sort)) {
				$sort = $request -> sort;
				if ($sort == "new") {
					$oldcats = $subcat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "old") {
					$oldcats = $subcat -> products() -> where('status', '=', 1) -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "low") {
					$oldcats = $subcat -> products() -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				}
				else if ($sort == "high") {
					$oldcats = $subcat -> products() -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
				}
				
				return response() -> json([
					'success' => true,
					'data' => compact('subcat', 'cats', 'sort')
				], 201);

			}

			// Otherwise Go To Category

			if ($request -> ajax()) {
				return view('front.pagination.category', compact('subcat', 'sort', 'cats'));
			}

			return response() -> json([
				'success' => true,
				'data' => compact('subcat', 'sort', 'cats')
			], 201);

		}

		public function childcategory(Request $request, $slug1, $slug2, $slug3) {
			$this -> code_image();
			$sort = "";
			$childcat = Childcategory:: where('slug', '=', $slug3) -> first();
			$oldcats = $childcat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
			$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);

			// Search By Price

			if (!empty($request -> min) || !empty($request -> max)) {
				$min = $request -> min;
				$max = $request -> max;
				$oldcats = $childcat -> products() -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
				$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				if ($request -> ajax()) {
					return view('front.pagination.category', compact('childcat', 'cats', 'sort', 'min', 'max'));
				}
				return view('front.category', compact('childcat', 'cats', 'sort', 'min', 'max'));
			}

			// Search By Sort

			if (!empty($request -> sort)) {
				$sort = $request -> sort;
				if ($sort == "new") {
					$oldcats = $childcat -> products() -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "old") {
					$oldcats = $childcat -> products() -> where('status', '=', 1) -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "low") {
					$oldcats = $childcat -> products() -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				}
				else if ($sort == "high") {
					$oldcats = $childcat -> products() -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
					$cats = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
				}
				if ($request -> ajax()) {
					return view('front.pagination.category', compact('childcat', 'cats', 'sort'));
				}

				return view('front.category', compact('childcat', 'cats', 'sort'));
			}

			// Otherwise Go To Category
			return response() -> json([
				'success' => true,
				'data' => compact('childcat', 'sort', 'cats')
			], 201);

		}



		public function tag(Request $request, $tag) {
			$this -> code_image();
			$tags = $tag;
			$sort = '';
			$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
			$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);

			// Search By Price

			if (!empty($request -> min) || !empty($request -> max)) {
				$min = $request -> min;
				$max = $request -> max;
				$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
				$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				if ($request -> ajax()) {
					return view('front.pagination.tags', compact('products', 'tags', 'sort', 'min', 'max'));
				}
				return view('front.tags', compact('products', 'tags', 'sort', 'min', 'max'));
			}

			// Search By Sort

			if (!empty($request -> sort)) {
				$sort = $request -> sort;
				if ($sort == "new") {
					$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "old") {
					$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
				}
				else if ($sort == "low") {
					$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
				}
				else if ($sort == "high") {
					$oldcats = Product:: where('tags', 'like', '%'.$tags. '%') -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
				}

				return response() -> json([
					'success' => true,
					'data' => compact('products', 'tags', 'sort')
				], 201);
				
 			}

			// Otherwise Go To Tags
			return response() -> json([
				'success' => true,
				'data' => compact('products', 'tags', 'sort')
			], 201);
			
		}


		public function search(Request $request) {
			$this -> code_image();
			if (!empty($request -> cat_id)) {

				// *********************** CATALOG SEARCH SECTION ******************

				$cat_id = $request -> cat_id;
				$sort = '';
				// Search By Sort

				if (!empty($request -> sort)) {
					$min = $request -> min;
					$max = $request -> max;
					$sort = $request -> sort;
					if ($sort == "new") {
						$oldcats = Product:: whereIn('category_id', $cat_id) -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('id', 'desc') -> get();
						$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
					}
					else if ($sort == "old") {
						$oldcats = Product:: whereIn('category_id', $cat_id) -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> get();
						$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
					}
					else if ($sort == "low") {
						$oldcats = Product:: whereIn('category_id', $cat_id) -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
						$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
					}
					else if ($sort == "high") {
						$oldcats = Product:: whereIn('category_id', $cat_id) -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'desc') -> get();
						$products = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
					}

					return response() -> json([
						'success' => true,
						'data' => compact('products', 'cat_id', 'sort', 'min', 'max')
					], 201);
				}



				// Search By Price

				if (!empty($request -> min) || !empty($request -> max)) {
					$min = $request -> min;
					$max = $request -> max;
					$oldcats = Product:: whereIn('category_id', $cat_id) -> where('status', '=', 1) -> whereBetween('price', [$min, $max]) -> orderBy('price', 'asc') -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);

					return response() -> json([
						'success' => true,
						'data' => compact('products', 'cat_id', 'sort', 'min', 'max')
					], 201);
				}


				// Otherwise Go To Tags
				return response() -> json([
					'success' => true,
					'data' => compact('products', 'cat_id', 'sort')
				], 201);
				
				// *********************** CATALOG SEARCH SECTION ENDS ******************

			}

			else {

				// *********************** NORMAL SEARCH SECTION ******************
				$sort = '';
				$category_id = $request -> category_id;
				$search = $request -> search;
				if ($category_id == 0) {

					// SORT SEARCH
					if (!empty($request -> sort)) {
						$sort = $request -> sort;
						if ($sort == "new") {
							$oldcats = Product:: where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
						}
						else if ($sort == "old") {
							$oldcats = Product:: where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
						}
						else if ($sort == "low") {
							$oldcats = Product:: where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
						}
						else if ($sort == "high") {
							$oldcats = Product:: where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
						}
						if ($request -> ajax()) {
							return view('front.pagination.search', compact('products', 'cat_id', 'sort', 'min', 'max'));
						}

						return view('front.search', compact('products', 'cat_id', 'sort', 'min', 'max'));
					}
					// SORT SEARCH ENDS

					$oldcats = Product:: where('status', '=', 1) -> whereRaw('MATCH (name) AGAINST (? IN BOOLEAN MODE)', array($search)) -> get();
					//$oldcats = Product::where('name', 'like', '%' . $search . '%')->where('status','=',1)->orderBy('id','desc')->get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);

					return response()->json([
						'success' => true,
						'data' => compact('products', 'search', 'category_id', 'sort')
					], 201);

				}
				else {

					// SORT SEARCH
					if (!empty($request -> sort)) {
						$sort = $request -> sort;
						if ($sort == "new") {
							$oldcats = PProduct:: where('category_id', 'like', '%'.$category_id. '%') -> where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
						}
						else if ($sort == "old") {
							$oldcats = Product:: where('category_id', 'like', '%'.$category_id. '%') -> where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> where('status', '=', 1) -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);
						}
						else if ($sort == "low") {
							$oldcats = Product:: where('category_id', 'like', '%'.$category_id. '%') -> where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('price', 'asc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> sortBy('price') -> paginate(9);
						}
						else if ($sort == "high") {
							$oldcats = Product:: where('category_id', 'like', '%'.$category_id. '%') -> where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('price', 'desc') -> get();
							$products = (new Collection(Product:: filterProducts($oldcats))) -> sortByDesc('price') -> paginate(9);
						}

						return response()->json([
							'success' => true,
							'data' => compact('products', 'search', 'category_id', 'sort')
						], 201);

					}
					// SORT SEARCH ENDS


					$oldcats = Product:: where('category_id', 'like', '%'.$category_id. '%') -> where('name', 'like', '%'.$search. '%') -> where('status', '=', 1) -> orderBy('id', 'desc') -> get();
					$products = (new Collection(Product:: filterProducts($oldcats))) -> paginate(9);

					return response()->json([
						'success' => true,
						'data' => compact('products', 'search', 'category_id', 'sort')
					], 201);

				}


				// *********************** NORMAL SEARCH SECTION ENDS ******************

			}

		}


		// -------------------------------- CATEGORY SECTION ENDS----------------------------------------


		// -------------------------------- PRODUCT DETAILS SECTION ----------------------------------------

		public function product($slug) {
			// $this -> code_image();
			$productt = Product:: where('slug', '=', $slug) -> first();
			$productt -> views+=1;
			$productt -> update();
			if (Session:: has('currency'))
			{
				$curr = Currency:: find(Session:: get('currency'));
			}
					else
					{
					$curr = Currency:: where('is_default', '=', 1) -> first();
				}
				
				$product_click = new ProductClick;
				$product_click -> product_id = $productt -> id;
				$product_click -> date = Carbon:: now() -> format('Y-m-d');
				$product_click -> save();

				if ($productt -> user_id != 0) {
					$vendors = Product:: where('status', '=', 1) -> where('user_id', '=', $productt -> user_id) -> take(8) -> get();
				}
				else {
					$vendors = Product:: where('status', '=', 1) -> where('user_id', '=', 0) -> take(8) -> get();
				}

				return response()->json([
					'success' => true, 
					'data' => compact('productt', 'curr', 'vendors')
				], 201);

		}


// -------------------------------- PRODUCT DETAILS SECTION ----------------------------------------

		public function report(Request $request) {

			//--- Validation Section
			$rules = [
				'note' => 'max:400',
			];
			$customs = [
				'note.max' => 'Note Must Be Less Than 400 Characters.',
			];
			$validator = Validator:: make(Input:: all(), $rules, $customs);
			if ($validator -> fails()) {
				return response() -> json(array('errors' => $validator -> getMessageBag() -> toArray()));
			}
			//--- Validation Section Ends

			//--- Logic Section
			$data = new Report;
			$input = $request -> all();
			$data -> fill($input) -> save();
			//--- Logic Section Ends

			//--- Redirect Section  
			$msg = 'New Data Added Successfully.';
			return response() -> json($msg);
			//--- Redirect Section Ends  

		}

		public function affProductRedirect($slug) {
			$product = Product:: where('slug', '=', $slug) -> first();
			//        $product->views+=1;
			//        $product->update();


			return redirect($product -> affiliate_link);

		}

		public function quick($id) {
			$product = Product:: findOrFail($id);
			if (Session:: has('currency'))
			{
				$curr = Currency:: find(Session:: get('currency'));
			}
						else
			{
				$curr = Currency:: where('is_default', '=', 1) -> first();
			}

			return response()->json([
				'success' => true,
				'data' => compact('product', 'curr')
			], 201);

		}

		// -------------------------------- PRODUCT DETAILS SECTION ENDS----------------------------------------

		// -------------------------------- PRODUCT COMMENT SECTION ----------------------------------------

		public function comment(Request $request) {
			$comment = new Comment;
			$input = $request -> all();
			$comment -> fill($input) -> save();
			$comments = Comment:: where('product_id', '=', $request -> product_id) -> get() -> count();
			$data[0] = $comment -> user -> photo ? url('assets/images/users/'.$comment -> user -> photo) : url('assets/images/noimage.png');
			$data[1] = $comment -> user -> name;
			$data[2] = $comment -> created_at -> diffForHumans();
			$data[3] = $comment -> text;
			$data[4] = $comments;
			$data[5] = route('product.comment.delete', $comment -> id);
			$data[6] = route('product.comment.edit', $comment -> id);
			$data[7] = route('product.reply', $comment -> id);
			$data[8] = $comment -> user -> id;
			return response() -> json($data);
		}

		public function commentedit(Request $request, $id) {
			$comment = Comment:: findOrFail($id);
			$comment -> text = $request -> text;
			$comment -> update();
			return response() -> json($comment -> text);
		}

		public function commentdelete($id) {
			$comment = Comment:: findOrFail($id);
			if ($comment -> replies -> count() > 0) {
				foreach($comment -> replies as $reply) {
					$reply -> delete ();
				}
			}
			$comment -> delete ();
		}

		// -------------------------------- PRODUCT COMMENT SECTION ENDS ----------------------------------------

		// -------------------------------- PRODUCT REPLY SECTION ----------------------------------------

		public function reply(Request $request, $id) {
			$reply = new Reply;
			$input = $request -> all();
			$input['comment_id'] = $id;
			$reply -> fill($input) -> save();
			$data[0] = $reply -> user -> photo ? url('assets/images/users/'.$reply -> user -> photo) : url('assets/images/noimage.png');
			$data[1] = $reply -> user -> name;
			$data[2] = $reply -> created_at -> diffForHumans();
			$data[3] = $reply -> text;
			$data[4] = route('product.reply.delete', $reply -> id);
			$data[5] = route('product.reply.edit', $reply -> id);
			return response() -> json($data);
		}

		public function replyedit(Request $request, $id) {
			$reply = Reply:: findOrFail($id);
			$reply -> text = $request -> text;
			$reply -> update();
			return response() -> json($reply -> text);
		}

		public function replydelete($id) {
			$reply = Reply:: findOrFail($id);
			$reply -> delete ();
		}

		// -------------------------------- PRODUCT REPLY SECTION ENDS----------------------------------------


		// ------------------ Rating SECTION --------------------

		public function reviewsubmit(Request $request) {
			$ck = 0;
			$orders = Order:: where('user_id', '=', $request -> user_id) -> where('status', '=', 'completed') -> get();

			foreach($orders as $order)
			{
				$cart = unserialize(bzdecompress(utf8_decode($order -> cart)));
				foreach($cart -> items as $product)
				{
					if ($request -> product_id == $product['item']['id']) {
						$ck = 1;
						break;
					}
				}
			}
			if ($ck == 1) {
				$user = Auth:: guard('web') -> user();
				$prev = Rating:: where('product_id', '=', $request -> product_id) -> where('user_id', '=', $user -> id) -> get();
				if (count($prev) > 0) {
					return response() -> json(array('errors' => [0 => 'You Have Reviewed Already.']));
				}
				$Rating = new Rating;
				$Rating -> fill($request -> all());
				$Rating['review_date'] = date('Y-m-d H:i:s');
				$Rating -> save();
				$data[0] = 'Your Rating Submitted Successfully.';
				$data[1] = Rating:: rating($request -> product_id);
				return response() -> json($data);
			}
			else {
				return response() -> json(array('errors' => [0 => 'Buy This Product First']));
			}
		}


		public function reviews($id) {
			$productt = Product:: find($id);

			return response()->json([
				'success' => true,
				'data' => compact('productt', 'id')
			], 201);

		}

		// ------------------ Rating SECTION ENDS --------------------

		// Capcha Code Image
		private function code_image() {
			$actual_path = str_replace('project', '', base_path());
			$image = imagecreatetruecolor(200, 50);
			$background_color = imagecolorallocate($image, 255, 255, 255);
			imagefilledrectangle($image, 0, 0, 200, 50, $background_color);

			$pixel = imagecolorallocate($image, 0, 0, 255);
			for ($i = 0; $i < 500; $i++) {
				imagesetpixel($image, rand() % 200, rand() % 50, $pixel);
			}

			$font = $actual_path.'assets/front/fonts/NotoSans-Bold.ttf';
			$allowed_letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			$length = strlen($allowed_letters);
			$letter = $allowed_letters[rand(0, $length - 1)];
			$word = '';
			//$text_color = imagecolorallocate($image, 8, 186, 239);
			$text_color = imagecolorallocate($image, 0, 0, 0);
			$cap_length = 6;// No. of character in image
			for ($i = 0; $i < $cap_length; $i++) {
				$letter = $allowed_letters[rand(0, $length - 1)];
				imagettftext($image, 25, 1, 35 + ($i * 25), 35, $text_color, $font, $letter);
				$word.= $letter;
			}
			$pixels = imagecolorallocate($image, 8, 186, 239);
			for ($i = 0; $i < 500; $i++) {
				imagesetpixel($image, rand() % 200, rand() % 50, $pixels);
			}
			session(['captcha_string' => $word]);
			imagepng($image, $actual_path."assets/images/capcha_code.png");
		}

		public function relatedproduct($id, $category) {

			$productt =	Product::where('category_id', '=', $category)
						->where('status','=',1)
						->where('id','!=',$id)->take(4)->latest()->get();

			return response()->json([
				'success' => true,
				'data' => compact('productt')
			], 201);
		}

}