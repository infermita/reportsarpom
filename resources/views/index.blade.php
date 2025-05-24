@extends('global')

@section('content')
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
                    <option value="{{ implode('@', array_keys($value)) }}">{{$key}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>Porta</label>
                <select name="area" class="form-control">
                    <option value="{{ implode('@', array_keys($areas)) }}">Tutti</option>
                    @foreach($areas as $key => $value)
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
    <div class="row">
         <div class="col-md-12 mb-4">
            <table id="datatable" class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Nome</th>
                        <th>Minuti</th>
                    </tr>
                </thead>
                @foreach($res as $val)
                    <tr>
                        <td>{{ Carbon\Carbon::createFromDate($val["Date"])->format("d-m-Y") }}</td>
                        <td>{{ $val["Name"] }}</td>
                        <td>{{ $val["TotalMinutes"] }}</td>
                    </tr>
                @endforeach
            </table>
         </div>
    </div>
</form>
@endsection