# thedaysoflife.com #
www.thedaysoflife.com

### Share Memories, Inspire People

I like social networks, they are made to connect people, but just for the sake of connecting. There are hundreds of social networks out there, and there are millions of statuses update everyday but how many of them are really meant to share something ? Or just to complain, to show off, to disrespect others? People spend hours surfing the network, clicking "Like" without event reading the status, commenting "smiley face" because they do not know what to say (or write). I was really amazed knowing that there are people who have thousands of friends, but how many of them did they meet, or talk, or text for the last seven days? I created TheDaysOfLife in the hope that it would be a place where people would feel comfortable to share their memories, a place where people would drop by seeking for companionship when lonely , a place where people would find inspiration when feeling down.

### How ThedaysOfLife was created

It was a rainy day in November 2014, a boring day. I wasn’t in the mood to do anything. I felt so empty and lonely, part of me just want to cry out loud. Again, I asked myself the same old question: “What is the meaning of my life ?”. I searched every sector of my mind for an answer, just to recall a quote from the comedy Jerry Maguire “Everyday I wake up I tell myself that today is gonna be the best day of my life”. I jump up the computer and google for “best day of my life”, one and a half billiion results returned, but only one grabbed my attention: American Authors – Best Day Of My Life – YouTube. I started listening to the song, over and over:
<pre>
“I had a dream so big and loud
I jumped so high I touched the clouds
I stretched my hands out to the sky
We danced with monsters through the night
I'm never gonna look back
Whoa, I'm never gonna give it up
No, please don't wake me now
This is gonna be the best day of my life 
This is gonna be the best day of my life..”
</pre>
Many questions poped up in my head: Was anybody in the same situation like me ? Why don't we share our moments so that others would be inspired ? Do people care enough to share ? Two weeks later, TheDaysOfLife was up and running.

### Development

Thedaysoflide was developed by using the Jennifer framework https://github.com/ngodinhloc/jennifer

### The Application
- [The Application Structure](#the-application-structure)
    - assets
    - caches
    - config
    - controllers
    - models
    - plugins
    - templates
    - views 
    
- [Single Point Entry](#single-point-entry)
    - index.php
    - api\index.php
    - controllers/index.php

### The Application Structure
- assets: contains css, js, images
- caches: contains cache files for mysql queries, views
- config: contains config files : env.inc, routes.inc...
- controllers: contains all controller classes
- models: contains all the packages and models which are the heart of Jennifer framework
- plugins: contains all plugins, such as: bootstrap, ckeditor, jquery
- templates: contains all templates using in views, models and controllers. Templates are organised under module just like view. There are view templates and content templates. Each view has one view template with similar file name. For example: the index view (index.class.php) is using index template (index.tpl.php). Content templates are placed inside "tpl" folder, content templates may be used to render html content in views, models or controllers.
- views: contains all view classes. View classes are placed under each module. In the sample code, we have 2 modules: "back" and "front", each module has serveral views.

### Single Point Entry
#### index.php
<pre>
use Jennifer\Http\Response;
use Jennifer\Http\Router;
use Jennifer\Sys\System;

try {
    $system = new System([DOC_ROOT . "/config/env.ini"]);
    $system->setRouter(new Router([DOC_ROOT . "/config/routes.ini"]))->loadView()->renderView();
} catch (Exception $exception) {
    (new Response())->error($exception->getMessage(), $exception->getCode());
}
</pre>
#### api/index.php
<pre>
use Jennifer\Api\Api;
use Jennifer\Http\Response;
use Jennifer\Sys\System;
use thedaysoflife\Api\ServiceMapper;

try {
    $system = new System([DOC_ROOT . "/config/env.ini"]);
    $system->setApi(new Api(new ServiceMapper()))->runAPI();
} catch (Exception $exception) {
    (new Response())->error($exception->getMessage(), $exception->getCode());
}
</pre>
#### controllers/index.php
<pre>
use Jennifer\Http\Response;
use Jennifer\Http\Router;
use Jennifer\Sys\System;

try {
    $system = new System([DOC_ROOT . "/config/env.ini"]);
    $system->setRouter(new Router([DOC_ROOT . "/config/routes.ini"]))->loadController()->runController();
} catch (Exception $exception) {
    (new Response())->error($exception->getMessage(), $exception->getCode());
}
</pre>
