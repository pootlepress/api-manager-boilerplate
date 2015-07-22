Pootlepress API Manager integration
===================================

1. Copy and paste the 'pp-api' directory to the root of your plugin.

2. Copy and paste these line to your main plugin file.
```php
/** Including PootlePress_API_Manager class */
require_once( plugin_dir_path( __FILE__ ) . 'pp-api/class-pp-api-manager.php' );
/** Instantiating PootlePress_API_Manager */
new PootlePress_API_Manager( 'Your_Plugin_Name', 'Your Plugin Name', '1.0.0', __FILE__, 'your-plugin-textdomain' );
```
Comments are not necessary but good to have.

ENJOY AUTOMATIC UPDATES :)

------
 NOTE 
------
For PootlePress_API_Manager manager class required following parameters.

1. $token: Your (unique) plugin token
2. $name: Your plugin name (Must match WC Product's **Software Title** in API tab)
3. $version: Plugin version, don't forget to update each time version updates
4. $file: __FILE__ php Serves as reference to current plugin
5. *$text_domain: Text domain for plugin, $token with - instead of _ is used if not supplied.
6. *$upgrade_url: URL to request update from, defaults to http://pootlepress.com/, no need to change for pootlepress plugins

*= Optional