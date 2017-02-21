=== Skroutz.gr XML Feed for WooCommerce ===
Contributors: pan.vagenas
Tags: feed, generate xml, price comparison, skroutz, skroutz.gr, xml, xml feed
Requires at least: 4.0
Tested up to: 4.7.2
Stable tag: 170126
License: GNU General Public License V3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.txt

Generate an XML according to skroutz.gr XML feed specs

== Description ==

**Skroutz.gr XML Feed for WooCommerce** is WordPress plugin that generates XML feed according to skroutz.gr specs

**If you like this plugin please support the developers and/or rate it at WordPress.org**

#### Features

* Exclude products by category or tag
* Exclude specific products
* Define availability per product
* Supports fashion products (shoes, clothes etc)
* Supports bookstores (ISBN field)
* XML compression

#### Requirements:

* WordPress version: 3.5.1+
* Apache version: 2.1+
* PHP version: 5.4+

== Installation ==

Please consult WordPress plugin [installation guide](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

== Screenshots ==

1. Main options
2. Map options
3. Info panel

== Changelog ==

#### 170126

* Added functionality to define product availability per product
* Added file compression (GZ)
* Added weight field
* Removed WS FW

#### 151127

* Fix: Attributes with Greek slug won't appear in XML
* Tweak: Support for larger product image sizes
* Tweak: You can now include category full path (experimental, need feedback)
* Tweak: Better message handling during XML generation
* Tweak: Filters in XML generation log panel

#### 150804

* New availability strings
* Fixed: Availability when product is out of stock and/or backorders allowed
* Fixed: Options submiting bug when XML path were empty

#### 150707

* WooCommerce Brands Addon support

#### 150610

* Fixed: Bug fix in color attributes

#### 150521

* Fixed: Unpublished products were included in XML
* Optimized default options

#### 150501

* Initial release
