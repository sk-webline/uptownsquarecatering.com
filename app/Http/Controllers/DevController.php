<?php

namespace App\Http\Controllers;

use App\Blog;
use App\Brand;
use App\BusinessSetting;
use App\Category;
use App\Color;
use App\Page;
use App\Product;
use App\ProductStock;
use App\ProductType;
use App\Service;
use App\Upload;
use Illuminate\Support\Facades\DB;

class DevController extends Controller
{
    public function findUnusedImagesFromUploadsTable() {
        $all_find_images_array = [];

        $blogs = Blog::select(DB::raw("GROUP_CONCAT(banner) as banner, GROUP_CONCAT(photos) as photos"))->withTrashed()->first();

        $brands = Brand::select(DB::raw("GROUP_CONCAT(logo) as logo, GROUP_CONCAT(header) as header, GROUP_CONCAT(banner) as banner"))->first();

        $business_settings_types = ['png_logo','fixed_header_logo','system_logo_black','system_logo_white','site_icon','payment_method_images','footer_logo','header_logo'];
        $business_settings = BusinessSetting::select(DB::raw("GROUP_CONCAT(value) as images"))->whereIn('type', $business_settings_types)->first();

        $categories = Category::select(DB::raw("GROUP_CONCAT(header) as header, GROUP_CONCAT(banner) as banner, GROUP_CONCAT(icon) as icon, GROUP_CONCAT(b2b_banner) as b2b_banner"))->first();

        $colors = Color::select(DB::raw("GROUP_CONCAT(image) as image"))->first();

        $pages = Page::select(DB::raw("GROUP_CONCAT(banner) as banner, GROUP_CONCAT(meta_image) as meta_image"))->first();

        $product = Product::select(DB::raw("GROUP_CONCAT(photos) as photos, GROUP_CONCAT(thumbnail_img) as thumbnail_img, GROUP_CONCAT(meta_img) as meta_img"))->first();

        $product_stocks = ProductStock::select(DB::raw("GROUP_CONCAT(image) as image"))->first();

        $product_types = ProductType::select(DB::raw("GROUP_CONCAT(banner) as banner"))->first();

        $services = Service::select(DB::raw("GROUP_CONCAT(banner) as banner"))->first();

        array_push($all_find_images_array,
            $blogs->banner,
            $blogs->photos,

            $brands->logo,
            $brands->header,
            $brands->banner,

            $business_settings->images,

            $categories->header,
            $categories->banner,
            $categories->icon,
            $categories->b2b_banner,

            $colors->image,

            $pages->banner,
            $pages->meta_image,

            $product->photos,
            $product->thumbnail_img,
            $product->meta_img,

            $product_stocks->image,

            $product_types->banner,

            $services->banner
        );

        $all_find_images_array = array_filter($all_find_images_array);
//dd($all_find_images_array);
        $all_find_images = implode(',', $all_find_images_array);

        $all_images_array = explode(',', $all_find_images);


        $all_unused_images = Upload::select('id', 'file_name', 'file_original_name')->whereNotIn('id', $all_images_array)->withTrashed()->get();
        $count_deleted_images = 0;
        foreach ($all_unused_images as $key => $image) {
            $file_path = public_path().'/'.$image->file_name;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            if ($image->forceDelete()) {
                $count_deleted_images++;
            }
        }
        die('Success! Deleted '.$count_deleted_images.' images.');

        $all_unused_images = Upload::select('id', 'file_name', 'file_original_name')->whereNotIn('id', $all_images_array)->withTrashed()->get();
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Λίστα με Εικόνες και Ονόματα</title>
        </head>
        <body>
        <h1>Λίστα με Εικόνες και Ονόματα</h1>
        <ol>
            <?php foreach ($all_unused_images as $key => $image) { ?>
                <li>
                    <img src="/public/<?php echo $image['file_name']; ?>" alt="<?php echo $image->file_original_name; ?>" width="250px">
                    <p>Εικόνα <?php echo $key+1; ?> - <?php echo $image->file_original_name; ?> - <?php echo $image->id; ?></p>
                </li>
            <?php } ?>
        </ol>
        </body>
        </html>


        <?php

//        dd($all_unused_images, $all_images_array);
    }
}
