@extends("layouts.app");
@section("content")
<form method="POST" action="{{ route('books.reviews.store', $book) }}">
    @csrf
    <label for="review">Review</label>
    <textarea name="review" id="review" required class="input mb-4"></textarea>

    <label for="rating">Rating</label>

    <select name="rating" id="rating" class="input mb-4" required>
      <option value="">Select a Rating</option>
      @for ($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}">{{ $i }}</option>
      @endfor
    </select>

    <button type="submit" class="btn">Add Review</button>
  </form>
@endsection