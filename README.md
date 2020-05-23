# Laravel-Proxy-Helper
This is a basic helper for using laravel controllers as proxy servers. 
It allows you to forward exact requests to another server and return their response. 
Supports all methods, and also files.

## What it does

Its main function is to make requests from one point to another using laravel as a proxy.

**Normal request:**

Client ---> Server2

``GET server2.test/api/avatar/color``

**Request with this helper:**

Client ---> Laravel ---> Server2

``GET laravel.test/api/proxy/api/avatar/color``

- Works with all GET, POST, PUT, HEAD, PATCH, DELETE methods
- Works with all formats: JSON / Multipart / www-form-urlencoded
- Supports files and any type of parameters supported by the original requests
- Allows you to add headers, authorization, and custom options.
- Returns the actual request (both content and status code)
- Allows calls to be made dynamically (i.e., any request the customer may have)


## How to add it
Since it is not a package, it may seem more tedious, but it is very simple.

### 1. Copy both files:
`ProxyHelperFacade.php` y `ProxyHelper.php`
in the folder you prefer from your laravel project, I've used: `App\Helpers`.
**If you use a diferent folder, change the namespaces of both files.**

### 2. Add the facade to your laravel project.
Go to the file: `App\Providers\AppServiceProvider.php` and in the method `register()` adds the facade:
```php
$this->app->bind('ProxyHelper', function($app) {
    return new ProxyHelper();
});
```
remember that you need to import the class (not the one called facade) in `App\Providers\AppServiceProvider.php`
```php
use App\Helpers\ProxyHelper; //or your path
```
### 3. Import it wherever you're going to use it.
Now you're gonna use the facade.
```php
use App\Helpers\ProxyHelperFacade;
```
to call him:
```php
ProxyHelperFacade::CreateProxy($request)
```
## How to use

It's pretty basic, but you can modify it according to your needs.

The constructor requires an object ``Illuminate\Http\Request``.

From there you can forward the request to another server, or a specific url, or if you want to modify/add some options.

I'll show you an example:

#### 1. Create controller (or could be middleware)

In my API drivers: ``routes\api.php`` (could be web, doesn't matter)

I have created a route to receive any kind of request, and redirect it as to another host.

```php
Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'proxy/{slug}', function(Request $request){


})->where('slug', '([A-Za-z0-9\-\/]+)');
```
This route accepts any call (POST,GET..) and any route from /proxy, i.e. ``my.laravel.test/api/proxy/*``

So, when you want to call my ``server2``, I'll do it this way:

Suppose I want to call GET ``my.server2.test/api/avatar/color``, I'll do it like this: 

GET ``my.laravel.test/proxy/api/avatar/color``

#### 2. Use ProxyHelper (Basic)

I will continue to show the example inside the controller we have created.

```php
Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'proxy/{slug}', function(Request $request){

    // To redirect the request to a different host, the first parameter will be the host.
    // the second, will be the current path that we want to ignore, it must be the url of the controller (api/proxy)
    //so we're telling you that the new url will be:
    // (host) http://my.server2.test + (deleted)[api/proxy] + ({slug}) /api/avatar/color
    return ProxyHelperFacade::CreateProxy($request)->toHost('http://my.server2.test','api/proxy');
    
    //other way is to tell him the url directly.
    return ProxyHelperFacade::CreateProxy($request)->toUrl('http://my.server2.test/api/avatar/color');
    
    // this second way will no longer be dynamic.
    

})->where('slug', '([A-Za-z0-9\-\/]+)');
```
#### 3. Options

Once we've seen how to make it dynamic or static, there are a few options:

```php
Route::match(['get', 'post', 'head', 'patch', 'put', 'delete'] , 'proxy/{slug}', function(Request $request){

    return ProxyHelperFacade::CreateProxy($request)
            // add a header before sending the request
            ->withHeaders(['x-custom' => 'customHeader'])
            // add a Bearer token (this is useful for the client not to have the token, and from the intermediary proxy we add it.
            ->withToken('eyJhbGcLPbNA...')
            //Maintain the query of the url.
            ->preserveQuery(true)
            ->toHost('http://my.server2.test','api/proxy');

})->where('slug', '([A-Za-z0-9\-\/]+)');
```
some more:

``->withBasicAuth('user','pass')``,

``->withDigestAuth('user','pass')``,

``->withMethod('POST'`)``,

And that's it, I hope it helps you, if you want to add more things like custom cookies, follow the same dynamics of the file, it's very simple.
