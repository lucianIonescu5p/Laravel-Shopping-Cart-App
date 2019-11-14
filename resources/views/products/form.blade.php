<div>
    <label for="title">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ $product ?? '' ? $product->title : '' }}">
</div>

<div>
    <label for="description">{{ __('Description') }}</label>
    <textarea name="description" cols="30" rows="10">{{ $product ?? '' ? $product->description : '' }}</textarea>
</div>

<div>
    <label for="price">{{ __('Price') }}</label>
    <input type="text" name="price" value="{{ $product ?? '' ? $product->price : '' }}">
</div>

<div>
    <label for="image">{{ __('Choose an image') }}</label>
    <input type="file" name="image">
</div>

<div>
    <input type="submit" name="submit" value="Submit">
</div>