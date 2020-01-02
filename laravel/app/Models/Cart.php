<?php

  namespace App\Models;
  use Session;

  use Illuminate\Database\Eloquent\Model;

  class Cart extends Model
  {
    public $items = null;
    public $total_quantity = 0;
    public $totalPrice = 0;
    
    public function __construct($oldcart) {
     
      if ($oldcart) {
        $this->items = $oldCart->items;
        $this->total_quantity = $oldCart->total_quantity;
        $this->totalPrice = $oldCart->totalPrice;
      }
    }

    // **************** ADD TO CART *******************
    public function add($item, $id, $size) {
      
      $size_cost = 0;
      $storedItem = ['qty' => 0,'size_key' => 0, 'size_qty' =>  $item->size_qty,'size_price' => $item->size_price, 'size' => $item->size, 'color' => $item->color, 'stock' => $item->stock, 'price' => $item->price, 'item' => $item, 'license' => '', 'dp' => '0'];

      if ($item->type == 'Physical') {
        if ($this->items) {
          if (array_key_exists($id.$size, $this->items)) {
            $storedItem = $this->item[$id.$size];
          }
        }
      }
      else {
        if ($this->items) {
          if (array_key_exists($id.$size, $this->items)) {
            $storedItem = $this->item[$id.$size];
            $storedItem['dp'] = 1;
          }
        }
      }

      $storedItem['qty']++;
      $stck = (string)$item->stock;
      if ($stck != null) {
        $storedItem['stock']--;
      }
      if (!empty($item->size)) {
        $storedItem['size'] = $item->size[0];
      }
      if (!empty($size)) {
        $storedItem['size'] = $size;
      }
      if (!empty($item->size_qty)) {
        $storedItem['size_qty'] = $item->size_qty[0];
      }
      if ($item->size_price != null) {
        $storedItem['size_price'] = $item->size_price[0];
      }
      if ($item->color != null) {
        $storedItem['color'] = $item->color[0];
      }

      $item->price += $size_cost;
      
      if (!empty($item->whole_sell_qty)) {
        foreach(array_combine($item->whole_sell_qty,$item->whole_sell_discount) as $whole_sell_qty => $whole_sell_discount) {
          if ($storedItem['qty'] == $whole_sell_qty) {
            $whole_discount[$id.$size] = $whole_sell_discount;
            Session::put('current_discount', $whole_discount);
            break;
          }
        }
        if (Session::has('current_discount')) {
          $data = Session::get('current_discount');
          if (array_key_exists($id.$size, $data)) {
            $discount = $item->price * ($data[$id.$size] / 100);
            $item->price = $item->price - $discount;
          }
        }
      }
      
      $storedItem['price'] = $item->price * $storedItem['qty'];
      $this->items[$id.$size] = $storedItem;
      $this->total_quantity++;
    }

    // **************** ADD TO CART MULTIPLE *******************
    public function addnum($item, $id, $qty, $size, $color, $size_qty, $size_price, $size_key) {
      
      $size_cost = 0;
      $storedItem = ['qty' => 0,'size_key' => 0, 'size_qty' =>  $item->size_qty,'size_price' => $item->size_price, 'size' => $item->size, 'color' => $item->color, 'stock' => $item->stock, 'price' => $item->price, 'item' => $item, 'license' => '', 'dp' => '0'];

      if ($item->type == 'Physical') {
        if ($this->items) {
          if (array_key_exists($id.$size, $this->items)) {
            $storedItem = $this->item[$id.$size];
          }
        }
      }
      else {
        if ($this->items) {
          if (array_key_exists($id.$size, $this->items)) {
            $storedItem = $this->item[$id.$size];
            $storedItem['dp'] = 1;
          }
        }
      }

      $storedItem['qty'] = $storedItem['qty'] + $qty;
      $stck = (string)$item->stock;
      if ($stck != null) {
        $storedItem['stock']--;
      }
      if (!empty($item->size)) {
        $storedItem['size'] = $item->size[0];
      }
      if (!empty($size)) {
        $storedItem['size'] = $size;
      }
      if (!empty($size_key)) {
        $storedItem['size_key'] = $size_key;
      }
      if (!empty($item->size_qty)) {
        $storedItem['size_qty'] = $item->size_qty[0];
      }
      if (!empty($size_qty)) {
        $storedItem['size_qty'] = $size_qty;
      }
      if (!empty($item->size_price)) {
        $storedItem['size_price'] = $item->size_price[0];
        $size_cost = $item->size_price[0];
      }
      if (!empty($size_price)) {
        $storedItem['size_price'] = $size_price;
        $size_cost = $size_price[0];
      }
      if (!empty($item->color)) {
        $storedItem['color'] = $item->color[0];
      }
      if (!empty($color)) {
        $storedItem['color'] = $color;
      }

      $item->price += $size_cost;
            
      if (!empty($item->whole_sell_qty)) {
        foreach(array_combine($item->whole_sell_qty,$item->whole_sell_discount) as $whole_sell_qty => $whole_sell_discount) {
          if ($storedItem['qty'] == $whole_sell_qty) {
            $whole_discount[$id.$size] = $whole_sell_discount;
            Session::put('current_discount', $whole_discount);
            break;
          }
        }
        if (Session::has('current_discount')) {
          $data = Session::get('current_discount');
          if (array_key_exists($id.$size, $data)) {
            $discount = $item->price * ($data[$id.$size] / 100);
            $item->price = $item->price - $discount;
          }
        }
      }
      
      $storedItem['price'] = $item->price * $storedItem['qty'];
      $this->items[$id.$size] = $storedItem;
      $this->total_quantity++;
    }
    // **************** ADD TO CART MULTIPLE ENDS *******************

    // **************** REDUCING QUANTITY ENDS *******************

    public function updateLicense($id,$license) {

        $this->items[$id]['license'] = $license;
    }

    public function updateColor($item, $id,$color) {

        $this->items[$id]['color'] = $color;
    }

    public function removeItem($id) {
        $this->totalQty -= $this->items[$id]['qty'];
        $this->totalPrice -= $this->items[$id]['price'];
        unset($this->items[$id]);
            if(Session::has('current_discount')) {
                    $data = Session::get('current_discount');
                if (array_key_exists($id, $data)) {
                    unset($data[$id]);
                    Session::put('current_discount',$data);
                }
            }

    }
    
  }
  