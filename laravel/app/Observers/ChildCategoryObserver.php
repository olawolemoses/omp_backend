<?php
namespace App\Observers;

use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Childcategory;
use App\Models\Product;

class ChildcategoryObserver
{
    /**
     * Handle the childcategory "created" event.
     *
     * @param  \App\Childcategory  $childcategory
     * @return void
     */
    public function created(Childcategory $childcategory)
    {
        //
    }

    /**
     * Handle the childcategory "updated" event.
     *
     * @param  \App\Childcategory  $childcategory
     * @return void
     */
    public function updated(Childcategory $childcategory)
    {
        //
        // 1. Update Status
        Product::where('childcategory_id', $childcategory->id)->update([
            'status' => $childcategory->status
        ]);       
    }

    /**
     * Handle the childcategory "deleted" event.
     *
     * @param  \App\Childcategory  $childcategory
     * @return void
     */
    public function deleted(Childcategory $childcategory)
    {
        // Delete all products
        Product::where('childcategory_id', $childcategory->id)->delete();
    }

    /**
     * Handle the childcategory "restored" event.
     *
     * @param  \App\Childcategory  $childcategory
     * @return void
     */
    public function restored(Childcategory $childcategory)
    {
        //
    }

    /**
     * Handle the childcategory "force deleted" event.
     *
     * @param  \App\Childcategory  $childcategory
     * @return void
     */
    public function forceDeleted(Childcategory $childcategory)
    {
        //
    }
}
