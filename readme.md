Archive Disabler
================

Sometimes you just don't need things like tag archives or date archives to show up.  Archive Disabler allows you to get rid of the archives you don't want.

![Archive Disabler Options](https://github.com/chrisguitarguy/Archive-Disabler/raw/master/screenshot-1.png)

## Usage

Check the archives you'd like disabled, and select whether you'd like those archive to 404 or redirect to the home page.

If you have custom post types with archives, an option will show up to disable those as well.

## Customize the Redirect

What to customize where you redirect the archives?  Hook into `cd_ad_redirect` and customize away.

    <?php
    add_filter( 'cd_ad_redirect', 'my_custom_ad_redirect' );
    function my_custom_ad_redirect( $uri )
    {
        return home_url( '/some-page' );
    }

## Installation

1. Grab the [zip file](https://github.com/chrisguitarguy/Archive-Disabler/zipball/master)
2. Unzip it 
3. Upload the archive disabler folder to your `wp-content/plugins` directory
4. Activate!

Alternatively, you can grab the zip file and install the plugin via the built in WordPress installer.

## Changelog

### 1.0

- Initial version

