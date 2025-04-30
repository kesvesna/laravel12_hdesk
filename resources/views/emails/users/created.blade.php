<h4>{{$user->name}}</h4>

<p>Почта {{$user->email}}</p>

<br>

@foreach($user->getRoleNames() as $role)
    <p>Роль в системе {{$role}}</p>
@endforeach

<br>

<a href="{{route('profile.show',$user->id)}}">Перейти в профиль</a>








