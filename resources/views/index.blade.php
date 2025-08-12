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
                    @foreach($company as $key => $value)
                        <option value="{{ implode('@', array_keys($value)) }}"
                        @if( implode('@', array_keys($value))  == $request["cardholder"])
                             selected
                        @endif
                        >{{$key}}</option>
                        
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>Area</label>
                <select name="area" class="form-control">
                    <!--<option value="{{ implode('@', array_keys($areas)) }}">Tutti</option>-->
                    @foreach($areas as $key => $value)
                    <option value="{{$key}}">{{$value}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>Data</label>
                <input type="date" class="form-control datetime" name="start" required value="{{ $request["start"] }}"/>
            </div>
        </div>
        <!--
        <div class="col-md-2 mb-4">
            <div class="form-group">
                <label>Al</label>
                <input type="date" class="form-control datetime"  name="end" value="{{ $request["end"] }}"/>
            </div>
        </div>
        -->
        <div class="col-md-3 mb-4">
            <div class="form-group">
                <label>&nbsp;</label>
                <button class="btn btn-info form-control" type="submit">Cerca</button>
            </div>
        </div>
    </div>
    <div class="row">
         <div class="col-md-12 mb-4">
            <table id="datatable" class="table table-bordered nowrap">
                <thead>
                    <tr>
                        <th>Azienda</th>
                        <th>Nome</th>
                        <!--<th>Cod. Sarpom</th>-->
                        <th>Badge</th>
                        <th>Data ora ingresso</th>
                        <th>Data ora uscita</th>
                        <th>Ore:Minuti Totali</th>
                        <th>Ore:Minuti Effettive</th>
                    </tr>
                </thead>
                @foreach($res as $val)
                    <tr>
                        <td>{{ $companySel }}</td>
                        <td>{{ $val[0] }}</td>
                        <!--<td>{{ $val[1] }}</td>-->
                        <td>{{ $val[2] }}</td>
                        
                        <td>{{ $val[3] }}</td>
                        <td>{{ $val[4] }}</td>
                        <td>{{ $val[6] }}</td>
                        <td>{{ $val[5] }}</td>
                    </tr>
                @endforeach
            </table>
         </div>
    </div>
</form>
@endsection