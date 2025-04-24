<?php
use App\Http\Controllers\Reviewcontroller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Bookcontroller;

Route::get('/', function () {
    return view('welcome');
});
Route::get("/books" , [ bookcontroller::class , "index"])->name("books.index");
Route::get("books/{book}
", [ bookcontroller::class , "show"] )->name("books.show");
Route::resource("books.reviews", Reviewcontroller::class)
->scoped([
    "review"=>"book"
]) ->only("create", "store");