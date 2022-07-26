## Random explaination on functions/why/what/where that I need to refactor within docs at some point
## All info below is more for myself to later compile into smaller/refactored categories, once scope becomes much more clear

General API/middleware handling


```
Be it through api or through frontend implementation, you are free to perform additional validation rules on the internal functions.

Try to perform only non-database validation/sanitization within this router. This minimizes any errors affecting any other endpoints.

Which is great so that small tasks/execution do actually get completed much faster, the bigger the execution the more this can/may affect other executions/requests.

Most of executions within casino industry, except for big data transformations (returning gamelist for example), are very small json objects (like balance object).

We will be curling an awful lot and/or proxying, hence why the ProxyFacade & config makes it very easy to setup curl/proxy/selenium tasks on another host with extreme ease.

This brings many advantages, however in case of mixing different types of frontend/backend we do not want it to affect each other.
You should perform database validation after the initial sanitization & regular (example: checking if a a game exists within db for given game_id on the internal function itself and not within this router)

```

## General tweaks & how PHP works

```
In essence you can look at php as 1 big queue of jobs based on the order of time, this means that once we call another function from within a router this basically puts the job at back of the queue. 

In general you can say that all tasks & data is extremely short & small and why PHP is good to handle your API stuff within the casino industry (most of tasks consist of returning singular objects like balance).

In recent years above obvious disavantage (single software thread) is solved by spawning additional php worker_threads/pools - basically the software reconizing dynamically (but can be set statically also within php_pool.ini) when to scale up additional threads, however this is only a workaround and will cap very much based on amount of processor cores and the more you compartiment regular returning functions the better. Compare php worker_threads this to your CPU processor having multiple threads.

When picking hardware for any productional use while using PHP within your API's you should always prefer amount of the total threads above the speed/capacity of the cores, unless you have small workload (though at that point won't be much of use/change anyway). 

You can then configure your php pools to easily keep regenerating threads on software level.

Most important is the max execution time on php pools, this prevents from the main php service from clogging up completely (you can set amount of time in seconds & max_memory size to kill off the process and re-spawn a pool).

Make sure that http threads and limits like rlimit, ulimit etc. do not cap you on the above either. Also take into account that there's differences within apache2 (and derrivates like litespeed etc.) and nginx in terms of the above, while apache2/litespeed will work in same way as php, nginx sorta works in similar way to the extension of php to pooling (and hence why there is php-fpm).

```

## General info regarding database

```
Fastest stack to use in conjuction for database management by far is noSQL/mongodb (though make sure to cache bigger collections like gamelists), though you will need to periodically archive data that is no longer in active use to stay optimal.

You can also use a mix of the above, it is important to know the difference between relational db's and/or the management differences.

Personally I would use a mix for example, I would use live data/recent data on mongoDB and to archive this using short time rules to mySQL.

Let's say for a liveblackjack game or in some cases retarded gameproviders will see (and send you) individual game callbacks for each bet on the game of roulette.

So if a user places bet on 8 numbers these unoptimized providers can/may send you 8x a full game transactions not only on win but also on the bet, so let's see the differences and what to be on the look out for.

General rule of thumb: mysql will be fine if you're starting out or if you are using this for a medium/small singular casino and/or a local environment, mongoDB is by far much quicker and has a much bigger error margin however is some important stuff to configure for productional/bigger/multi environment.


## mongoDB/noSQL you are saving each object directly from read into write buffer and make use of "collections" of singular objects.

This means that each game in full is stored and written on each own, instead of "fill" a pre-set table (like in mySQL where you tell fields for a general collection all rules just once).

While great for writing, mongodb on bigger data sets where you need to transform or search data actively will be terrible in comparison to mySQL if not making proper use of caching the data and creating proper indexing (basically this is mySQL's fields and prioritize order on fields).

Game lists you should cache for longer periods of time outside of noSQL and preferably in memory.

Big performance increase, if you are using mongoDB is to keep in mind to keep your "field" names as short as possible as each object is stored individually.

Simply take into account how many characters (general object size) are used, how many of those are used for field names and how many for the actual data.

But again this is only once you grow/become bigger and on productional - until then save yourself the hassle and just use mySQL, you can always change over at a later point in time and regardless you kinda will want your mySQL and field tabling to be there for archiving purposes anyway.


```

## MongoDB 

In short: **mongodb is optimal for short (in char length) key:value datasets, however smaller specific (unique) amount of search queries the better will perform. **

Though, with proper index/order sorting, simple queries can be done pretty much as fast as mysql, this becomes different the bigger amount of __dynamic__ data you are selecting within your search.

Let's say you are storing 10000 recent transactions within mongoDB before moving them (upon succesfull completion) to archive, look at below example of (*10000) difference in object size and how much size can be wasted on fields:

