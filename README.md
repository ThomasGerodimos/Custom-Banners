# WordPress Plugin
For my first attempt in creating a WordPress plugin I needed a way to show a banner in a fixed spot in a webpage for a period of time.
So I created the plugin Custom-banners.

When the plugin is activated, it creates a custom post type, which in my case I named it live because it would accept post with live video feed.
![image](https://github.com/ThomasGerodimos/Custom-Banners/assets/3667706/64cbe5ba-2bb4-4b6e-8082-97202854ecf5)

From the plugin settings you can upload specific banners (for dsktop and mobile):
![image](https://github.com/ThomasGerodimos/Custom-Banners/assets/3667706/48f3806f-f05d-4ecb-889e-433b35c12ec6)

Finaly when a post is publishes in the "live" custom post type and the "End date" is greater from today date the banners are shoen in the website position where the shortcode [custom_live_banner] is stored. 


