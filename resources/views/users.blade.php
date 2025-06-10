@extends('global')

@section('content')
    <div class="card-body" id="new" style="display: none">
        <div class="row">
            <div class="col-lg-12">
                <div class="p-5">
                    <form class="user" id="newform" action="/users" method="post">
                        <div class="form-group row">
                            @csrf
                            <div class="col-sm-4">
                                <label>Nome cliente</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Nome cliente" required />
                                <input type="hidden" id="id" name="id" value="0">
                            </div>
                            <div class="col-sm-4">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" id="email" placeholder="Email" required />
                            </div>
                            <div class="col-sm-4">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" id="password" placeholder="Password" required />
                            </div>
                           
                        </div>
                        <button type="submit" class="btn btn-primary float-right">SAVE</button>
                        <button type="button" class="btn btn-primary" onclick="viewNewRow(false)">UNDO</button>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body" id="button">
        <a href="#" class="btn btn-primary float-right" onclick="viewNewRow(true)">
            <span class="text">NUOVO</span>
        </a>
    </div>
    <div class="card-body" style="height: 100%;" id="list">
        <div class="table-responsive">
            <table class="table table-bordered" id="datatable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Azioni</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>

                            <td>{{$user->id}} </td>

                            <td>{{$user->name}} </td>

                            <td>{{$user->email}} </td>

                            <td>

                                <div class="btn-group btn-group-sm"><a href="#" class="btn btn-primary"><nobr>Seleziona azione</nobr></a>
                                <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-menu-right">
                                    <a class="dropdown-item" href="#" onclick="modRow('{{ $b64[$user->id] }}')">Modifica</a>
                                    <a class="dropdown-item" href="#" onclick="delRowDB('{{$user->id}}')">Cancella</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

