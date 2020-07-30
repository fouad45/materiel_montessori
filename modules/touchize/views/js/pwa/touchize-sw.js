/**
 * 2019 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 */
 function createCacheBustedRequest(url) {
	let request = new Request(url, {cache:'reload'});
	if('cache' in request) {
		return request;
	}

	let bustedUrl = new URL(url, self.location.href);
	bustedUrl.search += (bustedUrl.search ? '&' : '') + 'cachebust=' + Date.now();
	return new Request(bustedUrl);
}

self.addEventListener('install', function(event) {
	event.waitUntil(
		fetch(OFFLINE_URL)
			.then(function(response) {
				return caches.open(CACHE_NAME)
					.then(function(cache) {
						return cache.put(OFFLINE_URL, response);
					});
			})
	);
});

self.addEventListener('activate', function(event) {
	event.waitUntil(
		caches.keys()
			.then(function(cache_names) {
				return Promise.all(
					cache_names.map(function(cache_name) {
						if(CACHE_WHITELIST.indexOf(cache_name) === -1) {
							console.log('Deleting old cache: ' + cache_name);
							return caches.delete(cache_name);
						}
					})
				);
			})
	);
});

self.addEventListener('fetch', function(event) {
	let request = event.request;
	if(
		request.mode === 'navigate' ||
		(request.method === 'GET' && request.headers.get('accept').includes('text/html'))
	) {
//		console.log('Handling fetch event for ' + request.url);
		if(request.url.indexOf('?offline') !== -1) {
//			console.log('Offline url detected, attempting to fetch online page.');
			request = new Request(ONLINE_URL, {
				method: request.method,
				headers: request.headers,
				mode: 'same-origin',
				credentials: request.credentials,
				redirect: "manual"
			});
		}
		event.respondWith(
			fetch(request)
				.then(function(response) {
					if (response.status == '401') {
						//Kill switch
						//Unregister the serviceworker to let the browser handle the login part
						self.registration.unregister()
					    .then(function() {
					      return self.clients.matchAll();
					    })
					    .then(function(clients) {
					      clients.forEach(client => client.navigate(client.url))
					    });
					    //Force a reload
					    var init = {
					    	status : 301,
					    	headers : {
					    		'Location' : response.url
					    	}
					    };
					    return new Response(null, init);
					}
					return response;
				})
				.catch(function(error) {
					console.log('Fetch failed, offline page served instead. ' + error.message);
					return caches.match(OFFLINE_URL);
				})
		);
	}
});