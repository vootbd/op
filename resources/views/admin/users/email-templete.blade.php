@component('mail::message')
# {{ $details['title'] }}

アカウントが作成されました。 <br>
アカウント情報 <br>
# お名前:  {{ $details['name'] }} <br>
# メールアドレス:  {{ $details['email'] }} <br>
# パスワード:  {{ $details['password'] }} <br>

@component('mail::button', ['url' => $details['url']])
ログインページに移動
@endcomponent

@endcomponent