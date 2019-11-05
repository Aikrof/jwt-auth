# Simple JSON Web Token Authentication


#### 1. Instalation
<pre>composer require aikrof/jwt-auth</pre>

<hr>

#### 2. Add service provider
<small>Add the service provider to the <code>providers</code> array in the <code>config/app.php</code> config file:</small>
<pre>
'providers' => [
    ...
    Aikrof\JwtAuth\Providers\JwtServiceProvider::class,
    ...
],
</pre>

<hr>

#### 3. Publish the config file
<pre>
php artisan vendor:publish --provider="Aikrof\JwtAuth\Providers\JwtServiceProvider"
</pre>
<small>This command will be create <code>config/jwt.php</code> file with basics configure.</small>

<hr>

#### 4. Migrate table
<small>Create table where will be stored invalid tokens.</small>
<pre>
php artisan migrate
</pre>

<hr>

#### 5. Generate secret and refresh keys
<pre>php artisan create:secret</pre>
<small>This command will create JWT_SECRET_KEY and JWT_REFRESH_KEY in your <code>.env</code> file.</small>

<hr>

#### 6. Configure Auth guard
<small>Inside the <code>config/auth.php</code> file change:</small>
<pre>
    'defaults' => [
        'guard' => 'api',
        'passwords' => 'users',
    ],
    ...
    'guards' => [
        ...
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
</pre>
<small>Set <code>api</code> guard as the default and add <code>jwt</code> driver to <code>api</code> guard</small>

<hr>

#### 7. Update your User model
<small>Add <code>trait</code> to your User model:</small>
<br><small>1)Implement the <code>Aikrof\JwtAuth\JwtCreator</code></small>
<br><small>2)Then add trait <code>use JwtCreator</code></small>
<pre>
namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Aikrof\JwtAuth\JwtCreator;

class User extends Authenticatable
{
    use JwtCreator, Notifiable;
    
    ....
}
</pre>

<hr>

#### 8. Usage

##### 1)Set tokens time to live
##### <small>1. Set token time to live</small>
 <small>By default token time to live (<code>ttl</code>) is 1 week,
you can change <code>ttl</code> in <code>config/jwt.php</code> file or
when create new token:</small>
<pre>
Auth::user()->setTtl(2);
</pre>
<small>Example sets token time to live by 2 minutes.</small>
<br><small><code>setTtl()</code> -takes parameter in minutes.</small>
##### <small>1. Set refresh token time to live</small>
<small>By default refresh token time to live is <code>token ttl * 2</code>,
you can change <code>refreshTtl</code> in <code>config/jwt.php</code> file or
when create new token:</small>
<pre>
Auth::user()->setRefreshTtl(4);
</pre>
<small>Example sets token time to live by 4 minutes.</small>
<br><small><code>setRefreshTtl()</code> -takes parameter in minutes.</small>

<hr>

##### 2)Create JWT token and refresh token
<pre>Auth::token();</pre>
<small>Or with <code>setTtl() or setRefreshTtl()</code></small>
<pre>
Auth::user()->setTtl(2)->setRefreshTtl()->token();
</pre>
<small>Will be returned array with this fields:</small>
<pre>[
    'token' => '...',
    'refresh' => '...'
]</pre>

<hr>

##### 3)Get tokens time to live
##### <small>1. To get token time to live in minutes</small>
<pre>
Auth::user()->getTtl();
</pre>
<small>To get token time to live in UNIX timestamp</small>
<pre>
Auth::user()->getExpTtl();
</pre>
##### <small>2. To get refresh token time to live in minutes</small>
<pre>
Auth::user()->getRefreshTtl();
</pre>
<small>To get refresh token time to live in UNIX timestamp</small>
<pre>
Auth::user()->getRefreshExpTtl();
</pre>

<hr>

##### 4)Logout user
<small>Log the user out - which will invalidate the current token and unset the authenticated user.</small>
<pre>
Auth::logout();
</pre>

<hr>

##### 5)Refresh JWT tokens
<small>Refresh a token, which invalidates the current one and returned new token and refresh token.</small>
<pre>
Auth::refresh();
</pre>
<small>If jwt token was invalid or have invalid data then will be returned <code>null</code>.</small>
