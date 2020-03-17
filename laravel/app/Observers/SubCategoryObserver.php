<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\SubCategory;

use App\Models\Product;

class SubCategoryObserver
{
    /**
     * Handle the subcategory "created" event.
     *
     * @param  \App\SubCategory  $subcategory
     * @return void
     */
    public function created(SubCategory $subcategory)
    {
        //
    }

    /**
     * Handle the subcategory "updated" event.
     *
     * @param  \App\SubCategory  $subcategory
     * @return void
     */
    public function updated(SubCategory $subcategory)
    {
        //
        // 1. Update Status
        Product::where('subcategory_id', $subcategory->id)->update([
            'status' => $subcategory->status
        ]);       
    }

    /**
     * Handle the subcategory "deleted" event.
     *
     * @param  \App\SubCategory  $subcategory
     * @return void
     */
    public function deleted(SubCategory $subcategory)
    {
        // Delete all products
        Product::where('subcategory_id', $subcategory->id)->delete();
    }

    /**
     * Handle the subcategory "restored" event.
     *
     * @param  \App\SubCategory  $subcategory
     * @return void
     */
    public function restored(SubCategory $subcategory)
    {
        //
    }

    /**
     * Handle the subcategory "force deleted" event.
     *
     * @param  \App\SubCategory  $subcategory
     * @return void
     */
    public function forceDeleted(SubCategory $subcategory)
    {
        //
    }
}
