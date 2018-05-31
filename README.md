# one 
是一个兼容fpm和swoole两种运行模式的php迷你框架

## 安装
```
git clone git@github.com:lizhichao/one.git
cd one
composer install

```

## 目录说明

 - App 应用目录
 - One 框架目录

## 运行模式配置

```php
// App/config/app.php
return [
    'run_mode' => 'swoole' // swoole | fpm
];

```

## 路由

 * @method void group(array $rule, \Closure $route)
 * @method void controller(string $path, string $controller)
 * @method void shell(string $path, string|array $action)
 * @method void get(string $path, string|array $action)
 * @method void post(string $path, string|array $action)
 * @method void put(string $path, string|array $action)
 * @method void delete(string $path, string|array $action)
 * @method void patch(string $path, string|array $action)
 * @method void head(string $path, string|array $action)
 * @method void options(string $path, string|array $action)


```php
use One\Facades\Router;

//直接调用一个方法
Router::get('/test',\App\Controllers\IndexController::class.'@test');

//设置一个别名 {id} 匹配数组
Router::get('/user/{id}',[
    'use' => \App\Controllers\IndexController::class.'@user',
    'as' => 'user'
]);

//返回 /user/100
router('user',['id' => 100]);

//{xxx}通配符
Router::get('/user/{name}','User@getUserInfoByName);

//正则表达式匹配 ^\w{2,4}$
Router::get('/user/`^\w{2,4}$`','User@getUserInfoByVipName');


```

### 路由分组

- `group(array $rule, \Closure $route)`

`$rule`为数组

```php
[
	'prefix' => '', //前缀
	'namespace'=>'\\App\\Controllers\\', //命名空间
	'cache'=>1, //缓存时间 单位(秒)
	'middle'=>[
		'User@auth', //调用方法
	] //中间件
]
```

例如：

```php
Router::group([
	'namespace'=>'\\App\\Controllers\\', //命名空间
	'cache'=> 100, //缓存时间 单位(秒)
	'middle'=>[
		'\\App\\Middle\\User@auth', //调用方法
	] //中间件
],function(){
	Router::get('/user/{id}',[
    'use' => IndexController::class.'@user',
    'as' => 'user'
	]);
});
```

也可以这么写

```php
Router::get('/user/{id}',[
	'use' => IndexController::class.'@user',
	'as' => 'user',
	'namespace'=>'\\App\\Controllers\\', //命名空间
	'cache'=> 100, //缓存时间 单位(秒)
	'middle'=>[
		'\\App\\Middle\\User@auth', //调用方法
	] //中间件
]);
```

在命令行可以  
`php App/index.php /get/user/100`   
调用这个接口
    
## orm

```php
$user = User::find(1);

```
`$user` 是一个User对象可以通过定义的关系直接`$user->关系方法名`

如果需要转换成数组  

```php
$arr = (array) $user
```

列子： 

```php
namespace App\Model;

use One\Database\Mysql\Model;

class User extends Model
{
    CONST TABLE = 'users';

	// 缓存时间
    protected $cache_time = 100;

	// 事件
    public function events()
    {
        return [
            'afterFind' => function ($ret) {

            },
            'beforeFind' => function (& $a) {

            }
        ];
    }

	// 关系
    public function teamMembers()
    {
        return $this->hasMany('id', TeamMembers::class, 'user_id');
    }

}

$user = User::find(10);

//所有关联的teamMembers
$list = $user->teamMembers;

//设置条件调用
$list = $user->teamMembers()->where('type','>',1)->limit(10)->findAll();

```


### 关系


- `hasOne($self_column, $third, $third_column)` 一对一

- `hasMany($self_column, $third, $third_column)` 一对多


```php
	
	$list = User::whereIn('id',[1,2,3,4])->with('teamMembers')->findAll()
	
	//设置条件调用
	$list = User::whereIn('id',[1,2,3,4])->with('teamMembers',[function($teamMembers){
		$teamMembers->where('type','>',1);
	}])->findAll();
	
	//连续关联并且设置条件 teamMembers.teams
	$list = User::whereIn('id',[1,2,3,4])->with('teamMembers.teams',[function($teamMembers){
		$teamMembers->where('type','>',1);
	},function($teams){
		$teams->column(['id','name'])->cache(0);
	}])->findAll()
	
	//如果不需要缓存
	User::whereIn('id',[1,2,3,4])->with('teamMembers')->cache(0)->findAll()
	//或者把模型中的 $cache_time 设置为0

```

    
### 缓存

如果在模型中设置 `cache_time` 大于0标示这个模型的所有查询都会走缓存。
默认情况是所有模型都会缓存。也可通过每条语句的链式调用方法cache($time)来重新设置

- 缓存更新  
  当这个表有更新、删除、新增时会自动清除这个表的缓存。


### 事件

含有事件的方法有：
`find`、`findAll`、`update`、`delete`  
均有前置事件`before`和后置事件`after`.  
如：
`beforeFind` `afterFind `  
beforeFind 如果返回 `false` 会阻止这个动作执行。
可以通过 `&` 引用来修个调用的参数的值

	

