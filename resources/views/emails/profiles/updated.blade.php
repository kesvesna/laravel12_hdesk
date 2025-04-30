<p>Фамилия И.О. {{$old_user['name'] . ' >>> ' . $user->name}}</p>
<p>Город {{$old_user['town'] . ' >>> ' . $user->town->name}}</p>
<p>Организация {{$old_user['organization'] . ' >>> ' . $user->organization->name}}</p>
<p>Подразделение {{$old_user['division'] . ' >>> ' . $user->division->name}}</p>
<p>Роль в организации {{$old_user['function'] . ' >>> ' . $user->function->name}}</p>
<p>Руководитель {{$old_user['superior'] . ' >>> ' . $user->superior->name ?? 'отсутствует'}}</p>
<p>Телефон {{$old_user['phone'] . ' >>> ' . $user->phone ?? 'не заполнено'}}</p>

<br>
<p>Почта {{$user->email}}</p>

<br>
@foreach($user->getRoleNames() as $role)
    <p>Роль в системе {{$role}}</p>
@endforeach

<br>
<a href="{{route('profile.show',$user->id)}}">Перейти в профиль</a>








