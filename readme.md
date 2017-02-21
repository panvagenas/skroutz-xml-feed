# Skroutz.gr XML Feed for WooCommerce

## Description

**Skroutz.gr XML Feed for WooCommerce** is a WordPress plugin that generates XML feed according to skroutz.gr specs

#### Features

* Exclude products by category or tag
* Supports fashion products (shoes, clothes etc)
* Supports bookstores (ISBN field)
* XML compression

#### Requirements:

* WordPress version: 3.5.1+
* Apache version: 2.1+
* PHP version: 5.4+

## Installation

Please consult WordPress plugin [installation guide](https://codex.wordpress.org/Managing_Plugins#Installing_Plugins)

## License

Copyright (C) 2015 Panagiotis Vagenas <pan.vagenas@gmail.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see [http://www.gnu.org/licenses/](http://www.gnu.org/licenses/).

## Changelog

#### 170126

* Added functionality to define product availability per product
* Added file compression (GZ)
* Added weight field
* Removed WS FW

#### 160904
* Added product exclusion by category
* Added product exclusion by tag
* Tested on WordPress 4.6, WooCommerce 2.6 and PHP 7

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
