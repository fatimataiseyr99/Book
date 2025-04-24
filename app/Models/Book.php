<?php


namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
   
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    use HasFactory;
    protected $table="books";
    protected $Fillable=["id", "title" , "author"  ];
  

public function  reviews(){
    return $this->hasMany (Review::class);

}
public function scopeTitle( Builder $query , string $title){
    return $query->where( "title" ,"like" , "%"  .$title  . "%
    "); 
}

public function scopeWithReviewsCount(Builder $query, $from = null, $to = null): Builder|QueryBuilder
{
    return $query->withCount([
        'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
    ]);
  
}

public function scopeWithAvgRating(Builder $query, $from = null, $to = null): Builder|QueryBuilder
{
    return $query->withAvg([
        'reviews' => fn(Builder $q) => $this->dateRangeFilter($q, $from, $to)
   
    ], 'rating');
}
public function scopePopular(Builder $query, $from = null, $to = null): Builder|QueryBuilder
{
    return $query->withReviewsCount()
        ->orderBy('reviews_count', 'desc');
}
public function scopeHighestRated(Builder $query, $from = null, $to = null): Builder|QueryBuilder
{
    return $query->withAvgRating()
        ->orderBy('reviews_avg_rating', 'desc');
}

public function scopeMinReviews(Builder $query, int $minReviews): Builder|QueryBuilder
{
    return $query->having('reviews_count', '>=', $minReviews);
}

private function dateRangeFilter(Builder $query, $from = null, $to = null)
{
    if ($from && !$to) {
        $query->where('created_at', '>=', $from);
    } elseif (!$from && $to) {
        $query->where('created_at', '<=', $to);
    } elseif ($from && $to) {
        $query->whereBetween('created_at', [$from, $to]);
    }
}

public function scopePopularLast6Months(Builder $query): Builder|QueryBuilder
{
    return $query->popular(now()->subMonths(6), now())
        ->highestRated(now()->subMonths(6), now())
        ->minReviews(5);
}

public function scopePopularLastMonth(Builder $query): Builder|QueryBuilder
{
    return $query->popular(now()->subMonth(), now())
        ->highestRated(now()->subMonth(), now())
        ->minReviews(2);
}public function scopeHighestRatedLastMonth(Builder $query): Builder|QueryBuilder
{
    return $query->highestRated(now()->subMonth(), now())
        ->popular(now()->subMonth(), now())
        ->minReviews(2);
}

public function scopeHighestRatedLast6Months(Builder $query): Builder|QueryBuilder
{
    return $query->highestRated(now()->subMonths(6), now())
        ->popular(now()->subMonths(6), now())
        ->minReviews(5);
}
protected static function booted()
{
    static::updated(
        fn(Book $book) => cache()->forget('book:' . $book->id)
    );
    static::deleted(
        fn(Book $book) => cache()->forget('book:' . $book->id)
    );
}
}