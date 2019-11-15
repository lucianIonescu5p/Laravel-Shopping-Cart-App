<div>
    <label for="title">{{ __('Title') }}</label>
    <input type="text" name="title" value="{{ $product ?? '' ? $product->title : old('title') }}">

    @if ($errors->has('title'))
        <p>{{ $errors->first('title') }}</p>
    @endif
</div>

<div>
    <label for="description">{{ __('Description') }}</label>
    <textarea name="description" cols="30" rows="10">{{ $product ?? '' ? $product->description : old('description') }}</textarea>

    @if ($errors->has('description'))
        <p>{{ $errors->first('description') }}</p>
    @endif
</div>

<div>
    <label for="price">{{ __('Price') }}</label>
    <input type="text" name="price" value="{{ $product ?? '' ? $product->price : old('price') }}">

    @if ($errors->has('price'))
        <p>{{ $errors->first('price') }}</p>
    @endif
</div>

<div>
    <label for="image">{{ __('Choose an image') }}</label>
    <input type="file" name="image">

    @if ($errors->has('image'))
        <p>{{ $errors->first('image') }}</p>
    @endif
</div>

<div>
    <input type="submit" name="submit" value="{{ __('Submit') }}">
</div>