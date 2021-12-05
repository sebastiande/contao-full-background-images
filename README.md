Contao Full Background Images
==============

This extension provides multiple variants of having full background images for a Contao Open Source CMS based website:

* Single full background image with fade in
* Randomized full background image with fade in
* Multiple full background images with fade in and fade to change images
* Subpages can inherit settings from a parent page
* Mobile ready (phone & tablet)

Documentation
-------------
## Installation

* Install it via the Contao Manager (Composer) (search for `sdc/contao-full-background-images`)

## Requirements

* jQuery has to be enabled in the page layout

## Usage

All settings are placed in the page tree. Subpages will inherit the settings from parent page by default.

You can optionally define different settings for each subpage. It's also possible to disable the background image(s) on certain pages.


### Regular page / 403 / 404
#### Operation mode
* `Inherit from parent` - Inherits settings from a parent page
* `Selection of background images` - Choose the background images for this page
* `Disabled` - No background images are shown

#### Sort
* All known Contao sort modes

#### Image mode
* `Single image` - Show only one image (the first from the selection)
* `Random single image` - Show only one image (random from the selection)
* `Fade multiple images` - Show all images from the selection with fading

#### Timeout time
* Time between image changes in ms

#### Animation speed
* Animation speed in ms

#### Enable navigation
If checked, a navigation element is displayed (default lower left corner)

#### Enable navigation click
If checked, you can click on the navigation items to change the background

#### Enable prev/next navigation
If checked, a prev and a next button (default style: arrow) is added to the navigation items

#### Center image horizontally
If checked, the background image will be horizontally centered, else left aligned.

#### Center image vertically
If checked, the background image will be vertically centered, else top aligned.

#### Custom template ####
You can choose a specific template for the current page.


Upgrade Notes
-------------
* First release for Contao 4.4 **[3.0.0](https://github.com/sebastiande/contao-full-background-images/releases/tag/3.0.0)**

Compatibility
-------------

This extension requires the Contao Open Source CMS at version 4.4.0 or higher and works in all modern browsers (Internet Explorer 8+).

License
-------

This extension is licensed under the MIT license. See the complete license in the following directory:

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/sebastiande/contao-full-background-images/issues).

When reporting a bug, it may be a good idea to reproduce it in the demo project
built using the [Contao Open Source CMS](https://github.com/contao/core)
to allow developers of the extension to reproduce the issue by simply cloning it
and following some steps.
