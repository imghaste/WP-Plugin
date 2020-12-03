=== WebP, Image Optimization, CDN, Service Worker & Client Hints All in One ===
Contributors: imghaste, Sociality
Donate link: https://www.imghaste.com/
Tags: WebP, Optimize Images, Compress Images, Service Worker, Image CDN, Client Hints
Requires at least: 3.0.1
Tested up to: 5.4.1
Stable tag: trunk
Requires PHP: 5.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Speed up your website using cutting edge Image Service. Service Worker, Client Hints, WebP, 100% white labeled.
NO URL Rewrite required.

== Description ==

This WordPress plugin provides integration with our SaaS [Image Service](https://www.imghaste.com/ "Image Service")
By leveraging the Power of [Client Hints](https://www.imghaste.com/blog/client-hints-explained).

We've chosen to integrate to WordPress via a <b>Service Worker</b> in order to provide best possible compatibility against
Caching plugins (e.g. W3TC, WPSuper Cache, Elementor...) as well as images coming from your CSS files.

This way you won't have to rewrite your URLS and lose your images SEO juice.

Nor you will get overcharged in case of a BOT crawl your website.


<h1>WHAT DOES THIS PLUGIN DO?</h1>

This plugin registers a Service Worker, providing <b>Progressive Web App functionalities</b> to your
website in order to intercept your Images in order to automatically detect the best possible Image required by your end user.

Since we are speed monkeys, we need to say that the required javascript
that will load on your page is 708 <b>bytes</b> gzipped. (without a <b>k</b>)


<h1>FEATURES OUT OF THE BOX:</h1>

* White Label.
* Automatic Integration.
* Removes EXIF information.
* Compatible with Page Builders.
* Effective Connection Type Detection.
* Quality drop on slow 3g or Save Data on.
* Backup your images for Disaster recovery.
* Works with images coming from *CSS* files.
* Search Engines will grab the origin images.
* Works with images coming from Ajax requests.
* Compatible with Caching / Lazy Load plugins.
* Backup your original image safely in the cloud.
* Automatic WebP Conversion based on browser support.
* Progressive JPEGs & Interlaced PNGs as WebP fallback.

<h1>FULLY WHITE-LABEL</h1>

One of the reasons we decided to go with this approach is no other than SEO.
Using Client Hints Combined with Service Worker will get you all the performance benefits
for your end users without the SEO disadvantages that comes with the URL Rewrite.

<h1>WHY ITS IMPORTANT NOT TO CHANGE IMAGE URLS</h1>

In case you rewrite your image urls into something else all
SEO juice from all image urls goes to the Vendor domain
even if the urls contain a canonical name, they are still searchable using the vendor url.



<h1>PREREQUISITES</h1>

* Your website runs <b>https</b> only
* Your website doesn't have a <b>Service Worker</b> already.


<h1>IMPORTANT</h1>

This plugin is <b>not</b> compatible with other plugins that registers a <b>Service Worker</b>. You'll need to disable other plugins
that registers a service worker on your website or use our [Custom Integration Guide](https://www.imghaste.com/docs/) and lose the white label feature.

== Installation ==

<h1>INSTALLATION</h1>

1. Add IMGHaste from the WordPress repository
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter your CDN URL. If you don't have one, register your account for free [here](https://app.imghaste.com/signup) and add your website to get your CDN URL.
4. Clear any page caching (e.g. W3TC, WPSuper Cache)
5. Navigate through your website (from desktop & mobile) to warm up CDN cache (optional)
6. Enjoy upgraded performance and user experience

<h1>UNINSTALL</h1>

1. Delete WordPress plugin
2. Go to imghaste dashboard under settings
3. Turn Active status to false
4. Delete your origin


== Frequently Asked Questions ==

= Does it work with the new WP editor? =

As long as your files are served from your own domain and https
the integration is platform agnostic.

= What are the limits of free tier? =

The free has all features with paid plans, but comes 1.000 Image Optimization Credits through our Network.

Which is enough to power any small sized WordPress site.

= What if I exceed the limits? =

In case you exceed this limit No hard limitations are applied to your website.
We will contact you to set a more suitable plan for your needs.
We will be happy to see you grow, knowing that we played our small part.


= How to verify the plugin works? =

We get that a lot. Since the integration is fully white-labeled its hard to spot and we are proud for it. You will have to go to open your developer tools and check on network tab you will see your images having type: "WebP" instead of jpeg/png. Check Screenshot#2

= Pagespeed insights still reports "serve next gen images" why? =

Well, this is because all bot speed checkers do not register
a service worker when scanning a website. But you can rest assure that
your users are enjoying webp Images from over 300 Edge Servers worldwide.

This is actually part of the WHITE LABEL solution since your urls won't change.

= Why don't you just rewrite the urls? =

The minute you sign up for url rewrites on your website (imghaste or any other similar service)
you immediately lose a portion of SEO juice from your website towards the image service.

So why waste it when you can benefit from both worlds? 100% SEO and enhanced user experience.

Also what if you decide to change CDN 3 months later? Will you re-re-write the urls?
In our humble opinion it's better to have YOUR links in place even if its undetectable (remember.. 100% white label).

Therefore, we have one option for those brave users
who wishes to ditch their brand name in favor of ours, but we advice against it :)

= Can I use it with other CDN? =

Yes, it works transparently. Our CDN will be the one serving your images, and we will get them from your CDN.
As long as your images come from your own domain name, we will intercept them.
This setup won't provide any additional performance benefits.


= Does this plugin work with all browsers? =

You can check browser compatibility [here]("https://caniuse.com/#search=service%20workers") as of today there is 94.5% compatibility and growing.
As for the rest of the 5.5% this will work the same way it already used to.

Any browser that supports Service Worker will support the image service.
Take notice that you need to serve your website using https:// only to register your Service Worker

= I am using WP JSON API, does it work? =

Oh yes!

= Do you offer a cname for SEO? =

No, We do better we offer the service on your own domain.

= My theme does not provide info for client hints =

That can be even better in some cases.
Contact support, to find out about a hybrid solution called Smart Hints by imghaste
and we be glad to investigate on your behalf.


== Screenshots ==

1. How can we Improve your website speed. You can check before installing
2. Verify Plugin works.


== Changelog ==
= 1.1.0 =
Added SlimCSS Feature.
Added Feature Policy for Client hints

= 1.0.11 =
* Added a no cache header to resolve some false negative HealthChecks.
* Customers with a CDN still needs to manually decache it.

= 1.0.10 =
* Added a fancy gif icon.
* Our Great thanks to the fun of imghaste for his wonderful contribution.

= 1.0.9 =
* cache invalidation of service worker + SDK file every 10 minutes to support upcoming features.

= 1.0.8 =
* Added image-service.ih.js as a physical file as well

= 1.0.7 =
* Some better HealthCheck to avoid redirects
* Linting Project using PSR-2
* Fixed some installation issues
* Enable URL Rewrite for test Only.

= 1.0.6 =
* Some Undefined Warnings reported

= 1.0.5 =
* Add a Short Notice while CDN Url not configured
* Suggest a 5* Rating & a Version Compatibility thumbs up
* Add HealthCheck on plugin settings

= 1.0.4 =
* Fix Changelog outline
* DomDocument errors.

= 1.0.3 =
* Remove CDN Rewrite on a specific case

= 1.0.2 =
* Clear all output buffers before & after Service Worker Route

= 1.0.1 =
* Content-type: application/javascript
* Name of SW: image-service.sw.js renamed to /image-service

= 1.0 =
* Initial Release

== Upgrade Notice ==
