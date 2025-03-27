<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WelcomeController;
use App\Models\LevelModel;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Monolog\Level;

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

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/level',[LevelController::class,'index']);
// Route::get('/kategori',[KategoriController::class,'index']);
// Route::get('/user',[UserController::class,'index']);

Route::pattern('id', '[0-9]+'); // artinya ketika ada parameter {id}, maka harus berupa angka

Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'postlogin']);

Route::get('register', [AuthController::class, 'register'])->name('register');
Route::post('register', [AuthController::class, 'postregister']);

Route::post('logout', [AuthController::class, 'logout'])->middleware('auth'); //untuk keamanan, logout sebaiknya menggunakan metode POST untuk mencegah logout tidak sengaja (misalnya, jika tautan /logout diakses oleh crawler). 

Route::middleware(['auth'])->group(function () {

    Route::post('/user/update-profile-photo', [UserController::class, 'updateProfilePhoto'])->name('user.updateProfilePhoto');

    Route::middleware(['authorize:ADM,MNG'])->group(function () { // artinya semua route di dalam group ini harus login dulu

        Route::get('/', [WelcomeController::class, 'index']);

        Route::group(['prefix' => 'user'], function () {
            // route statis
            Route::get('/', [UserController::class, 'index']);            //menampilkan halaman awal pada user
            Route::post('/list', [UserController::class, 'list']);        //menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [UserController::class, 'create']);     //menampilkan halaman form tambah user
            Route::post('/', [UserController::class, 'store']);           //menyimpan data user 
            Route::get('/create_ajax', [UserController::class, 'create_ajax']);
            Route::post('/ajax', [UserController::class, 'store_ajax']);
            Route::get('/import', [UserController::class, 'import']);
            Route::post('/import_ajax', [UserController::class, 'import_ajax']);
            Route::get('/export_excel', [UserController::class, 'export_excel']);
            Route::get('/export_pdf', [UserController::class, 'export_pdf']);

            // route dinamis
            Route::get('/{id}', [UserController::class, 'show']);         //menampilkan detail user
            Route::get('/{id}/edit', [UserController::class, 'edit']);    //menampilkan halaman form edit user
            Route::put('/{id}', [UserController::class, 'update']);       //menyimpan perubahan data user
            Route::get('{id}/edit_ajax', [UserController::class, 'edit_ajax']);
            Route::put('{id}/update_ajax', [UserController::class, 'update_ajax']);
            Route::get('{id}/delete_ajax', [UserController::class, 'confirm_ajax']);
            Route::delete('{id}/delete_ajax', [UserController::class, 'delete_ajax']);
            Route::delete('/{id}', [UserController::class, 'destroy']);    //menghapus data user
        });

        Route::group(['prefix' => 'level'], function () {
            // route statis
            Route::get('/', [LevelController::class, 'index']);            //menampilkan halaman awal pada level
            Route::post('/list', [LevelController::class, 'list']);        //menampilkan data level dalam bentuk json untuk datatables
            Route::get('/create', [LevelController::class, 'create']);     //menampilkan halaman form tambah level
            Route::post('/', [LevelController::class, 'store']);           //menyimpan data level baru
            Route::get('/create_ajax', [LevelController::class, 'create_ajax']);
            Route::post('/ajax', [LevelController::class, 'store_ajax']);
            Route::get('/import', [LevelController::class, 'import']);
            Route::post('/import_ajax', [LevelController::class, 'import_ajax']);
            Route::get('/export_excel', [LevelController::class, 'export_excel']);
            Route::get('/export_pdf', [LevelController::class, 'export_pdf']);

            // route dinamis  
            Route::get('/{id}', [LevelController::class, 'show']);         //menampilkan detail level
            Route::get('/{id}/edit', [LevelController::class, 'edit']);    //menampilkan halaman form edit level
            Route::put('/{id}', [LevelController::class, 'update']);       //menyimpan perubahan data level
            Route::get('{id}/edit_ajax', [LevelController::class, 'edit_ajax']);
            // Route::put('{id}/update_ajax', [LevelModel::class, 'update_ajax']);
            Route::put('{id}/update_ajax', [LevelController::class, 'update_ajax']);
            Route::get('{id}/delete_ajax', [LevelController::class, 'confirm_ajax']);
            Route::delete('{id}/delete_ajax', [LevelController::class, 'delete_ajax']);
            Route::delete('/{id}', [LevelController::class, 'destroy']);    //menghapus data level
        });

        Route::group(['prefix' => 'kategori'], function () {
            // route statis
            Route::get('/', [KategoriController::class, 'index']);            //menampilkan halaman awal pada user
            Route::post('/list', [KategoriController::class, 'list']);        //menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [KategoriController::class, 'create']);     //menampilkan halaman form tambah user
            Route::post('/', [KategoriController::class, 'store']);           //menyimpan data user baru
            Route::get('/create_ajax', [KategoriController::class, 'create_ajax']);
            Route::post('/ajax', [KategoriController::class, 'store_ajax']);
            Route::get('/import', [KategoriController::class, 'import']);
            Route::post('/import_ajax', [KategoriController::class, 'import_ajax']);
            Route::get('/export_excel', [KategoriController::class, 'export_excel']);
            Route::get('/export_pdf', [KategoriController::class, 'export_pdf']);

            // route dinamis
            Route::get('/{id}', [KategoriController::class, 'show']);         //menampilkan detail user
            Route::get('/{id}/edit', [KategoriController::class, 'edit']);    //menampilkan halaman form edit user
            Route::put('/{id}', [KategoriController::class, 'update']);       //menyimpan perubahan data user
            Route::get('{id}/edit_ajax', [KategoriController::class, 'edit_ajax']);
            Route::put('{id}/update_ajax', [KategoriController::class, 'update_ajax']);
            Route::get('{id}/delete_ajax', [KategoriController::class, 'confirm_ajax']);
            Route::delete('{id}/delete_ajax', [KategoriController::class, 'delete_ajax']);
            Route::delete('/{id}', [KategoriController::class, 'destroy']);    //menghapus data user
        });

        Route::group(['prefix' => 'supplier'], function () {
            // route statis
            Route::get('/', [SupplierController::class, 'index']);            //menampilkan halaman awal pada user
            Route::post('/list', [SupplierController::class, 'list']);        //menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [SupplierController::class, 'create']);     //menampilkan halaman form tambah user
            Route::post('/', [SupplierController::class, 'store']);           //menyimpan data user baru
            Route::get('/create_ajax', [SupplierController::class, 'create_ajax']);
            Route::post('/ajax', [SupplierController::class, 'store_ajax']);
            Route::get('/import', [SupplierController::class, 'import']);
            Route::post('/import_ajax', [SupplierController::class, 'import_ajax']);
            Route::get('/export_excel', [SupplierController::class, 'export_excel']);
            Route::get('/export_pdf', [SupplierController::class, 'export_pdf']);

            // route dinamis
            Route::get('/{id}', [SupplierController::class, 'show']);         //menampilkan detail user
            Route::get('/{id}/edit', [SupplierController::class, 'edit']);    //menampilkan halaman form edit user
            Route::put('/{id}', [SupplierController::class, 'update']);       //menyimpan perubahan data user
            Route::get('{id}/edit_ajax', [SupplierController::class, 'edit_ajax']);
            Route::put('{id}/update_ajax', [SupplierController::class, 'update_ajax']);
            Route::get('{id}/delete_ajax', [SupplierController::class, 'confirm_ajax']);
            Route::delete('{id}/delete_ajax', [SupplierController::class, 'delete_ajax']);
            Route::delete('/{id}', [SupplierController::class, 'destroy']);    //menghapus data user
        });

        Route::group(['prefix' => 'barang'], function () {
            // route statis
            Route::get('/', [BarangController::class, 'index']);            //menampilkan halaman awal pada user
            Route::post('/list', [BarangController::class, 'list']);        //menampilkan data user dalam bentuk json untuk datatables
            Route::get('/create', [BarangController::class, 'create']);     //menampilkan halaman form tambah user
            Route::post('/', [BarangController::class, 'store']);           //menyimpan data user baru
            Route::get('/create_ajax', [BarangController::class, 'create_ajax']);
            Route::post('/ajax', [BarangController::class, 'store_ajax']);
            Route::get('/import', [BarangController::class, 'import']);
            Route::post('/import_ajax', [BarangController::class, 'import_ajax']);
            Route::get('/export_excel', [BarangController::class, 'export_excel']);
            Route::get('/export_pdf', [BarangController::class, 'export_pdf']);

            // route dinamis
            Route::get('/{id}', [BarangController::class, 'show']);         //menampilkan detail user
            Route::get('/{id}/edit', [BarangController::class, 'edit']);    //menampilkan halaman form edit user
            Route::put('/{id}', [BarangController::class, 'update']);       //menyimpan perubahan data user
            Route::get('{id}/edit_ajax', [BarangController::class, 'edit_ajax']);
            Route::put('{id}/update_ajax', [BarangController::class, 'update_ajax']);
            Route::get('{id}/delete_ajax', [BarangController::class, 'confirm_ajax']);
            Route::delete('{id}/delete_ajax', [BarangController::class, 'delete_ajax']);
            Route::delete('/{id}', [BarangController::class, 'destroy']);    //menghapus data user


        });
    });
});
