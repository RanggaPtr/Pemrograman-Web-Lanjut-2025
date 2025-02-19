<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PhotoController; 
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route non parameter
// Route::get('/', function () {
//     return view(view: 'welcome');
// });

Route::get('/hello', [WelcomeController::class,'hello']);


Route::get('/world', function () {
    return "World";
});

Route::get('/welcome', function () {
    return ('welcome');
});

Route::get('/about', function () {
    return ('nim: 2341720079,nama: Rangga Putra');
});


// Route berparameter
Route::get('/user/{name}', function ($name) {
    return 'Nama Saya:' . $name;
});

Route::get('/posts/{post}/comments/{comment}', function ($post, $comment) {
    return 'Post ID:' . $post . ' Comment ID:' . $comment;
});

Route::get('/articles/{id}', function ($id) {
    return 'Halaman Artikel 
dengan ID : ' . $id;
});


//optional parameter
Route::get('/users/{nama?}', function ($nama = null) {
    return 'Nama Saya : ' . $nama;
});

//modifikasi kode program
Route::get('/', [HomeController::class, 'index']);
Route::get('/about',[AboutController::class, 'about']);
Route::get('/articles/{id}',[ArticleController::class, 'index']);

Route::resource('photos', PhotoController::class); 

// modifikasi view akses routes tanpa controller
// Route::get('/greeting', function () { 
//     return view('blog.hello', ['name' => 'Andi']); 
//     }); 


//menampilkan view menggunakan controller
Route::get('/greeting', [WelcomeController::class, 
'greeting']); 


// Route Name
// Route::get('/user/profile', function () {
    
// })->name('profile');
// Route::get(
//     '/user/profile',
//     [UserProfileController::class, 'show']
// )->name('profile');

// // Generating URLs... 
// $url = route('profile');

// // Generating Redirects... 
// return redirect()->route('profile');


// Route Group
// Route::middleware(['first', 'second'])->group(function () {
//     Route::get('/', function () {
//         // Uses first & second middleware... 
//     });

//     Route::get('/user/profile', function () {
//         // Uses first & second middleware... 
//     });
// });

// Route::domain('{account}.example.com')->group(function () {
//     Route::get('user/{id}', function ($account, $id) {
//         // 
//     });
// });

// Route::middleware('auth')->group(function () {
//     Route::get('/user', [UserController::class, 'index']);
//     Route::get('/post', [PostController::class, 'index']);
//     Route::get('/event', [EventController::class, 'index']);

// });


// Route Prefixes
// Route::prefix('admin')->group(function () { 
//     Route::get('/user', [UserController::class, 'index']); 
//     Route::get('/post', [PostController::class, 'index']); 
//     Route::get('/event', [EventController::class, 'index']); 
    
//    });


// // Redirect Routes
// Route::redirect('/here', '/there'); 