```json
{
    "transaction-hash": "2512906215",
    "transaction-outcome": "win",
    "transaction-betamount": "10000",
    "transaction-identifier": "NUMBER1234",
    "transaction-game-id": "slotmachine_game",
}
```

To the following (again, imagine this * 10000 objects in small-value-size collection):

_(Data snippet iself is only to serve to explain the inner working of mongodb stack. The data itself has nothing to do with our app in general.)_

```json
{
    "h": "2512906215",
    "o": "win",
    "b": "10000",
    "i": "NUMBER1234",
    "gi": "slotmachine_game",
}
```

** While the collection object count is the same, your database for the same 10000 data objects will be half +- in size using above example, shortening fieldnames where you can will in the end yield you a lot of performance gain.**

You can then head over to next step where you also structure & split up the data in collections or change for example to id's on places where you can, so like using same example, look at the final key's value to show you:

```json
{
    "h": "2512906215",
    "o": "win",
    "b": "10000",
    "i": "NUMBER1234",
    "gi": "slotmachine_game",
}
```

Removing statically same value data in below example: you can see changing the slotmachine slug to a shortened id, which in turn we then use to search (using memcached or mysql or any other relational db) on our handling as it is faster then searching through big useless data arrays that is same anyway (like prefixes in values etc. that are same on every object).

_(Data snippet iself is only to serve to explain the inner working of mongodb stack. The data itself has nothing to do with our app in general.)_

```json
{
    "h": "2512906215",
    "o": "w",
    "b": "10000",
    "i": "1234",
    "gi": "1",
}
```

Then step after that (if even needed) is to make transforming dynamic morphable data, but at that point you should hire someone who actually has studied db management stuff or hire a AWS specialist because if any need for that would mean that you actually can go full serverless for your data needs.


However, while the package provided will work fine for any development capacity and small productional env's, I hope that showing you 2x small examples to atleast make you aware of the difference in data managament, so that while you develop your own stuff on-top of package you from get-go know these simple code structural optimals (use google in addition for your usecase).

For search enquiries, it is important to have a proper index configuration for your collection. 

Keep the object example I've shown above in back of your head, because if you select (searching for the object) for stuff on mongoDB you will want the least amount of unrelated info unless not in some capacity (with active use) have to do with each other.

Example of how mongoDB selects/searches using the above object examples, let's say you are searching for a transaction that ended up to be at order at the very end of your collection, this means each full object (if not properly indexed) is queried/searched/indexed for 9999 times before finding the object last (10000th item) you was looking for.

Always archive (big) data that is rarely used immediately and make archiving servers/clusters/workers seperated from any live environment as it will affect game/page loading potentially quite big.

MongoDB is much less forgiving in bad configuration of index/sort configuration and on bigger env's will result in extreme use of mongoDB cluster scaling & can even cause sudden page/data blackouts (if db query takes longer then the max. execution time on your http server f.e.) and thus can disable your pages easily at a very sudden rate while being fine beforehand.

Then again, as showing also the next step (if needed, again all this above on small data sets), is to really limit also the active used data based on actual metrics you should make a plan for this and proper database structuring.

Where on mySQL it's fine and encouraged to use as many fields in a table basically, on mongoDB the mindset is completely reversed and you should make different collections for every little bit of dataset:

player_profile
player_balances
player_currencies
player_vipstats
player_transactions_wins
players_transactions_losses
player_transactions_hourly
player_transactions_daily
player_transactions_monday
player_transactions_tuesday
player_transactions_wednesday

etc. etc. 

Archiving on week numbers or whatever, there's a million ways, aslong to keep it also useable without much transformations on the client/app you want to include.


```

## General info regarding caching
```
Proper cache configuration by _FAR_ will result in the biggest performance increase.

For caching, once you are in a stable state (you will run into issues if you are not starting from a stable state) is to really go out and utilize [Laravel Octane](https://laravel.com/docs/9.x/octane) utilizing Swoole or Roadrunner. You can setup PHP-native caching using php-cgi & php-opcache. Do not forget to clear your cache (by restarting php-fpm workers) after making configurational changes, in general do not use this on your dev stack because you will honestly a 100% forget about the hard caching (and you will be figuring out all night why code changes not pushing :P).


```

General info queue's

```
For your queue's & session caching, the fastest is to use memcached though redis is fine in most cases (only make sure to restrict any pagefiling on (slower) harddisk) and easier to maintain down the road where you will want insight in your handling.

You can set database/cluster ID's on laravel within the /config/ directory, f.e. on caching config/cache.php etc. this makes able to use multiple API stacks while only needing to fire up single redis cluster. Do not forget to set automated eviction to not run into any issues on any leakage as else will break rest of the stack completely.
```
