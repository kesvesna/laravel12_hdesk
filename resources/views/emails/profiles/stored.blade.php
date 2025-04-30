<h4>{{$user->name}}</h4>

<p>Город {{$user->town->name}}</p>

<p>Организация {{$user->organization->name}}</p>
<p>Подразделение {{$user->division->name}}</p>
<p>Роль в организации {{$user->function->name}}</p>

<p>Почта {{$user->email}}</p>
<p>Телефон {{$user->phone ?? 'не заполнено'}}</p>

<br>

@foreach($user->getRoleNames() as $role)
    <p>Роль в системе {{$role}}</p>
@endforeach

<br>

<a href="{{route('profile.show',$user->id)}}">Перейти в профиль</a>








