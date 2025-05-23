@extends('global')

@section('global')
<form class="form-group" action="/" method="post">
    @csrf
    <div class="row">

        <!-- Earnings (Monthly) Card Example -->
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>Azienda</label>
                <select name="cardholder" class="form-control">
                    <option value="{{ implode('@', array_keys($company)) }}">Tutti</option>
                    @foreach($company as $key => $value)
                    <option value="{{$key}}">{{$key}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>Porta</label>
                <select name="door" class="form-control">
                    <option value="{{ implode('@', array_keys($doors)) }}">Tutti</option>
                    @foreach($doors as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="form-group">
                <label>Dal</label>
                <input type="date" class="form-control datetime" name="start" required />
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="form-group">
                <label>Al</label>
                <input type="date" class="form-control datetime"  name="end" required/>
            </div>
        </div>
        <div class="col-md-2 mb-4">
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="btn btn-info form-control" type="submit">Cerca</button>
            </div>
        </div>
    </div>
</form>
@endsection